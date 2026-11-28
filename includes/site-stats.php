<?php
/**
 * Built-in website analytics: drawlead.com counts its own visits, so Admin →
 * Analytics has a board that works with nothing to set up.
 *
 * assets/pulse.js sends one small beacon per page view to api/pulse.php, and
 * one more with the seconds the page was in view when the visitor leaves.
 * site_stats_record() stores a site_hits row per page view. No cookies are
 * set for this and no IP address is stored: the visitor and visit ids are
 * random strings the browser keeps in localStorage. The country is worked out
 * from the browser's time zone (or language), not from the IP.
 *
 * dash_site() turns the rows into the popup's shape (see analytics-dash.php).
 * The table creates itself on first use. Rows older than 400 days are pruned.
 */

// days and hours are counted in the business's own time zone
const SITE_STATS_TZ = 'Asia/Kolkata';
const SITE_STATS_KEEP_DAYS = 400;

/** SQL for the site_hits table, also in _dev/sql/schema.sql. */
function site_stats_table_sql(): string
{
    return "CREATE TABLE IF NOT EXISTS site_hits (
        id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        pv_id          CHAR(16)     NOT NULL,
        visitor_id     CHAR(16)     NOT NULL,
        session_id     CHAR(16)     NOT NULL,
        is_new_visitor TINYINT(1)   NOT NULL DEFAULT 0,
        is_entry       TINYINT(1)   NOT NULL DEFAULT 0,
        created_at     DATETIME     NOT NULL,
        day            DATE         NOT NULL,
        path           VARCHAR(300) NOT NULL DEFAULT '',
        title          VARCHAR(200) NOT NULL DEFAULT '',
        referrer_host  VARCHAR(190) NOT NULL DEFAULT '',
        channel        VARCHAR(60)  NOT NULL DEFAULT '',
        utm_source     VARCHAR(100) NOT NULL DEFAULT '',
        utm_medium     VARCHAR(100) NOT NULL DEFAULT '',
        utm_campaign   VARCHAR(100) NOT NULL DEFAULT '',
        device         VARCHAR(10)  NOT NULL DEFAULT '',
        browser        VARCHAR(30)  NOT NULL DEFAULT '',
        os             VARCHAR(30)  NOT NULL DEFAULT '',
        country        CHAR(2)      NOT NULL DEFAULT '',
        seconds        INT UNSIGNED NOT NULL DEFAULT 0,
        UNIQUE KEY uniq_pv (pv_id),
        KEY idx_day (day),
        KEY idx_created (created_at),
        KEY idx_session (session_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

/** Run $fn; if the site_hits table does not exist yet, create it and run $fn again. */
function site_stats_with_table(PDO $pdo, callable $fn)
{
    try {
        return $fn();
    } catch (PDOException $e) {
        if ($e->getCode() !== '42S02') { // base table not found
            throw $e;
        }
        $pdo->exec(site_stats_table_sql());
        return $fn();
    }
}

function site_stats_now(): DateTimeImmutable
{
    return new DateTimeImmutable('now', new DateTimeZone(SITE_STATS_TZ));
}

/** [device, browser, os] from the user agent. */
function site_stats_ua(string $ua): array
{
    $device = preg_match('/iPad|Tablet|PlayBook|Silk|(Android(?!.*Mobile))/i', $ua) ? 'Tablet'
        : (preg_match('/Mobi|iPhone|iPod|Android|BlackBerry|Opera Mini|IEMobile/i', $ua) ? 'Mobile' : 'Desktop');
    $browser = match (true) {
        (bool) preg_match('/Edg(e|A|iOS)?\//', $ua)        => 'Edge',
        (bool) preg_match('/OPR\/|Opera/', $ua)            => 'Opera',
        (bool) preg_match('/SamsungBrowser/', $ua)         => 'Samsung Internet',
        (bool) preg_match('/FBAN|FBAV|Instagram/', $ua)    => 'Facebook / Instagram app',
        (bool) preg_match('/LinkedInApp/', $ua)            => 'LinkedIn app',
        (bool) preg_match('/Firefox|FxiOS/', $ua)          => 'Firefox',
        (bool) preg_match('/Chrome|CriOS/', $ua)           => 'Chrome',
        (bool) preg_match('/Safari/', $ua)                 => 'Safari',
        default                                            => 'Other',
    };
    $os = match (true) {
        (bool) preg_match('/iPhone|iPad|iPod/', $ua) => 'iOS',
        (bool) preg_match('/Android/', $ua)          => 'Android',
        (bool) preg_match('/Windows/', $ua)          => 'Windows',
        (bool) preg_match('/Mac OS X|Macintosh/', $ua) => 'macOS',
        (bool) preg_match('/CrOS/', $ua)             => 'ChromeOS',
        (bool) preg_match('/Linux/', $ua)            => 'Linux',
        default                                      => 'Other',
    };
    return [$device, $browser, $os];
}

/**
 * Two-letter country code: from a CDN header when the host adds one,
 * otherwise from the browser's time zone, otherwise from its language
 * (en-IN → IN). '' when none of them says.
 */
function site_stats_country(string $tz, string $lang): string
{
    foreach (['HTTP_CF_IPCOUNTRY', 'HTTP_X_COUNTRY_CODE', 'GEOIP_COUNTRY_CODE'] as $h) {
        $c = strtoupper((string) ($_SERVER[$h] ?? ''));
        if (preg_match('/^[A-Z]{2}$/', $c) && $c !== 'XX') {
            return $c;
        }
    }
    // old zone names browsers still report, which PHP no longer places
    static $old = [
        'Asia/Calcutta' => 'IN', 'Asia/Saigon' => 'VN', 'Asia/Katmandu' => 'NP', 'Asia/Rangoon' => 'MM',
        'Asia/Dacca' => 'BD', 'Asia/Thimbu' => 'BT', 'Asia/Ujung_Pandang' => 'ID', 'Europe/Kiev' => 'UA',
        'America/Buenos_Aires' => 'AR', 'Atlantic/Faeroe' => 'FO', 'Asia/Istanbul' => 'TR', 'Europe/Nicosia' => 'CY',
    ];
    if (isset($old[$tz])) {
        return $old[$tz];
    }
    if (preg_match('#^[A-Za-z_]+/[A-Za-z_/\-]+$#', $tz)) {
        try {
            $loc = (new DateTimeZone($tz))->getLocation();
            $c = is_array($loc) ? (string) ($loc['country_code'] ?? '') : '';
            if (preg_match('/^[A-Z]{2}$/', $c)) {
                return $c;
            }
        } catch (Throwable $e) {
            // not a zone PHP knows
        }
        $prefix = ['US/' => 'US', 'Canada/' => 'CA', 'Australia/' => 'AU', 'Brazil/' => 'BR', 'Mexico/' => 'MX'];
        foreach ($prefix as $p => $c) {
            if (str_starts_with($tz, $p)) {
                return $c;
            }
        }
    }
    return preg_match('/^[a-z]{2,3}[-_]([A-Z]{2})\b/i', $lang, $m) ? strtoupper($m[1]) : '';
}

/**
 * Store one beacon from assets/pulse.js. $d is the decoded JSON: a page view
 * {pv, v, s, n, e, p, t, r, tz, l, utm_*, click} or a time update {pv, sec}.
 * Bots, the site's own logged-in users and anything malformed are ignored.
 */
function site_stats_record(PDO $pdo, $d): void
{
    if (!is_array($d)) {
        return;
    }
    $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
    if ($ua === '' || preg_match('/bot|crawl|spider|slurp|preview|facebookexternalhit|headless|lighthouse|pagespeed|gtmetrix|pingdom|uptime|monitor|curl|wget|python|axios|node-fetch|scrapy|httpclient|java\//i', $ua)) {
        return;
    }
    $origin = (string) ($_SERVER['HTTP_ORIGIN'] ?? '');
    $host = strtolower(preg_replace('/:\d+$/', '', (string) ($_SERVER['HTTP_HOST'] ?? '')));
    if ($origin !== '' && strtolower((string) parse_url($origin, PHP_URL_HOST)) !== $host) {
        return; // sent from another site
    }
    $id = fn ($v): string => is_string($v) && preg_match('/^[a-f0-9]{16}$/', $v) ? $v : '';
    $pv = $id($d['pv'] ?? null);
    if ($pv === '') {
        return;
    }

    // the seconds a page was in view, sent when the visitor leaves it
    if (isset($d['sec'])) {
        $sec = max(0, min(1800, (int) $d['sec']));
        site_stats_with_table($pdo, fn () => $pdo->prepare('UPDATE site_hits SET seconds = ? WHERE pv_id = ? AND seconds < ?')->execute([$sec, $pv, $sec]));
        return;
    }

    $visitor = $id($d['v'] ?? null);
    $session = $id($d['s'] ?? null);
    $path = lead_source_str($d['p'] ?? '', 300);
    if ($visitor === '' || $session === '' || $path === '' || $path[0] !== '/') {
        return;
    }
    $entry = !empty($d['e']);
    $channel = '';
    $refHost = '';
    $utm = ['', '', ''];
    if ($entry) {
        // where the visit came from, worked out the same way as Lead Source
        $touch = lead_source_clean_touch([
            'landing' => $path, 'referrer' => $d['r'] ?? '', 'click_id' => $d['click'] ?? '',
            'utm_source' => $d['utm_source'] ?? '', 'utm_medium' => $d['utm_medium'] ?? '', 'utm_campaign' => $d['utm_campaign'] ?? '',
        ]);
        [$channel] = lead_source_classify($touch);
        $channel = $channel !== '' ? $channel : 'Direct';
        $refHost = mb_substr(lead_source_host($touch['referrer']), 0, 190);
        $utm = [mb_substr($touch['utm_source'], 0, 100), mb_substr($touch['utm_medium'], 0, 100), mb_substr($touch['utm_campaign'], 0, 100)];
    }
    [$device, $browser, $os] = site_stats_ua($ua);
    $now = site_stats_now();
    $row = [
        $pv, $visitor, $session, empty($d['n']) ? 0 : 1, $entry ? 1 : 0, $now->format('Y-m-d H:i:s'), $now->format('Y-m-d'),
        $path, lead_source_str($d['t'] ?? '', 200), $refHost, $channel, $utm[0], $utm[1], $utm[2],
        $device, $browser, $os, site_stats_country(lead_source_str($d['tz'] ?? '', 60), lead_source_str($d['l'] ?? '', 20)),
    ];
    try {
        site_stats_with_table($pdo, fn () => $pdo->prepare(
            'INSERT INTO site_hits (pv_id, visitor_id, session_id, is_new_visitor, is_entry, created_at, day,
              path, title, referrer_host, channel, utm_source, utm_medium, utm_campaign, device, browser, os, country)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute($row));
    } catch (PDOException $e) {
        if ($e->getCode() !== '23000') { // 23000: the same page view sent twice
            throw $e;
        }
    }
    if (random_int(1, 500) === 1) {
        $cutoff = $now->modify('-' . SITE_STATS_KEEP_DAYS . ' days')->format('Y-m-d');
        $pdo->prepare('DELETE FROM site_hits WHERE day < ? LIMIT 5000')->execute([$cutoff]);
    }
}

/** Today's visitors and page views, for the line on the Analytics card. */
function site_stats_today(PDO $pdo): array
{
    try {
        $day = site_stats_now()->format('Y-m-d');
        return site_stats_with_table($pdo, function () use ($pdo, $day): array {
            $st = $pdo->prepare('SELECT COUNT(DISTINCT visitor_id) AS v, COUNT(*) AS p FROM site_hits WHERE day = ?');
            $st->execute([$day]);
            $r = $st->fetch() ?: [];
            $first = $pdo->query('SELECT MIN(day) FROM site_hits')->fetchColumn();
            return ['visitors' => (int) ($r['v'] ?? 0), 'views' => (int) ($r['p'] ?? 0), 'since' => $first ? (string) $first : ''];
        });
    } catch (Throwable $e) {
        error_log('site_stats_today: ' . $e->getMessage());
        return ['visitors' => 0, 'views' => 0, 'since' => ''];
    }
}

// ── The board ──

/**
 * Totals for one range: visitors, new visitors, visits, views, bounces,
 * seconds. $until (Y-m-d H:i:s) cuts the range off at that moment, so today
 * so far can be compared with yesterday up to the same time.
 */
function site_stats_totals(PDO $pdo, string $from, string $to, string $until = '9999-12-31 23:59:59'): array
{
    $st = $pdo->prepare('SELECT COUNT(DISTINCT visitor_id) AS visitors, COUNT(DISTINCT CASE WHEN is_new_visitor = 1 THEN visitor_id END) AS new_visitors
                         FROM site_hits WHERE day BETWEEN ? AND ? AND created_at <= ?');
    $st->execute([$from, $to, $until]);
    $a = $st->fetch() ?: [];
    $st = $pdo->prepare('SELECT COUNT(*) AS visits, COALESCE(SUM(pv), 0) AS views, COALESCE(SUM(CASE WHEN pv = 1 THEN 1 ELSE 0 END), 0) AS bounces, COALESCE(SUM(secs), 0) AS secs
                         FROM (SELECT session_id, COUNT(*) AS pv, SUM(seconds) AS secs FROM site_hits WHERE day BETWEEN ? AND ? AND created_at <= ? GROUP BY session_id) s');
    $st->execute([$from, $to, $until]);
    $b = $st->fetch() ?: [];
    $visits = (float) ($b['visits'] ?? 0);
    return [
        'visitors' => (float) ($a['visitors'] ?? 0),
        'new'      => (float) ($a['new_visitors'] ?? 0),
        'visits'   => $visits,
        'views'    => (float) ($b['views'] ?? 0),
        'ppv'      => $visits > 0 ? (float) $b['views'] / $visits : null,
        'duration' => $visits > 0 ? (float) $b['secs'] / $visits : null,
        'bounce'   => $visits > 0 ? (float) $b['bounces'] / $visits : null,
    ];
}

/** A ranked table from one GROUP BY query: [[label, values...], ...]. */
function site_stats_rows(PDO $pdo, string $sql, array $args): array
{
    $st = $pdo->prepare($sql);
    $st->execute($args);
    return array_map(fn (array $r): array => array_map(fn ($v) => is_numeric($v) ? (float) $v : (string) $v, array_values($r)), $st->fetchAll(PDO::FETCH_ASSOC));
}

function dash_site(PDO $pdo, int $days): array
{
    return site_stats_with_table($pdo, fn (): array => dash_site_build($pdo, $days));
}

function dash_site_build(PDO $pdo, int $days): array
{
    $now = site_stats_now();
    $end = $now->setTime(0, 0);
    $start = $end->modify('-' . ($days - 1) . ' days');
    $prevEnd = $start->modify('-1 day');
    $prevStart = $prevEnd->modify('-' . ($days - 1) . ' days');
    [$s, $e, $ps, $pe] = [$start->format('Y-m-d'), $end->format('Y-m-d'), $prevStart->format('Y-m-d'), $prevEnd->format('Y-m-d')];
    $first = (string) ($pdo->query('SELECT MIN(day) FROM site_hits')->fetchColumn() ?: '');

    $cur = site_stats_totals($pdo, $s, $e);
    // compare only with a period that was counted in full; a partly counted one reads as a huge jump
    $prev = $first !== '' && $first <= $ps
        ? ($days === 1 ? site_stats_totals($pdo, $ps, $pe, $now->modify('-1 day')->format('Y-m-d H:i:s')) : site_stats_totals($pdo, $ps, $pe))
        : null;
    $p = fn (string $k): ?float => $prev === null ? null : ($prev[$k] ?? 0.0);
    $kpis = [
        dash_kpi('visitors', 'Visitors', $cur['visitors'], $p('visitors'), 'int'),
        dash_kpi('visits', 'Visits', $cur['visits'], $p('visits'), 'int'),
        dash_kpi('views', 'Page views', $cur['views'], $p('views'), 'int'),
        dash_kpi('new', 'New visitors', $cur['new'], $p('new'), 'int'),
        dash_kpi('ppv', 'Pages per visit', $cur['ppv'], $p('ppv'), 'dec2'),
        dash_kpi('duration', 'Avg. visit duration', $cur['duration'], $p('duration'), 'dur'),
        dash_kpi('bounce', 'Bounce rate', $cur['bounce'], $p('bounce'), 'pct', 'down'),
    ];

    // the trend: by hour for today, by day otherwise
    $series = ['dates' => [], 'labels' => [], 'tips' => [], 'metrics' => ['visitors' => [], 'visits' => [], 'views' => []]];
    if ($days === 1) {
        $rows = [];
        $st = $pdo->prepare('SELECT created_at, visitor_id, session_id FROM site_hits WHERE day = ?');
        $st->execute([$s]);
        foreach ($st->fetchAll() as $r) {
            $h = (int) substr((string) $r['created_at'], 11, 2);
            $rows[$h]['v'][$r['visitor_id']] = 1;
            $rows[$h]['s'][$r['session_id']] = 1;
            $rows[$h]['p'] = ($rows[$h]['p'] ?? 0) + 1;
        }
        for ($h = 0, $last = (int) $now->format('G'); $h <= $last; $h++) {
            $label = date('g A', mktime($h, 0, 0));
            $series['dates'][] = $s;
            $series['labels'][] = $label;
            $series['tips'][] = $label . ' – ' . date('g A', mktime($h + 1, 0, 0)) . ', today';
            $series['metrics']['visitors'][] = (float) count($rows[$h]['v'] ?? []);
            $series['metrics']['visits'][] = (float) count($rows[$h]['s'] ?? []);
            $series['metrics']['views'][] = (float) ($rows[$h]['p'] ?? 0);
        }
    } else {
        $byDay = [];
        foreach (site_stats_rows($pdo, 'SELECT day, COUNT(DISTINCT visitor_id), COUNT(DISTINCT session_id), COUNT(*) FROM site_hits WHERE day BETWEEN ? AND ? GROUP BY day', [$s, $e]) as $r) {
            $byDay[substr((string) $r[0], 0, 10)] = $r;
        }
        foreach (dash_day_list($s, $e) as $d) {
            $series['dates'][] = $d;
            $series['metrics']['visitors'][] = (float) ($byDay[$d][1] ?? 0);
            $series['metrics']['visits'][] = (float) ($byDay[$d][2] ?? 0);
            $series['metrics']['views'][] = (float) ($byDay[$d][3] ?? 0);
        }
        unset($series['labels'], $series['tips']);
    }

    $range = [$s, $e];
    $panels = [
        dash_panel('Pages', [['Page', 'text'], ['Views', 'int'], ['Visitors', 'int'], ['Avg. time on page', 'dur']],
            site_stats_rows($pdo, 'SELECT path, COUNT(*) AS views, COUNT(DISTINCT visitor_id) AS visitors, COALESCE(AVG(NULLIF(seconds, 0)), 0) AS secs
                                   FROM site_hits WHERE day BETWEEN ? AND ? GROUP BY path ORDER BY views DESC LIMIT 15', $range), true),
        dash_panel('Traffic sources', [['Source', 'text'], ['Visits', 'int'], ['Visitors', 'int']],
            site_stats_rows($pdo, "SELECT channel, COUNT(*) AS visits, COUNT(DISTINCT visitor_id) AS visitors
                                   FROM site_hits WHERE day BETWEEN ? AND ? AND is_entry = 1 GROUP BY channel ORDER BY visits DESC LIMIT 12", $range)),
        dash_panel('Referring sites', [['Site', 'text'], ['Visits', 'int']],
            site_stats_rows($pdo, "SELECT referrer_host, COUNT(*) AS visits FROM site_hits
                                   WHERE day BETWEEN ? AND ? AND is_entry = 1 AND referrer_host <> '' GROUP BY referrer_host ORDER BY visits DESC LIMIT 10", $range)),
        dash_panel('Landing pages', [['Page', 'text'], ['Visits', 'int']],
            site_stats_rows($pdo, 'SELECT path, COUNT(*) AS visits FROM site_hits WHERE day BETWEEN ? AND ? AND is_entry = 1 GROUP BY path ORDER BY visits DESC LIMIT 10', $range)),
        dash_panel('Countries', [['Country', 'country'], ['Visitors', 'int']],
            site_stats_rows($pdo, "SELECT country, COUNT(DISTINCT visitor_id) AS visitors FROM site_hits WHERE day BETWEEN ? AND ? GROUP BY country ORDER BY visitors DESC LIMIT 10", $range)),
        dash_panel('Devices', [['Device', 'text'], ['Visitors', 'int']],
            site_stats_rows($pdo, 'SELECT device, COUNT(DISTINCT visitor_id) AS visitors FROM site_hits WHERE day BETWEEN ? AND ? GROUP BY device ORDER BY visitors DESC', $range)),
        dash_panel('Browsers', [['Browser', 'text'], ['Visitors', 'int']],
            site_stats_rows($pdo, 'SELECT browser, COUNT(DISTINCT visitor_id) AS visitors FROM site_hits WHERE day BETWEEN ? AND ? GROUP BY browser ORDER BY visitors DESC LIMIT 8', $range)),
        dash_panel('Operating systems', [['OS', 'text'], ['Visitors', 'int']],
            site_stats_rows($pdo, 'SELECT os, COUNT(DISTINCT visitor_id) AS visitors FROM site_hits WHERE day BETWEEN ? AND ? GROUP BY os ORDER BY visitors DESC LIMIT 8', $range)),
    ];
    $campaigns = array_map(fn (array $r): array => [($r[0] !== '' ? $r[0] . ' / ' : '') . $r[1], $r[2]], site_stats_rows($pdo,
        "SELECT utm_source, utm_campaign, COUNT(*) AS visits FROM site_hits
         WHERE day BETWEEN ? AND ? AND is_entry = 1 AND utm_campaign <> '' GROUP BY utm_source, utm_campaign ORDER BY visits DESC LIMIT 10", $range));
    if ($campaigns) {
        array_splice($panels, 3, 0, [dash_panel('Campaigns', [['Source / campaign', 'text'], ['Visits', 'int']], $campaigns)]);
    }

    // right now: anyone with a page view in the last 30 minutes
    $since = $now->modify('-30 minutes')->format('Y-m-d H:i:s');
    $st = $pdo->prepare('SELECT COUNT(DISTINCT visitor_id) FROM site_hits WHERE created_at >= ?');
    $st->execute([$since]);
    $liveCount = (float) $st->fetchColumn();
    $livePages = site_stats_rows($pdo, 'SELECT path, COUNT(DISTINCT visitor_id) AS n FROM site_hits WHERE created_at >= ? GROUP BY path ORDER BY n DESC LIMIT 4', [$since]);

    if ($first === '') {
        $note = 'No visits counted yet. Visits appear here a few seconds after someone opens drawlead.com (your own visits while logged in to the admin are not counted).';
    } elseif ($first > $s) {
        $note = 'Drawlead started counting visits on ' . date('M j, Y', strtotime($first)) . ', so the days before that show zero.';
    } else {
        $note = '';
    }

    return [
        'title'    => 'Drawlead website',
        'subtitle' => 'Built-in visit counter',
        'period'   => $days === 1 ? 'Today, ' . $end->format('M j, Y') : dash_period_text($s, $e),
        'compare'  => $prev !== null ? ($days === 1 ? 'yesterday up to this time' : dash_period_text($ps, $pe)) : '',
        'kpis'     => $kpis,
        'series'   => $series,
        'panels'   => $panels,
        'live'     => ['value' => $liveCount, 'label' => 'visitors in the last 30 minutes', 'rows' => $livePages],
        'links'    => [],
        'info'     => $note,
    ];
}
