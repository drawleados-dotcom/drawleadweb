<?php
/**
 * Drawlead OS connection — sends every website lead to the OS, where it lands
 * in Sales Department → Pre-sales → Lead with source "Website/Popup",
 * "Website/Calendar" or "Website/WhatsApp" (see os_leads_sub_source()).
 *
 * Admin → Lead Source → "Drawlead OS connection" saves the OS endpoint URL and
 * the API key generated in OS → Sales Department → Website. From then on each
 * calendar booking and WhatsApp chat is sent right after the visitor gets
 * their confirmation (os_leads_push_after_response). The leads already on the
 * site, and any lead that couldn't be sent because the OS was down, are sent
 * automatically in the background (see AUTOMATIC CATCH-UP) — nobody has to
 * click "Send all leads to OS". The OS adds a lead only once per external_id
 * ("booking-12", "whatsapp-5"), so sending one again is harmless.
 *
 * Nothing here may break or slow a visitor's form: every failure is caught,
 * logged, and shown on the Lead Source page instead.
 */

const OS_LEADS_URL_SETTING = 'os_leads_endpoint';
const OS_LEADS_KEY_SETTING = 'os_leads_api_key';
const OS_LEADS_STATUS_SETTING = 'os_leads_status'; // JSON: last_ok_at, last_error, last_error_at

// ── AUTOMATIC CATCH-UP ──
// Every lead on the site is sent again (os_leads_push_all) when the OS is
// behind: right after connecting, after OS_LEADS_SYNC_VERSION is bumped, or
// after a lead couldn't be sent because the OS was unreachable or erroring.
// It runs after a page has gone out to its visitor (shared hosting has no
// cron), one run at a time, and at most every OS_LEADS_RETRY_MINUTES.

/** Bump to have every lead already in the OS sent again, e.g. 2 = sub_source ("Website/Popup"…). */
const OS_LEADS_SYNC_VERSION = 2;
const OS_LEADS_RETRY_MINUTES = 10;
const OS_LEADS_SYNC_LOCK = 'drawlead_os_leads_sync'; // MySQL GET_LOCK name
const OS_LEADS_SYNCED_VERSION_SETTING = 'os_leads_synced_version'; // version of the last complete run
const OS_LEADS_SYNCED_AT_SETTING = 'os_leads_synced_at';           // when that run started
const OS_LEADS_UNSENT_AT_SETTING = 'os_leads_unsent_at';           // last time a lead couldn't be sent
const OS_LEADS_SYNC_TRIED_AT_SETTING = 'os_leads_sync_tried_at';   // last run started (complete or not)

/** ['url' => ..., 'key' => ...] as saved on the Lead Source page ('' when not set). */
function os_leads_config(PDO $pdo): array
{
    return [
        'url' => get_setting($pdo, OS_LEADS_URL_SETTING),
        'key' => get_setting($pdo, OS_LEADS_KEY_SETTING),
    ];
}

function os_leads_connected(PDO $pdo): bool
{
    $c = os_leads_config($pdo);
    return $c['url'] !== '' && $c['key'] !== '';
}

/**
 * Last success / last error, for the status line on the Lead Source page.
 * Read straight from the table, not get_setting(): that caches per request,
 * and "Send all leads" updates this many times in one request.
 */
function os_leads_status(PDO $pdo): array
{
    $stmt = $pdo->prepare('SELECT `value` FROM settings WHERE `key` = ?');
    $stmt->execute([OS_LEADS_STATUS_SETTING]);
    $s = json_decode((string) $stmt->fetchColumn(), true);
    return array_merge(['last_ok_at' => '', 'last_error' => '', 'last_error_at' => ''], is_array($s) ? $s : []);
}

function os_leads_record(PDO $pdo, bool $ok, string $error = ''): void
{
    try {
        $s = os_leads_status($pdo);
        if ($ok) {
            $s['last_ok_at'] = date('Y-m-d H:i:s');
            $s['last_error'] = '';
        } else {
            $s['last_error'] = mb_substr($error, 0, 300);
            $s['last_error_at'] = date('Y-m-d H:i:s');
        }
        set_setting($pdo, OS_LEADS_STATUS_SETTING, json_encode($s));
    } catch (Throwable $e) {
        error_log('os_leads_record: ' . $e->getMessage());
    }
}

/**
 * One call to the OS endpoint: GET tests the key, POST sends a lead.
 * Returns [http status, decoded reply or null, error text or '']. The status is
 * 0 when nothing answered, and -1 when something answered that isn't the OS
 * lead API (e.g. the URL is a web page).
 */
function os_leads_request(string $method, string $url, string $key, ?array $body = null): array
{
    if (!function_exists('curl_init')) {
        return [0, null, 'PHP cURL is not available on this server.'];
    }
    $headers = ['Accept: application/json', 'X-API-Key: ' . $key];
    $ch = curl_init($url);
    $opts = [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 12,
        CURLOPT_FOLLOWLOCATION => false,
    ];
    if ($body !== null) {
        $opts[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        $headers[] = 'Content-Type: application/json';
    }
    $opts[CURLOPT_HTTPHEADER] = $headers;
    curl_setopt_array($ch, $opts);
    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = $raw === false ? curl_error($ch) : '';
    unset($ch); // frees the handle (curl_close() has done nothing since PHP 8 and is deprecated)

    $data = is_string($raw) ? json_decode($raw, true) : null;
    $data = is_array($data) ? $data : null;
    if ($curlError !== '') {
        return [0, null, "Couldn't reach the OS: $curlError"];
    }
    if ($status !== 200) {
        $detail = $data['detail'] ?? null;
        $detail = is_string($detail) ? $detail : ($detail !== null ? json_encode($detail) : '');
        if ($status === 401) {
            return [$status, $data, 'The OS rejected the API key' . ($detail ? " ($detail)" : '') . '. Paste a current key from OS → Sales Department → Website.'];
        }
        if ($status === 404 || $status === 405) {
            return [$status, $data, "The endpoint URL isn't the OS lead API (HTTP $status). Copy it again from OS → Sales Department → Website."];
        }
        return [$status, $data, "The OS replied HTTP $status" . ($detail ? ": $detail" : '.')];
    }
    if ($data === null) {
        return [-1, null, "The endpoint URL didn't answer like the OS lead API. Copy it again from OS → Sales Department → Website."];
    }
    return [$status, $data, ''];
}

/** Test a URL + key without creating a lead. Returns [ok, message]. */
function os_leads_test(string $url, string $key): array
{
    [, $data, $err] = os_leads_request('GET', $url, $key);
    if ($err !== '') {
        return [false, $err];
    }
    if (empty($data['success'])) {
        return [false, "The endpoint URL didn't answer like the OS lead API."];
    }
    return [true, (string) ($data['message'] ?? 'Connected to Drawlead OS.')];
}

/** Booking form field labels, phone-type keys and the name/email keys (looked up once per request). */
function os_leads_booking_fields(PDO $pdo): array
{
    static $fields = null;
    if ($fields !== null) {
        return $fields;
    }
    $fields = ['labels' => [], 'phone' => [], 'skip' => ['name', 'email']];
    if (migration_table_exists($pdo, 'booking_form_fields')) {
        foreach ($pdo->query('SELECT field_key, label, field_type, field_role FROM booking_form_fields') as $f) {
            $fields['labels'][$f['field_key']] = $f['label'];
            if ($f['field_type'] === 'phone') {
                $fields['phone'][] = $f['field_key'];
            }
            if (in_array($f['field_role'], ['name', 'email'], true)) {
                $fields['skip'][] = $f['field_key'];
            }
        }
    }
    return $fields;
}

/**
 * Which website form a lead came through, sent to the OS as `sub_source`: the
 * OS shows it after the key's source, e.g. "Website/Popup". A booking counts
 * as "Popup" when the site popup opened the calendar (booking.js labels that
 * trigger "Popup: <name>"), and as "Calendar" from anywhere else — menu,
 * buttons, footer, the /book link, or before the trigger was recorded.
 */
function os_leads_sub_source(string $type, string $trigger): string
{
    if ($type === 'whatsapp') {
        return 'WhatsApp';
    }
    return str_starts_with($trigger, 'Popup:') ? 'Popup' : 'Calendar';
}

/**
 * The OS payload for one lead, or null if it no longer exists. Contact details
 * go into the OS lead's own fields; the form, source, call time and answers
 * are written into its Notes so the sales team sees them on the lead.
 */
function os_leads_payload(PDO $pdo, string $type, int $id): ?array
{
    if ($type === 'booking') {
        $stmt = $pdo->prepare('SELECT id, created_at, name, email, form_data, booking_date, booking_time, status FROM bookings WHERE id = ?');
    } elseif ($type === 'whatsapp') {
        $stmt = $pdo->prepare('SELECT id, created_at, phone, answers FROM whatsapp_leads WHERE id = ?');
    } else {
        return null;
    }
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    $src = null;
    if (migration_table_exists($pdo, 'lead_sources')) {
        $s = $pdo->prepare('SELECT channel, source_detail, page_url, page_title, utm_campaign, trigger_label FROM lead_sources WHERE lead_type = ? AND lead_id = ?');
        $s->execute([$type, $id]);
        $src = $s->fetch() ?: null;
    }

    $name = '';
    $email = '';
    $phone = '';
    $answers = [];
    if ($type === 'booking') {
        $name = trim((string) $row['name']);
        $email = trim((string) $row['email']);
        $bf = os_leads_booking_fields($pdo);
        foreach (json_decode((string) $row['form_data'], true) ?: [] as $key => $val) {
            $val = is_array($val) ? implode(', ', array_map('strval', $val)) : trim((string) $val);
            if ($val === '' || in_array($key, $bf['skip'], true)) {
                continue;
            }
            if ($phone === '' && in_array($key, $bf['phone'], true)) {
                $phone = $val;
                continue;
            }
            $answers[] = [$bf['labels'][$key] ?? $key, $val];
        }
    } else {
        $phone = trim((string) $row['phone']);
        foreach (json_decode((string) $row['answers'], true) ?: [] as $a) {
            if (is_array($a) && trim((string) ($a['answer'] ?? '')) !== '') {
                $answers[] = [trim((string) ($a['question'] ?? '')), trim((string) $a['answer'])];
            }
        }
    }

    $form = $type === 'booking' ? 'Calendar booking' : 'WhatsApp chat';
    $notes = ["Website lead: $form"];
    if ($type === 'booking' && $row['booking_date']) {
        $notes[] = 'Call booked for ' . date('D, M j, Y', strtotime($row['booking_date']))
            . ' at ' . date('g:i A', strtotime($row['booking_time'])) . ' (IST)'
            . ($row['status'] === 'cancelled' ? ' (cancelled)' : '');
    }
    if ($src) {
        $notes[] = 'Came from: ' . $src['channel'] . ($src['source_detail'] !== '' ? ' (' . $src['source_detail'] . ')' : '');
        if ($src['utm_campaign'] !== '') {
            $notes[] = 'Campaign: ' . $src['utm_campaign'];
        }
        if ($src['page_url'] !== '') {
            $notes[] = 'Sent from page: ' . ($src['page_title'] !== '' ? $src['page_title'] . ' (' . $src['page_url'] . ')' : $src['page_url']);
        }
        if ($type === 'booking' && $src['trigger_label'] !== '') {
            $notes[] = 'Opened from: ' . $src['trigger_label'];
        }
    }
    if ($answers) {
        $notes[] = '';
        foreach ($answers as [$q, $a]) {
            $notes[] = ($q !== '' ? "$q: " : '') . $a;
        }
    }

    return [
        'external_id'      => $type . '-' . $id,
        'name'             => $name,
        'email'            => $email,
        'phone'            => $phone,
        'notes'            => implode("\n", $notes),
        'date_of_lead'     => date('Y-m-d', strtotime((string) $row['created_at'])),
        'sub_source'       => os_leads_sub_source($type, (string) ($src['trigger_label'] ?? '')),
        'website_form'     => $form,
        'website_channel'  => $src['channel'] ?? '',
        'website_campaign' => $src['utm_campaign'] ?? '',
        'website_page'     => $src['page_url'] ?? '',
    ];
}

/**
 * Send one lead to the OS. Returns ['ok' => bool, 'duplicate' => bool, 'status' => http, 'error' => text].
 * A lead with nothing to identify it (no name, phone or email) is skipped as ok.
 */
function os_leads_push(PDO $pdo, string $type, int $id): array
{
    $cfg = os_leads_config($pdo);
    if ($cfg['url'] === '' || $cfg['key'] === '') {
        return ['ok' => false, 'duplicate' => false, 'status' => 0, 'error' => 'Not connected to the OS.'];
    }
    $payload = os_leads_payload($pdo, $type, $id);
    // status null = decided here, without asking the OS (so "Send all" carries on)
    if (!$payload) {
        return ['ok' => false, 'duplicate' => false, 'status' => null, 'error' => "Lead $type #$id not found."];
    }
    if ($payload['name'] === '' && $payload['phone'] === '' && $payload['email'] === '') {
        return ['ok' => true, 'duplicate' => false, 'status' => null, 'error' => '', 'skipped' => true];
    }
    [$status, $data, $err] = os_leads_request('POST', $cfg['url'], $cfg['key'], $payload);
    $ok = $err === '' && !empty($data['success']);
    if (!$ok && $err === '') {
        $err = 'The OS did not confirm the lead.';
    }
    os_leads_record($pdo, $ok, $ok ? '' : ($type === 'booking' ? 'Booking' : 'WhatsApp lead') . " #$id: $err");
    if (!$ok && os_leads_worth_retrying($status)) {
        set_setting($pdo, OS_LEADS_UNSENT_AT_SETTING, date('Y-m-d H:i:s')); // the catch-up sends it later
    }
    return ['ok' => $ok, 'duplicate' => !empty($data['duplicate']), 'status' => $status, 'error' => $err];
}

/**
 * A failure that may pass on its own (OS unreachable, down or overloaded), so
 * the catch-up tries again. A rejected key or a wrong URL needs the admin to
 * fix the connection, and saving it sends everything again anyway.
 */
function os_leads_worth_retrying(?int $status): bool
{
    return $status === 0 || $status === -1 || $status === 429 || $status >= 500;
}

/**
 * Send what's been echoed so far to the visitor now and keep running in the
 * background. False when the server can't do that, as the work would then
 * hold the visitor's page or confirmation up.
 */
function os_leads_finish_response(): bool
{
    ignore_user_abort(true);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close(); // don't hold the visitor's session lock while we wait on the OS
    }
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
        return true;
    }
    if (function_exists('litespeed_finish_request')) {
        litespeed_finish_request();
        return true;
    }
    return false;
}

/**
 * For the public form endpoints: finish the visitor's response first, then
 * send the lead, so a slow or unreachable OS never holds up the confirmation.
 * Call it after the JSON reply has been echoed.
 */
function os_leads_push_after_response(PDO $pdo, string $type, int $id): void
{
    try {
        if (!os_leads_connected($pdo)) {
            return;
        }
        $finished = os_leads_finish_response();
        $r = os_leads_push($pdo, $type, $id);
        if (!$r['ok']) {
            error_log("os_leads_push $type #$id: " . $r['error']);
        }
        if ($finished && os_leads_sync_state($pdo)['due']) {
            os_leads_sync_all($pdo, true);
        }
    } catch (Throwable $e) {
        error_log('os_leads_push_after_response: ' . $e->getMessage());
    }
}

/**
 * Settings read straight from the table, not get_setting(): that caches per
 * request, and these change while a request runs.
 */
function os_leads_settings(PDO $pdo, array $keys): array
{
    $stmt = $pdo->prepare('SELECT `key`, `value` FROM settings WHERE `key` IN (' . implode(',', array_fill(0, count($keys), '?')) . ')');
    $stmt->execute($keys);
    return array_merge(array_fill_keys($keys, ''), array_map('strval', $stmt->fetchAll(PDO::FETCH_KEY_PAIR)));
}

/**
 * Where the automatic catch-up stands: 'behind' = the OS may be missing leads
 * or have them in an older form; 'due' = behind and time for another try;
 * 'synced_at' = start of the last complete run ('' if none yet).
 */
function os_leads_sync_state(PDO $pdo): array
{
    $s = os_leads_settings($pdo, [
        OS_LEADS_URL_SETTING, OS_LEADS_KEY_SETTING, OS_LEADS_SYNCED_VERSION_SETTING,
        OS_LEADS_SYNCED_AT_SETTING, OS_LEADS_UNSENT_AT_SETTING, OS_LEADS_SYNC_TRIED_AT_SETTING,
    ]);
    $connected = $s[OS_LEADS_URL_SETTING] !== '' && $s[OS_LEADS_KEY_SETTING] !== '';
    $syncedAt = $s[OS_LEADS_SYNCED_AT_SETTING];
    $behind = $connected && (
        (int) $s[OS_LEADS_SYNCED_VERSION_SETTING] < OS_LEADS_SYNC_VERSION
        || ($s[OS_LEADS_UNSENT_AT_SETTING] !== '' && $s[OS_LEADS_UNSENT_AT_SETTING] >= $syncedAt)
    );
    $triedAt = $s[OS_LEADS_SYNC_TRIED_AT_SETTING];
    $due = $behind && ($triedAt === '' || strtotime($triedAt) <= time() - OS_LEADS_RETRY_MINUTES * 60);
    return ['behind' => $behind, 'due' => $due, 'synced_at' => $syncedAt];
}

/** Have the catch-up send every lead again soon — e.g. after the connection is saved. */
function os_leads_sync_reset(PDO $pdo): void
{
    set_setting($pdo, OS_LEADS_SYNCED_VERSION_SETTING, '0');
    set_setting($pdo, OS_LEADS_SYNC_TRIED_AT_SETTING, '');
}

/**
 * os_leads_push_all(), remembering a complete run so the catch-up doesn't
 * repeat it. One run at a time across requests: returns null when another is
 * going, or (with $onlyIfDue) when it's no longer due — another request just did it.
 */
function os_leads_sync_all(PDO $pdo, bool $onlyIfDue = false): ?array
{
    // Named lock: released below, or by MySQL if this request dies.
    if ((int) $pdo->query("SELECT GET_LOCK('" . OS_LEADS_SYNC_LOCK . "', 0)")->fetchColumn() !== 1) {
        return null;
    }
    try {
        if ($onlyIfDue && !os_leads_sync_state($pdo)['due']) {
            return null;
        }
        $start = date('Y-m-d H:i:s');
        set_setting($pdo, OS_LEADS_SYNC_TRIED_AT_SETTING, $start);
        $r = os_leads_push_all($pdo);
        if (!$r['stopped'] && $r['retry'] === 0) {
            set_setting($pdo, OS_LEADS_SYNCED_VERSION_SETTING, (string) OS_LEADS_SYNC_VERSION);
            set_setting($pdo, OS_LEADS_SYNCED_AT_SETTING, $start);
        }
        return $r;
    } finally {
        $pdo->query("SELECT RELEASE_LOCK('" . OS_LEADS_SYNC_LOCK . "')");
    }
}

/**
 * The catch-up hook for page loads: register_shutdown_function() it. Costs
 * one settings query when the OS is up to date; otherwise sends the page to
 * the visitor first, then every lead to the OS.
 */
function os_leads_catch_up_after_response(PDO $pdo): void
{
    try {
        if (os_leads_sync_state($pdo)['due'] && os_leads_finish_response()) {
            os_leads_sync_all($pdo, true);
        }
    } catch (Throwable $e) {
        error_log('os_leads_catch_up_after_response: ' . $e->getMessage());
    }
}

/**
 * Send every booking and WhatsApp lead on the site (oldest first). Stops at
 * the first error that would fail every remaining lead the same way (OS
 * unreachable, key rejected, wrong URL). Returns counts for the page message;
 * 'retry' counts the failures worth trying again later.
 */
function os_leads_push_all(PDO $pdo): array
{
    @set_time_limit(600);
    $result = ['added' => 0, 'already' => 0, 'skipped' => 0, 'failed' => 0, 'retry' => 0, 'error' => '', 'stopped' => false];
    $todo = [];
    if (migration_table_exists($pdo, 'bookings')) {
        foreach ($pdo->query('SELECT id, created_at FROM bookings') as $r) {
            $todo[] = [$r['created_at'], 'booking', (int) $r['id']];
        }
    }
    if (migration_table_exists($pdo, 'whatsapp_leads')) {
        foreach ($pdo->query('SELECT id, created_at FROM whatsapp_leads') as $r) {
            $todo[] = [$r['created_at'], 'whatsapp', (int) $r['id']];
        }
    }
    sort($todo); // oldest first, so the OS list ends with the newest on top

    foreach ($todo as [, $type, $id]) {
        $r = os_leads_push($pdo, $type, $id);
        if (!empty($r['skipped'])) {
            $result['skipped']++;
        } elseif ($r['ok']) {
            $result[$r['duplicate'] ? 'already' : 'added']++;
        } else {
            $result['failed']++;
            $result['retry'] += os_leads_worth_retrying($r['status']) ? 1 : 0;
            $result['error'] = $result['error'] ?: $r['error'];
            if (in_array($r['status'], [-1, 0, 401, 404, 405], true)) {
                $result['stopped'] = true;
                break;
            }
        }
    }
    return $result;
}
