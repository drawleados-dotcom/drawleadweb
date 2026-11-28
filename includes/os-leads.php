<?php
/**
 * Drawlead OS connection — sends every website lead to the OS, where it lands
 * in Sales Department → Pre-sales → Lead with source "Website".
 *
 * Admin → Lead Source → "Drawlead OS connection" saves the OS endpoint URL and
 * the API key generated in OS → Sales Department → Website. From then on each
 * calendar booking and WhatsApp chat is sent right after the visitor gets
 * their confirmation (os_leads_push_after_response), and "Send all leads to
 * OS" sends every lead already on the site. The OS adds a lead only once per
 * external_id ("booking-12", "whatsapp-5"), so sending one again is harmless.
 *
 * Nothing here may break or slow a visitor's form: every failure is caught,
 * logged, and shown on the Lead Source page instead.
 */

const OS_LEADS_URL_SETTING = 'os_leads_endpoint';
const OS_LEADS_KEY_SETTING = 'os_leads_api_key';
const OS_LEADS_STATUS_SETTING = 'os_leads_status'; // JSON: last_ok_at, last_error, last_error_at

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
        $s = $pdo->prepare('SELECT channel, source_detail, page_url, page_title, utm_campaign FROM lead_sources WHERE lead_type = ? AND lead_id = ?');
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
    return ['ok' => $ok, 'duplicate' => !empty($data['duplicate']), 'status' => $status, 'error' => $err];
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
        ignore_user_abort(true);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close(); // don't hold the visitor's session lock while we wait on the OS
        }
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
        }
        $r = os_leads_push($pdo, $type, $id);
        if (!$r['ok']) {
            error_log("os_leads_push $type #$id: " . $r['error']);
        }
    } catch (Throwable $e) {
        error_log('os_leads_push_after_response: ' . $e->getMessage());
    }
}

/**
 * Send every booking and WhatsApp lead on the site (oldest first). Stops at
 * the first error that would fail every remaining lead the same way (OS
 * unreachable, key rejected, wrong URL). Returns counts for the page message.
 */
function os_leads_push_all(PDO $pdo): array
{
    @set_time_limit(600);
    $result = ['added' => 0, 'already' => 0, 'skipped' => 0, 'failed' => 0, 'error' => '', 'stopped' => false];
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
            $result['error'] = $result['error'] ?: $r['error'];
            if (in_array($r['status'], [-1, 0, 401, 404, 405], true)) {
                $result['stopped'] = true;
                break;
            }
        }
    }
    return $result;
}
