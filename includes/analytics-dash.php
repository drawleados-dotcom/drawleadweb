<?php
/**
 * Analytics dashboards for admin/analytics.php.
 *
 * Reads Google Analytics 4, Google Search Console and Microsoft Clarity through
 * their APIs and turns each answer into the one shape that the popup
 * (admin/assets/analytics-dash.js) draws:
 *
 *   kpis    headline numbers, each with the previous period when the API has one
 *   series  one value per day for the KPIs that have a trend chart
 *   panels  ranked tables (top pages, sources, countries ...)
 *   live    an optional "right now" figure (GA realtime)
 *
 * Access: GA4 and Search Console are read with a Google service account. Its
 * JSON key is saved in settings, and its email is added as a Viewer on the GA4
 * property and as a user on the Search Console property. Clarity is read with a
 * Data Export API token. Answers are cached in the settings table, which also
 * keeps Clarity under its limit of 10 API calls per project per day.
 */

const DASH_GOOGLE_TOKEN_URL = 'https://oauth2.googleapis.com/token';
const DASH_GOOGLE_SCOPES = 'https://www.googleapis.com/auth/analytics.readonly https://www.googleapis.com/auth/webmasters.readonly';
const DASH_RANGES = [
    'ga'      => [7 => 'Last 7 days', 28 => 'Last 28 days', 90 => 'Last 90 days'],
    'gsc'     => [7 => 'Last 7 days', 28 => 'Last 28 days', 90 => 'Last 3 months'],
    'clarity' => [1 => 'Last 24 hours', 2 => 'Last 2 days', 3 => 'Last 3 days'],
];
const DASH_DEFAULT_RANGE = ['ga' => 28, 'gsc' => 28, 'clarity' => 3];
// seconds a saved answer is used before asking the API again
const DASH_TTL = ['ga' => 300, 'gsc' => 3600, 'clarity' => 8 * 3600];
const DASH_CLARITY_DAILY_LIMIT = 10;

/**
 * A problem worth showing in the popup. $setup names the dashboard whose setup
 * steps should be shown with it ('ga', 'gsc' or 'clarity'), or '' when the
 * setup is fine and the problem is elsewhere (network, rate limit ...).
 */
class DashError extends RuntimeException
{
    public string $setup;

    public function __construct(string $message, string $setup = '')
    {
        parent::__construct($message);
        $this->setup = $setup;
    }
}

// ── Settings ──

/** The saved service account key, or null. */
function dash_service_account(PDO $pdo): ?array
{
    $raw = get_setting($pdo, 'google_service_account', '');
    $sa = $raw !== '' ? json_decode($raw, true) : null;
    return is_array($sa) && !empty($sa['client_email']) && !empty($sa['private_key']) ? $sa : null;
}

/**
 * Check a pasted service account key file. Returns [key to save, ''] or
 * [null, what is wrong]. Only the fields needed to sign in are kept.
 */
function dash_parse_service_account(string $json): array
{
    $sa = json_decode($json, true);
    if (!is_array($sa)) {
        return [null, 'The service account key is not valid JSON. Paste the whole downloaded key file, from { to }.'];
    }
    if (($sa['type'] ?? '') !== 'service_account' || empty($sa['client_email']) || empty($sa['private_key'])) {
        return [null, 'That JSON is not a service account key. In Google Cloud, open the service account → Keys → Add key → Create new key → JSON, and paste that file.'];
    }
    if (!function_exists('openssl_pkey_get_private') || !openssl_pkey_get_private((string) $sa['private_key'])) {
        return [null, 'The private key inside that file could not be read. Create a new JSON key and paste it again.'];
    }
    return [[
        'type'           => 'service_account',
        'project_id'     => (string) ($sa['project_id'] ?? ''),
        'private_key_id' => (string) ($sa['private_key_id'] ?? ''),
        'private_key'    => (string) $sa['private_key'],
        'client_email'   => (string) $sa['client_email'],
    ], ''];
}

/** Forget saved answers for one dashboard ('ga', 'gsc', 'clarity') or all (''). */
function dash_cache_clear(PDO $pdo, string $source = ''): void
{
    $pdo->prepare('DELETE FROM settings WHERE `key` LIKE ?')->execute(['dash_cache_' . $source . '%']);
}

function dash_cache_get(PDO $pdo, string $key): ?array
{
    $c = json_decode(get_setting($pdo, 'dash_cache_' . $key, ''), true);
    return is_array($c) && isset($c['at'], $c['data']) && is_array($c['data']) ? $c : null;
}

function dash_cache_put(PDO $pdo, string $key, array $data): void
{
    $json = json_encode(['at' => time(), 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    // settings.value is a TEXT column (64 KB); an answer too big for it is just not cached
    if ($json !== false && strlen($json) < 60000) {
        set_setting($pdo, 'dash_cache_' . $key, $json);
    }
}

// ── HTTP ──

/**
 * Run several HTTP requests at the same time. Each request is
 * [method, url, headers, body or null]. Returns, in the same order,
 * ['status' => HTTP status (0 when nothing answered), 'data' => decoded JSON
 * or null, 'error' => cURL error or ''].
 */
function dash_http(array $requests): array
{
    if (!function_exists('curl_multi_init')) {
        throw new DashError('PHP cURL is not available on this server, so the dashboards cannot reach the analytics services.');
    }
    $mh = curl_multi_init();
    $handles = [];
    foreach ($requests as $i => [$method, $url, $headers, $body]) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_ENCODING       => '',
            CURLOPT_HTTPHEADER     => array_merge(['Accept: application/json'], $headers),
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        curl_multi_add_handle($mh, $ch);
        $handles[$i] = $ch;
    }
    do {
        $status = curl_multi_exec($mh, $running);
        if ($running) {
            curl_multi_select($mh, 1.0);
        }
    } while ($running && $status === CURLM_OK);

    $failed = [];
    while ($info = curl_multi_info_read($mh)) {
        if ($info['result'] !== CURLE_OK) {
            $failed[spl_object_id($info['handle'])] = curl_strerror($info['result']);
        }
    }
    $out = [];
    foreach ($handles as $i => $ch) {
        $raw = curl_multi_getcontent($ch);
        $data = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
        $error = $failed[spl_object_id($ch)] ?? '';
        $out[$i] = [
            'status' => $error !== '' ? 0 : (int) curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'data'   => is_array($data) ? $data : null,
            'error'  => $error,
        ];
        curl_multi_remove_handle($mh, $ch);
    }
    unset($handles, $mh); // frees the handles (curl_close() is deprecated)
    return $out;
}

/** A readable sentence for a failed Google API call. */
function dash_google_error(array $res, string $service): string
{
    if ($res['status'] === 0) {
        return "Couldn't reach $service" . ($res['error'] !== '' ? " ({$res['error']})" : '') . '. Try again in a minute.';
    }
    $msg = trim((string) ($res['data']['error']['message'] ?? ''));
    return "$service replied HTTP {$res['status']}" . ($msg !== '' ? ": $msg" : '.');
}

// ── Google sign-in (service account → access token) ──

function dash_b64url(string $bytes): string
{
    return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
}

/** An access token for the GA4 Data API and the Search Console API. */
function dash_google_token(PDO $pdo, string $setup): string
{
    $sa = dash_service_account($pdo);
    if (!$sa) {
        throw new DashError('No Google service account key is saved yet.', $setup);
    }
    $cached = json_decode(get_setting($pdo, 'dash_google_token', ''), true);
    if (is_array($cached) && ($cached['email'] ?? '') === $sa['client_email'] && (int) ($cached['exp'] ?? 0) > time() + 60) {
        return (string) $cached['token'];
    }

    $now = time();
    $head = ['alg' => 'RS256', 'typ' => 'JWT'];
    if ($sa['private_key_id'] !== '') {
        $head['kid'] = $sa['private_key_id'];
    }
    $claims = ['iss' => $sa['client_email'], 'scope' => DASH_GOOGLE_SCOPES, 'aud' => DASH_GOOGLE_TOKEN_URL, 'iat' => $now, 'exp' => $now + 3600];
    $input = dash_b64url(json_encode($head)) . '.' . dash_b64url(json_encode($claims, JSON_UNESCAPED_SLASHES));
    if (!openssl_sign($input, $signature, $sa['private_key'], OPENSSL_ALGO_SHA256)) {
        throw new DashError('The saved service account key could not sign in. Paste a new JSON key.', $setup);
    }
    [$res] = dash_http([[
        'POST', DASH_GOOGLE_TOKEN_URL, ['Content-Type: application/x-www-form-urlencoded'],
        http_build_query(['grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => $input . '.' . dash_b64url($signature)]),
    ]]);
    $token = (string) ($res['data']['access_token'] ?? '');
    if ($res['status'] !== 200 || $token === '') {
        if ($res['status'] === 0) {
            throw new DashError(dash_google_error($res, 'Google sign-in'));
        }
        $why = (string) ($res['data']['error_description'] ?? $res['data']['error'] ?? "HTTP {$res['status']}");
        throw new DashError("Google did not accept the service account key ($why). The key may have been deleted in Google Cloud; create a new JSON key and paste it again.", $setup);
    }
    set_setting($pdo, 'dash_google_token', json_encode(['email' => $sa['client_email'], 'token' => $token, 'exp' => $now + (int) ($res['data']['expires_in'] ?? 3600)]));
    return $token;
}

// ── Shared helpers ──

/**
 * The days of a range ending $endOffset days ago, and the same number of days
 * before it: [start, end, previous start, previous end] as Y-m-d.
 */
function dash_dates(int $days, int $endOffset = 1): array
{
    $end = new DateTimeImmutable("today -$endOffset day");
    $start = $end->modify('-' . ($days - 1) . ' days');
    $prevEnd = $start->modify('-1 day');
    $prevStart = $prevEnd->modify('-' . ($days - 1) . ' days');
    return [$start->format('Y-m-d'), $end->format('Y-m-d'), $prevStart->format('Y-m-d'), $prevEnd->format('Y-m-d')];
}

/** Every Y-m-d from $start to $end. */
function dash_day_list(string $start, string $end): array
{
    $out = [];
    for ($d = new DateTimeImmutable($start), $last = new DateTimeImmutable($end); $d <= $last; $d = $d->modify('+1 day')) {
        $out[] = $d->format('Y-m-d');
    }
    return $out;
}

function dash_period_text(string $start, string $end): string
{
    $s = new DateTimeImmutable($start);
    $e = new DateTimeImmutable($end);
    if ($start === $end) {
        return $e->format('M j, Y');
    }
    return $s->format($s->format('Y') === $e->format('Y') ? 'M j' : 'M j, Y') . ' – ' . $e->format('M j, Y');
}

/** One KPI tile. $better is 'up', 'down' or '' (neither). */
function dash_kpi(string $id, string $label, ?float $value, ?float $prev, string $fmt, string $better = 'up', bool $invert = false): array
{
    return ['id' => $id, 'label' => $label, 'value' => $value, 'prev' => $prev, 'fmt' => $fmt, 'better' => $better, 'invert' => $invert];
}

/** One ranked table. $cols is [[heading, format], ...]; column 0 is the label. */
function dash_panel(string $title, array $cols, array $rows, bool $wide = false, string $note = ''): array
{
    return ['title' => $title, 'cols' => $cols, 'rows' => $rows, 'bar' => 1, 'wide' => $wide, 'note' => $note];
}

/** "https://drawlead.com/services/" → "/services/" when it is this site. */
function dash_short_url(string $url, string $host): string
{
    $p = parse_url($url);
    $h = strtolower(preg_replace('/^www\./', '', $p['host'] ?? ''));
    if ($h !== '' && $h === $host) {
        return ($p['path'] ?? '/') . (isset($p['query']) ? '?' . $p['query'] : '');
    }
    return $url;
}

function dash_site_host(): string
{
    return strtolower(preg_replace('/^www\./', '', preg_replace('/:\d+$/', '', (string) ($_SERVER['HTTP_HOST'] ?? ''))));
}

// ── The dashboards ──

/**
 * The popup's data for one dashboard and range, from the cache when it is
 * fresh enough. A failed call falls back to the last saved answer, with a note.
 */
function dash_report(PDO $pdo, string $source, int $days, bool $refresh): array
{
    if (!isset(DASH_RANGES[$source])) {
        throw new DashError('Unknown dashboard.');
    }
    if (!isset(DASH_RANGES[$source][$days])) {
        $days = DASH_DEFAULT_RANGE[$source];
    }
    $key = $source . '_' . $days;
    $cached = dash_cache_get($pdo, $key);
    $age = $cached ? time() - (int) $cached['at'] : PHP_INT_MAX;
    $stale = $age >= DASH_TTL[$source];
    $note = '';

    if ($cached && $source === 'clarity' && ($stale || $refresh)) {
        // every Clarity call counts against its 10-a-day limit: refresh at most
        // every 15 minutes, and keep a few calls back for the other ranges
        $left = DASH_CLARITY_DAILY_LIMIT - dash_clarity_calls_today($pdo);
        if ($left <= 0 || ($refresh && !$stale && ($age < 900 || $left <= 3))) {
            $refresh = false;
            $stale = false;
            $note = $left <= 0
                ? "Clarity allows 10 data requests a day and today's are used up, so these are the last saved numbers."
                : 'Clarity allows 10 data requests a day, so these numbers refresh at most every 15 minutes.';
        }
    }
    if ($cached && !$stale && !$refresh) {
        return dash_with_meta($cached['data'], $source, $days, (int) $cached['at'], $note);
    }

    try {
        $data = match ($source) {
            'ga'      => dash_ga($pdo, $days),
            'gsc'     => dash_gsc($pdo, $days),
            'clarity' => dash_clarity($pdo, $days),
        };
    } catch (DashError $e) {
        if ($cached) {
            return dash_with_meta($cached['data'], $source, $days, (int) $cached['at'], 'Showing the last saved numbers. ' . $e->getMessage());
        }
        throw $e;
    }
    dash_cache_put($pdo, $key, $data);
    return dash_with_meta($data, $source, $days, time(), $note);
}

function dash_with_meta(array $data, string $source, int $days, int $at, string $note): array
{
    $ranges = [];
    foreach (DASH_RANGES[$source] as $d => $label) {
        $ranges[] = ['days' => $d, 'label' => $label];
    }
    return ['ok' => true, 'source' => $source, 'days' => $days, 'ranges' => $ranges, 'fetched_at' => $at, 'note' => $note] + $data;
}

// ── Google Analytics 4 ──

function dash_ga(PDO $pdo, int $days): array
{
    $prop = preg_replace('/\D/', '', get_setting($pdo, 'ga_property_id', ''));
    if ($prop === '' && !dash_service_account($pdo)) {
        throw new DashError('Google Analytics needs its Property ID and a Google service account key before it can show numbers here.', 'ga');
    }
    if ($prop === '') {
        throw new DashError('Add the GA4 Property ID (a number, from Admin → Property details) to show the numbers here.', 'ga');
    }
    $token = dash_google_token($pdo, 'ga');
    [$s, $e, $ps, $pe] = dash_dates($days);
    $cur = [['startDate' => $s, 'endDate' => $e]];
    $names = fn (string ...$n): array => array_map(fn (string $x): array => ['name' => $x], $n);
    $top = fn (string $dim, array $metrics, int $limit = 10): array => [
        'dateRanges' => $cur, 'dimensions' => $names($dim), 'metrics' => $names(...$metrics),
        'orderBys' => [['metric' => ['metricName' => $metrics[0]], 'desc' => true]], 'limit' => $limit,
    ];
    $kpiDefs = [
        'activeUsers'            => ['Active users', 'int', 'up'],
        'newUsers'               => ['New users', 'int', 'up'],
        'sessions'               => ['Sessions', 'int', 'up'],
        'screenPageViews'        => ['Views', 'int', 'up'],
        'engagementRate'         => ['Engagement rate', 'pct', 'up'],
        'averageSessionDuration' => ['Avg. session duration', 'dur', 'up'],
        'bounceRate'             => ['Bounce rate', 'pct', 'down'],
        'eventCount'             => ['Events', 'int', 'up'],
    ];
    $kpiNames = array_keys($kpiDefs);
    $batch1 = ['requests' => [
        ['dateRanges' => [['startDate' => $s, 'endDate' => $e, 'name' => 'cur'], ['startDate' => $ps, 'endDate' => $pe, 'name' => 'prev']], 'metrics' => $names(...$kpiNames)],
        ['dateRanges' => $cur, 'dimensions' => $names('date'), 'metrics' => $names(...$kpiNames), 'orderBys' => [['dimension' => ['dimensionName' => 'date']]], 'limit' => 400],
        $top('pagePath', ['screenPageViews', 'activeUsers', 'userEngagementDuration'], 15),
        $top('sessionDefaultChannelGroup', ['sessions', 'activeUsers', 'engagementRate']),
        $top('sessionSourceMedium', ['sessions', 'activeUsers']),
    ]];
    $batch2 = ['requests' => [
        $top('country', ['activeUsers', 'sessions']),
        $top('city', ['activeUsers']),
        $top('deviceCategory', ['activeUsers', 'sessions']),
        $top('browser', ['activeUsers']),
        $top('eventName', ['eventCount', 'totalUsers']),
    ]];
    $base = 'https://analyticsdata.googleapis.com/v1beta/properties/' . $prop;
    $hdr = ['Authorization: Bearer ' . $token, 'Content-Type: application/json'];
    $enc = fn (array $b): string => json_encode($b, JSON_UNESCAPED_SLASHES);
    [$r1, $r2, $rt1, $rt2] = dash_http([
        ['POST', $base . ':batchRunReports', $hdr, $enc($batch1)],
        ['POST', $base . ':batchRunReports', $hdr, $enc($batch2)],
        ['POST', $base . ':runRealtimeReport', $hdr, $enc(['metrics' => $names('activeUsers')])],
        ['POST', $base . ':runRealtimeReport', $hdr, $enc(['dimensions' => $names('unifiedScreenName'), 'metrics' => $names('activeUsers'), 'orderBys' => [['metric' => ['metricName' => 'activeUsers'], 'desc' => true]], 'limit' => 5])],
    ]);
    if ($r1['status'] !== 200) {
        throw new DashError(dash_google_error($r1, 'Google Analytics'), in_array($r1['status'], [400, 401, 403, 404], true) ? 'ga' : '');
    }
    $rep = $r1['data']['reports'] ?? [];
    $rep2 = $r2['status'] === 200 ? ($r2['data']['reports'] ?? []) : [];

    // headline numbers, this period and the one before
    $byRange = [];
    foreach (dash_ga_rows($rep[0] ?? null) as [$dims, $vals]) {
        $byRange[$dims[0] ?? 'cur'] = $vals;
    }
    $kpis = [];
    foreach ($kpiNames as $i => $id) {
        [$label, $fmt, $better] = $kpiDefs[$id];
        $kpis[] = dash_kpi($id, $label, (float) ($byRange['cur'][$i] ?? 0), isset($byRange['prev']) ? (float) ($byRange['prev'][$i] ?? 0) : 0.0, $fmt, $better);
    }

    // one value per day; GA leaves out days with no data
    $dates = dash_day_list($s, $e);
    $byDay = [];
    foreach (dash_ga_rows($rep[1] ?? null) as [$dims, $vals]) {
        $d = $dims[0] ?? '';
        if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', $d, $m)) {
            $byDay["$m[1]-$m[2]-$m[3]"] = $vals;
        }
    }
    // a day with no visits has no rate or duration, not a zero one
    $series = ['dates' => $dates, 'metrics' => []];
    foreach ($kpiNames as $i => $id) {
        $isCount = $kpiDefs[$id][1] === 'int';
        $series['metrics'][$id] = array_map(fn (string $d): ?float => isset($byDay[$d][$i]) ? (float) $byDay[$d][$i] : ($isCount ? 0.0 : null), $dates);
    }

    $rows = fn (?array $report, callable $map): array => array_map($map, dash_ga_rows($report));
    $panels = [];
    $panels[] = dash_panel('Pages', [['Page path', 'text'], ['Views', 'int'], ['Active users', 'int'], ['Avg. engagement time', 'dur']],
        $rows($rep[2] ?? null, fn (array $r): array => [$r[0][0], $r[1][0], $r[1][1], $r[1][1] > 0 ? $r[1][2] / $r[1][1] : 0]), true);
    $panels[] = dash_panel('Traffic channels', [['Channel', 'text'], ['Sessions', 'int'], ['Active users', 'int'], ['Engagement rate', 'pct']],
        $rows($rep[3] ?? null, fn (array $r): array => [$r[0][0], $r[1][0], $r[1][1], $r[1][2]]));
    $panels[] = dash_panel('Source / medium', [['Source / medium', 'text'], ['Sessions', 'int'], ['Active users', 'int']],
        $rows($rep[4] ?? null, fn (array $r): array => [$r[0][0], $r[1][0], $r[1][1]]));
    if ($rep2) {
        $panels[] = dash_panel('Countries', [['Country', 'text'], ['Active users', 'int'], ['Sessions', 'int']],
            $rows($rep2[0] ?? null, fn (array $r): array => [$r[0][0], $r[1][0], $r[1][1]]));
        $panels[] = dash_panel('Cities', [['City', 'text'], ['Active users', 'int']],
            $rows($rep2[1] ?? null, fn (array $r): array => [$r[0][0], $r[1][0]]));
        $panels[] = dash_panel('Devices', [['Device', 'text'], ['Active users', 'int'], ['Sessions', 'int']],
            $rows($rep2[2] ?? null, fn (array $r): array => [ucfirst($r[0][0]), $r[1][0], $r[1][1]]));
        $panels[] = dash_panel('Browsers', [['Browser', 'text'], ['Active users', 'int']],
            $rows($rep2[3] ?? null, fn (array $r): array => [$r[0][0], $r[1][0]]));
        $panels[] = dash_panel('Events', [['Event name', 'text'], ['Event count', 'int'], ['Total users', 'int']],
            $rows($rep2[4] ?? null, fn (array $r): array => [$r[0][0], $r[1][0], $r[1][1]]));
    }

    $live = null;
    if ($rt1['status'] === 200) {
        $liveRows = dash_ga_rows($rt1['data']);
        $live = [
            'value' => (float) ($liveRows[0][1][0] ?? 0),
            'label' => 'active users in the last 30 minutes',
            'rows'  => $rt2['status'] === 200 ? array_map(fn (array $r): array => [$r[0][0], $r[1][0]], dash_ga_rows($rt2['data'])) : [],
        ];
    }

    return [
        'title'    => 'Google Analytics',
        'subtitle' => 'GA4 property ' . $prop,
        'period'   => dash_period_text($s, $e),
        'compare'  => dash_period_text($ps, $pe),
        'kpis'     => $kpis,
        'series'   => $series,
        'panels'   => $panels,
        'live'     => $live,
        'links'    => [['label' => 'Open Google Analytics', 'url' => 'https://analytics.google.com/analytics/web/#/p' . $prop . '/reports/intelligenthome']],
    ];
}

/** GA report rows as [[dimension values], [metric values as floats]]. */
function dash_ga_rows(?array $report): array
{
    $out = [];
    foreach ($report['rows'] ?? [] as $r) {
        $out[] = [
            array_map(fn ($v): string => (string) ($v['value'] ?? ''), $r['dimensionValues'] ?? []),
            array_map(fn ($v): float => (float) ($v['value'] ?? 0), $r['metricValues'] ?? []),
        ];
    }
    return $out;
}

// ── Google Search Console ──

/** The Search Console property to read: the saved one, or the best match for this site. */
function dash_gsc_site(PDO $pdo, string $token): string
{
    $site = trim(get_setting($pdo, 'gsc_site_url', ''));
    if ($site !== '') {
        return $site;
    }
    [$res] = dash_http([['GET', 'https://www.googleapis.com/webmasters/v3/sites', ['Authorization: Bearer ' . $token], null]]);
    if ($res['status'] !== 200) {
        throw new DashError(dash_google_error($res, 'Search Console'), in_array($res['status'], [401, 403], true) ? 'gsc' : '');
    }
    $host = dash_site_host();
    $best = '';
    $bestScore = -1;
    $entries = 0;
    foreach ($res['data']['siteEntry'] ?? [] as $entry) {
        $url = (string) ($entry['siteUrl'] ?? '');
        if ($url === '' || ($entry['permissionLevel'] ?? '') === 'siteUnverifiedUser') {
            continue;
        }
        $h = str_starts_with($url, 'sc-domain:') ? substr($url, 10) : (string) parse_url($url, PHP_URL_HOST);
        $h = strtolower(preg_replace('/^www\./', '', $h));
        $score = $h !== $host ? 0 : (str_starts_with($url, 'sc-domain:') ? 3 : (str_starts_with($url, 'https://') ? 2 : 1));
        $entries++;
        if ($score > $bestScore) {
            [$best, $bestScore] = [$url, $score];
        }
    }
    // a property for another site is only used when it is the only one there is
    if ($best !== '' && $bestScore === 0 && $entries > 1) {
        throw new DashError("The service account can see several Search Console properties and none of them is for $host. Enter the one to show in “Property” in the Google Search Console card.", 'gsc');
    }
    if ($best === '') {
        $email = dash_service_account($pdo)['client_email'] ?? 'the service account';
        throw new DashError("The service account can't see any Search Console property yet. In Search Console → Settings → Users and permissions, add $email as a user.", 'gsc');
    }
    return $best;
}

function dash_gsc(PDO $pdo, int $days): array
{
    $token = dash_google_token($pdo, 'gsc');
    $site = dash_gsc_site($pdo, $token);
    // Search Console data arrives a day or two late; dataState "all" includes the fresh, still-settling days
    [$s, $e, $ps, $pe] = dash_dates($days);
    $url = 'https://www.googleapis.com/webmasters/v3/sites/' . rawurlencode($site) . '/searchAnalytics/query';
    $hdr = ['Authorization: Bearer ' . $token, 'Content-Type: application/json'];
    $q = function (array $dims, int $limit, string $from, string $to) use ($url, $hdr): array {
        $body = ['startDate' => $from, 'endDate' => $to, 'rowLimit' => $limit, 'dataState' => 'all'];
        if ($dims) {
            $body['dimensions'] = $dims;
        }
        return ['POST', $url, $hdr, json_encode($body)];
    };
    $res = dash_http([
        $q([], 1, $s, $e),
        $q([], 1, $ps, $pe),
        $q(['date'], 500, $s, $e),
        $q(['query'], 25, $s, $e),
        $q(['page'], 25, $s, $e),
        $q(['country'], 10, $s, $e),
        $q(['device'], 5, $s, $e),
        ['GET', 'https://www.googleapis.com/webmasters/v3/sites/' . rawurlencode($site) . '/sitemaps', ['Authorization: Bearer ' . $token], null],
    ]);
    if ($res[0]['status'] !== 200) {
        throw new DashError(dash_google_error($res[0], 'Search Console'), in_array($res[0]['status'], [400, 401, 403, 404], true) ? 'gsc' : '');
    }
    $rows = fn (int $i): array => $res[$i]['status'] === 200 ? ($res[$i]['data']['rows'] ?? []) : [];
    $tot = $rows(0)[0] ?? [];
    $prev = $res[1]['status'] === 200 ? ($rows(1)[0] ?? []) : null;
    $num = fn (?array $r, string $k): ?float => $r !== null && isset($r[$k]) ? (float) $r[$k] : null;
    $hasData = $tot !== [];
    $kpis = [
        dash_kpi('clicks', 'Total clicks', $num($tot, 'clicks') ?? 0.0, $prev !== null ? ($num($prev, 'clicks') ?? 0.0) : null, 'int'),
        dash_kpi('impressions', 'Total impressions', $num($tot, 'impressions') ?? 0.0, $prev !== null ? ($num($prev, 'impressions') ?? 0.0) : null, 'int'),
        dash_kpi('ctr', 'Average CTR', $hasData ? $num($tot, 'ctr') : null, $prev ? $num($prev, 'ctr') : null, 'pct'),
        dash_kpi('position', 'Average position', $hasData ? $num($tot, 'position') : null, $prev ? $num($prev, 'position') : null, 'dec1', 'down', true),
    ];

    $dates = dash_day_list($s, $e);
    $byDay = [];
    foreach ($rows(2) as $r) {
        $byDay[(string) ($r['keys'][0] ?? '')] = $r;
    }
    $series = ['dates' => $dates, 'metrics' => []];
    foreach (['clicks', 'impressions', 'ctr', 'position'] as $m) {
        // a day with no impressions has no CTR or position, not a zero one
        $series['metrics'][$m] = array_map(fn (string $d) => isset($byDay[$d][$m]) ? (float) $byDay[$d][$m] : (in_array($m, ['clicks', 'impressions'], true) ? 0.0 : null), $dates);
    }

    $host = dash_site_host();
    $perf = [['Clicks', 'int'], ['Impressions', 'int'], ['CTR', 'pct'], ['Position', 'dec1']];
    $perfRows = fn (int $i, callable $label): array => array_map(fn (array $r): array => [
        $label((string) ($r['keys'][0] ?? '')), (float) ($r['clicks'] ?? 0), (float) ($r['impressions'] ?? 0), (float) ($r['ctr'] ?? 0), (float) ($r['position'] ?? 0),
    ], $rows($i));
    $panels = [
        dash_panel('Top queries', array_merge([['Query', 'text']], $perf), $perfRows(3, fn (string $k): string => $k), true),
        dash_panel('Top pages', array_merge([['Page', 'text']], $perf), $perfRows(4, fn (string $k): string => dash_short_url($k, $host)), true),
        dash_panel('Countries', [['Country', 'text'], ['Clicks', 'int'], ['Impressions', 'int']],
            array_map(fn (array $r): array => [dash_country_name((string) ($r['keys'][0] ?? '')), (float) ($r['clicks'] ?? 0), (float) ($r['impressions'] ?? 0)], $rows(5))),
        dash_panel('Devices', [['Device', 'text'], ['Clicks', 'int'], ['Impressions', 'int'], ['CTR', 'pct']],
            array_map(fn (array $r): array => [ucfirst(strtolower((string) ($r['keys'][0] ?? ''))), (float) ($r['clicks'] ?? 0), (float) ($r['impressions'] ?? 0), (float) ($r['ctr'] ?? 0)], $rows(6))),
    ];
    if ($res[7]['status'] === 200) {
        $maps = [];
        foreach ($res[7]['data']['sitemap'] ?? [] as $sm) {
            $submitted = 0;
            foreach ($sm['contents'] ?? [] as $c) {
                $submitted += (int) ($c['submitted'] ?? 0);
            }
            $errors = (int) ($sm['errors'] ?? 0);
            $warnings = (int) ($sm['warnings'] ?? 0);
            $status = !empty($sm['isPending']) ? 'Pending'
                : ($errors || $warnings ? trim(($errors ? "$errors error" . ($errors > 1 ? 's' : '') : '') . ($errors && $warnings ? ', ' : '') . ($warnings ? "$warnings warning" . ($warnings > 1 ? 's' : '') : '')) : 'Success');
            $read = (string) ($sm['lastDownloaded'] ?? '');
            $maps[] = [dash_short_url((string) ($sm['path'] ?? ''), $host), (float) $submitted, $read !== '' ? date('M j, Y', strtotime($read)) : 'Not read yet', $status];
        }
        $panels[] = ['title' => 'Sitemaps', 'cols' => [['Sitemap', 'text'], ['Pages submitted', 'int'], ['Last read', 'text'], ['Status', 'text']], 'rows' => $maps, 'bar' => 0, 'wide' => true, 'note' => ''];
    }

    $siteLabel = str_starts_with($site, 'sc-domain:') ? substr($site, 10) . ' (domain property)' : $site;
    return [
        'title'    => 'Google Search Console',
        'subtitle' => $siteLabel . ' · Google web search',
        'period'   => dash_period_text($s, $e),
        'compare'  => dash_period_text($ps, $pe),
        'kpis'     => $kpis,
        'series'   => $series,
        'panels'   => $panels,
        'live'     => null,
        'links'    => [['label' => 'Open Search Console', 'url' => 'https://search.google.com/search-console/performance/search-analytics?resource_id=' . rawurlencode($site)]],
    ];
}

/** Search Console reports countries as ISO 3166-1 alpha-3 codes. */
function dash_country_name(string $code): string
{
    static $names = [
        'afg' => 'Afghanistan', 'alb' => 'Albania', 'dza' => 'Algeria', 'ago' => 'Angola', 'arg' => 'Argentina', 'arm' => 'Armenia',
        'aus' => 'Australia', 'aut' => 'Austria', 'aze' => 'Azerbaijan', 'bhr' => 'Bahrain', 'bgd' => 'Bangladesh', 'blr' => 'Belarus',
        'bel' => 'Belgium', 'btn' => 'Bhutan', 'bol' => 'Bolivia', 'bih' => 'Bosnia and Herzegovina', 'bwa' => 'Botswana', 'bra' => 'Brazil',
        'brn' => 'Brunei', 'bgr' => 'Bulgaria', 'khm' => 'Cambodia', 'cmr' => 'Cameroon', 'can' => 'Canada', 'chl' => 'Chile', 'chn' => 'China',
        'col' => 'Colombia', 'cri' => 'Costa Rica', 'civ' => "Côte d'Ivoire", 'hrv' => 'Croatia', 'cyp' => 'Cyprus', 'cze' => 'Czechia',
        'dnk' => 'Denmark', 'dom' => 'Dominican Republic', 'ecu' => 'Ecuador', 'egy' => 'Egypt', 'slv' => 'El Salvador', 'est' => 'Estonia',
        'eth' => 'Ethiopia', 'fin' => 'Finland', 'fra' => 'France', 'geo' => 'Georgia', 'deu' => 'Germany', 'gha' => 'Ghana', 'grc' => 'Greece',
        'gtm' => 'Guatemala', 'hnd' => 'Honduras', 'hkg' => 'Hong Kong', 'hun' => 'Hungary', 'isl' => 'Iceland', 'ind' => 'India',
        'idn' => 'Indonesia', 'irn' => 'Iran', 'irq' => 'Iraq', 'irl' => 'Ireland', 'isr' => 'Israel', 'ita' => 'Italy', 'jam' => 'Jamaica',
        'jpn' => 'Japan', 'jor' => 'Jordan', 'kaz' => 'Kazakhstan', 'ken' => 'Kenya', 'kwt' => 'Kuwait', 'kgz' => 'Kyrgyzstan', 'lao' => 'Laos',
        'lva' => 'Latvia', 'lbn' => 'Lebanon', 'ltu' => 'Lithuania', 'lux' => 'Luxembourg', 'mac' => 'Macao', 'mdg' => 'Madagascar',
        'mys' => 'Malaysia', 'mdv' => 'Maldives', 'mlt' => 'Malta', 'mus' => 'Mauritius', 'mex' => 'Mexico', 'mda' => 'Moldova',
        'mng' => 'Mongolia', 'mne' => 'Montenegro', 'mar' => 'Morocco', 'moz' => 'Mozambique', 'mmr' => 'Myanmar', 'nam' => 'Namibia',
        'npl' => 'Nepal', 'nld' => 'Netherlands', 'nzl' => 'New Zealand', 'nic' => 'Nicaragua', 'nga' => 'Nigeria', 'mkd' => 'North Macedonia',
        'nor' => 'Norway', 'omn' => 'Oman', 'pak' => 'Pakistan', 'pse' => 'Palestine', 'pan' => 'Panama', 'pry' => 'Paraguay', 'per' => 'Peru',
        'phl' => 'Philippines', 'pol' => 'Poland', 'prt' => 'Portugal', 'pri' => 'Puerto Rico', 'qat' => 'Qatar', 'rou' => 'Romania',
        'rus' => 'Russia', 'rwa' => 'Rwanda', 'sau' => 'Saudi Arabia', 'sen' => 'Senegal', 'srb' => 'Serbia', 'sgp' => 'Singapore',
        'svk' => 'Slovakia', 'svn' => 'Slovenia', 'zaf' => 'South Africa', 'kor' => 'South Korea', 'esp' => 'Spain', 'lka' => 'Sri Lanka',
        'swe' => 'Sweden', 'che' => 'Switzerland', 'twn' => 'Taiwan', 'tza' => 'Tanzania', 'tha' => 'Thailand', 'tto' => 'Trinidad and Tobago',
        'tun' => 'Tunisia', 'tur' => 'Türkiye', 'uga' => 'Uganda', 'ukr' => 'Ukraine', 'are' => 'United Arab Emirates', 'gbr' => 'United Kingdom',
        'usa' => 'United States', 'ury' => 'Uruguay', 'uzb' => 'Uzbekistan', 'ven' => 'Venezuela', 'vnm' => 'Vietnam', 'yem' => 'Yemen',
        'zmb' => 'Zambia', 'zwe' => 'Zimbabwe', 'zzz' => 'Unknown region',
    ];
    $c = strtolower($code);
    return $names[$c] ?? strtoupper($code);
}

// ── Microsoft Clarity ──

function dash_clarity_calls_today(PDO $pdo): int
{
    $c = json_decode(get_setting($pdo, 'dash_clarity_calls', ''), true);
    return is_array($c) && ($c['day'] ?? '') === gmdate('Y-m-d') ? (int) ($c['n'] ?? 0) : 0;
}

function dash_clarity(PDO $pdo, int $days): array
{
    $token = get_setting($pdo, 'clarity_api_token', '');
    if ($token === '') {
        throw new DashError('Clarity needs a Data Export API token before it can show numbers here.', 'clarity');
    }
    $calls = dash_clarity_calls_today($pdo);
    if ($calls >= DASH_CLARITY_DAILY_LIMIT) {
        throw new DashError("Clarity allows 10 data requests a day and today's are used up. The numbers will load again tomorrow (UTC).");
    }
    set_setting($pdo, 'dash_clarity_calls', json_encode(['day' => gmdate('Y-m-d'), 'n' => $calls + 1]));

    [$res] = dash_http([['GET', 'https://www.clarity.ms/export-data/api/v1/project-live-insights?numOfDays=' . $days, ['Authorization: Bearer ' . $token, 'Content-Type: application/json'], null]]);
    if ($res['status'] === 401 || $res['status'] === 403) {
        throw new DashError('Clarity did not accept the API token. Generate a new one in Clarity → Settings → Data Export and paste it again.', 'clarity');
    }
    if ($res['status'] === 429) {
        set_setting($pdo, 'dash_clarity_calls', json_encode(['day' => gmdate('Y-m-d'), 'n' => DASH_CLARITY_DAILY_LIMIT]));
        throw new DashError("Clarity allows 10 data requests a day and today's are used up. The numbers will load again tomorrow (UTC).");
    }
    if ($res['status'] === 0) {
        throw new DashError("Couldn't reach Clarity" . ($res['error'] !== '' ? " ({$res['error']})" : '') . '. Try again in a minute.');
    }
    if ($res['status'] !== 200 || !is_array($res['data']) || ($res['data'] !== [] && !isset($res['data'][0]))) {
        $msg = is_array($res['data']) ? (string) ($res['data']['message'] ?? $res['data']['error'] ?? '') : '';
        throw new DashError("Clarity replied HTTP {$res['status']}" . ($msg !== '' ? ": $msg" : '.'));
    }
    $data = dash_clarity_normalize($res['data']);
    $id = get_setting($pdo, 'clarity_project_id', '');
    $data['title'] = 'Microsoft Clarity';
    $data['subtitle'] = $id !== '' ? 'Project ' . $id : 'Clarity project';
    $data['period'] = DASH_RANGES['clarity'][$days];
    $data['compare'] = '';
    $data['links'] = $id === '' ? [] : [
        ['label' => 'Open Clarity', 'url' => 'https://clarity.microsoft.com/projects/view/' . rawurlencode($id) . '/dashboard'],
        ['label' => 'Heatmaps', 'url' => 'https://clarity.microsoft.com/projects/view/' . rawurlencode($id) . '/heatmaps'],
        ['label' => 'Recordings', 'url' => 'https://clarity.microsoft.com/projects/view/' . rawurlencode($id) . '/impressions'],
    ];
    return $data;
}

/**
 * Clarity's live insights: a list of {metricName, information: [rows]}. Rows
 * are read by field name where the name is known, and otherwise by shape (the
 * first text field is the label, the first number the value), so a renamed
 * field still shows up.
 */
function dash_clarity_normalize(array $metrics): array
{
    $byName = [];
    $titles = [];
    foreach ($metrics as $m) {
        if (is_array($m) && isset($m['metricName']) && is_array($m['information'] ?? null)) {
            $k = strtolower(preg_replace('/[^a-z]/i', '', (string) $m['metricName']));
            $byName[$k] = $m['information'];
            $titles[$k] = (string) $m['metricName'];
        }
    }
    $num = function (array $row, array $keys): ?float {
        foreach ($keys as $k) {
            foreach ($row as $rk => $v) {
                if (strcasecmp((string) $rk, $k) === 0 && is_numeric($v)) {
                    return (float) $v;
                }
            }
        }
        return null;
    };
    $first = fn (string $k): array => is_array($byName[$k][0] ?? null) ? $byName[$k][0] : [];

    $traffic = $first('traffic');
    $engage = $first('engagementtime');
    $sessions = $num($traffic, ['totalSessionCount']);
    $kpis = array_values(array_filter([
        dash_kpi('sessions', 'Sessions', $sessions, null, 'int'),
        dash_kpi('users', 'Unique users', $num($traffic, ['distinctUserCount', 'distantUserCount']), null, 'int'),
        dash_kpi('pages', 'Pages per session', $num($traffic, ['pagesPerSessionPercentage', 'pagesPerSession']), null, 'dec2'),
        dash_kpi('scroll', 'Average scroll depth', $num($first('scrolldepth'), ['averageScrollDepth']), null, 'pct100'),
        dash_kpi('active', 'Active time', $num($engage, ['activeTime']), null, 'dur'),
        dash_kpi('total', 'Total time', $num($engage, ['totalTime']), null, 'dur'),
        dash_kpi('bots', 'Bot sessions (filtered out)', $num($traffic, ['totalBotSessionCount']), null, 'int', ''),
    ], fn (array $k): bool => $k['value'] !== null));

    // the frustration and error signals, as a share of sessions
    $signals = [
        'deadclickcount' => 'Dead clicks', 'rageclickcount' => 'Rage clicks', 'errorclickcount' => 'Error clicks',
        'quickbackclick' => 'Quick backs', 'excessivescroll' => 'Excessive scrolling', 'scripterrorcount' => 'JavaScript errors',
    ];
    $signalRows = [];
    foreach ($signals as $k => $label) {
        $r = $first($k);
        if ($r) {
            $signalRows[] = [$label, $num($r, ['sessionsWithMetricPercentage']) ?? 0.0, $num($r, ['subTotal', 'sessionsCount']) ?? 0.0];
        }
    }
    $panels = [];
    if ($signalRows) {
        $panels[] = dash_panel('Behaviour signals', [['Signal', 'text'], ['Sessions with it', 'pct100'], ['Count', 'int']], $signalRows, true,
            'Share of sessions where Clarity saw each frustration or error signal. Watch the recordings in Clarity to see why.');
    }

    $known = array_merge(['traffic', 'engagementtime', 'scrolldepth'], array_keys($signals));
    $lists = [
        'popularpages' => ['Popular pages', 'Page', 'Visits', true],
        'referrerurl'  => ['Referrers', 'Referrer', 'Sessions', true],
        'pagetitle'    => ['Page titles', 'Title', 'Sessions', false],
        'device'       => ['Devices', 'Device', 'Sessions', false],
        'browser'      => ['Browsers', 'Browser', 'Sessions', false],
        'os'           => ['Operating systems', 'OS', 'Sessions', false],
        'country'      => ['Countries', 'Country', 'Sessions', false],
        'countryregion' => ['Countries', 'Country', 'Sessions', false],
    ];
    $host = dash_site_host();
    foreach ($byName as $k => $info) {
        if (in_array($k, $known, true)) {
            continue;
        }
        [$title, $labelHead, $valueHead, $wide] = $lists[$k] ?? [trim(preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $titles[$k])), 'Name', 'Sessions', false];
        $rows = [];
        foreach ($info as $r) {
            if (!is_array($r)) {
                continue;
            }
            $label = null;
            foreach ($r as $v) {
                if (is_string($v) && !is_numeric($v)) {
                    $label = $v;
                    break;
                }
            }
            $value = $num($r, ['sessionsCount', 'visitsCount', 'count', 'totalSessionCount', 'subTotal']);
            if ($value === null) {
                foreach ($r as $v) {
                    if (is_numeric($v)) {
                        $value = (float) $v;
                        break;
                    }
                }
            }
            if ($label !== null && $value !== null) {
                $label = trim($label) === '' ? ($k === 'referrerurl' ? '(direct)' : '(not set)') : ($k === 'popularpages' ? dash_short_url($label, $host) : $label);
                $rows[] = [$label, $value];
            }
        }
        if ($rows) {
            usort($rows, fn (array $a, array $b): int => $b[1] <=> $a[1]);
            $panels[] = dash_panel($title, [[$labelHead, 'text'], [$valueHead, 'int']], array_slice($rows, 0, 10), $wide);
        }
    }

    return ['kpis' => $kpis, 'series' => null, 'panels' => $panels, 'live' => null];
}
