<?php
/**
 * Admin → Lead Source: every lead from the booking calendar and the WhatsApp
 * chat in one list, with where each one came from (see includes/lead-source.php).
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/os-leads.php';
require_admin();
// Once this page is out: send the OS any leads it's missing (automatic catch-up).
register_shutdown_function('os_leads_catch_up_after_response', $pdo);

$hasSources = migration_table_exists($pdo, 'lead_sources');
$hasBookings = migration_table_exists($pdo, 'bookings');
$hasWhatsapp = migration_table_exists($pdo, 'whatsapp_leads');

// ── Drawlead OS connection (POST): save + test, test, send all, disconnect ──
// See includes/os-leads.php. Each action leaves a one-time message in the
// session and comes back to this page with the same filters.
$osAction = $_SERVER['REQUEST_METHOD'] === 'POST' ? (string) ($_POST['action'] ?? '') : '';
if (in_array($osAction, ['os_save', 'os_test', 'os_send_all', 'os_disconnect'], true)) {
    csrf_verify();
    $osCfg = os_leads_config($pdo);
    $flash = function (bool $ok, string $msg): void {
        $_SESSION['os_flash'] = ['ok' => $ok, 'msg' => $msg];
    };

    if ($osAction === 'os_save') {
        // No trailing slash: the OS would answer ".../leads/" with a redirect, which isn't followed.
        $url = rtrim(trim((string) ($_POST['os_url'] ?? '')), '/');
        $key = trim((string) ($_POST['os_key'] ?? ''));
        if ($key === '') {
            $key = $osCfg['key']; // left blank: keep the key already saved
        }
        if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url)) {
            $flash(false, 'Paste the Endpoint URL from OS → Sales Department → Website (it starts with https://).');
        } elseif ($key === '') {
            $flash(false, 'Paste the API key from OS → Sales Department → Website → Generate API key.');
        } else {
            set_setting($pdo, OS_LEADS_URL_SETTING, mb_substr($url, 0, 500));
            set_setting($pdo, OS_LEADS_KEY_SETTING, mb_substr($key, 0, 200));
            os_leads_sync_reset($pdo); // a new connection gets every lead already here
            [$ok, $msg] = os_leads_test($url, $key);
            os_leads_record($pdo, $ok, $ok ? '' : $msg);
            $flash($ok, $ok
                ? "Saved. $msg New bookings and WhatsApp chats now go to the OS, and the leads already here are being sent in the background."
                : "Saved, but the connection test failed: $msg");
        }
    } elseif ($osAction === 'os_test') {
        [$ok, $msg] = $osCfg['url'] !== '' && $osCfg['key'] !== ''
            ? os_leads_test($osCfg['url'], $osCfg['key'])
            : [false, 'Save the Endpoint URL and API key first.'];
        os_leads_record($pdo, $ok, $ok ? '' : $msg);
        $flash($ok, $ok ? $msg : "Connection test failed: $msg");
    } elseif ($osAction === 'os_send_all') {
        if ($osCfg['url'] === '' || $osCfg['key'] === '') {
            $flash(false, 'Save the Endpoint URL and API key first.');
        } elseif (($r = os_leads_sync_all($pdo)) === null) {
            $flash(true, 'The leads are already being sent to the OS in the background. Check back in a minute.');
        } else {
            $parts = [];
            if ($r['added']) {
                $parts[] = $r['added'] . ' added to the OS';
            }
            if ($r['already']) {
                $parts[] = $r['already'] . ' already there';
            }
            if ($r['skipped']) {
                $parts[] = $r['skipped'] . ' skipped (no name, phone or email)';
            }
            if ($r['failed']) {
                $parts[] = $r['failed'] . ' failed';
            }
            $summary = $parts ? implode(', ', $parts) . '.' : 'There are no leads to send yet.';
            $flash($r['failed'] === 0, $r['failed'] === 0
                ? "Done: $summary"
                : "$summary " . ($r['stopped'] ? 'Stopped early: ' : 'First problem: ') . $r['error']);
        }
    } else { // os_disconnect
        set_setting($pdo, OS_LEADS_URL_SETTING, '');
        set_setting($pdo, OS_LEADS_KEY_SETTING, '');
        $flash(true, 'Disconnected from the OS. New leads stay on the website only until you connect again.');
    }
    header('Location: ' . leads_url());
    exit;
}

// ── Delete one lead (POST): the booking or chat row, and its lead_sources row ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_lead') {
    csrf_verify();
    $delType = $_POST['lead_type'] ?? '';
    $delId = (int) ($_POST['lead_id'] ?? 0);
    $delTable = ['booking' => $hasBookings ? 'bookings' : null, 'whatsapp' => $hasWhatsapp ? 'whatsapp_leads' : null][$delType] ?? null;
    $delOk = false;
    if ($delTable && $delId > 0) {
        try {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM $delTable WHERE id = ?")->execute([$delId]);
            if ($hasSources) {
                $pdo->prepare('DELETE FROM lead_sources WHERE lead_type = ? AND lead_id = ?')->execute([$delType, $delId]);
            }
            $pdo->commit();
            $delOk = true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('admin/leads.php delete: ' . $e->getMessage());
        }
    }
    // Back to the same filters and page the delete was made from.
    $page = (int) ($_GET['page'] ?? 1);
    header('Location: ' . leads_url(['page' => $page > 1 ? $page : null, $delOk ? 'deleted' : 'delete_failed' => 1]));
    exit;
}

// ── Filters (GET) ──
$fChannel = is_string($_GET['source'] ?? null) ? trim($_GET['source']) : '';
$fForm = in_array($_GET['form'] ?? '', ['booking', 'whatsapp'], true) ? $_GET['form'] : '';
$fQuery = is_string($_GET['q'] ?? null) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$validDate = fn ($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) ? $d : '';
$fFrom = $validDate($_GET['from'] ?? '');
$fTo = $validDate($_GET['to'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 50;

// ── One list from both lead tables ──
$lsCols = $hasSources
    ? "COALESCE(ls.channel, '" . LEAD_SOURCE_NOT_RECORDED . "') AS channel, ls.source_detail, ls.page_url, ls.page_title, ls.trigger_label,
       ls.landing_page, ls.referrer, ls.utm_source, ls.utm_medium, ls.utm_campaign, ls.utm_term, ls.utm_content, ls.click_id,
       ls.first_channel, ls.first_detail, ls.first_landing_page, ls.first_referrer, ls.first_utm_campaign, ls.first_seen_at, ls.device"
    : "'" . LEAD_SOURCE_NOT_RECORDED . "' AS channel, NULL AS source_detail, NULL AS page_url, NULL AS page_title, NULL AS trigger_label,
       NULL AS landing_page, NULL AS referrer, NULL AS utm_source, NULL AS utm_medium, NULL AS utm_campaign, NULL AS utm_term, NULL AS utm_content, NULL AS click_id,
       NULL AS first_channel, NULL AS first_detail, NULL AS first_landing_page, NULL AS first_referrer, NULL AS first_utm_campaign, NULL AS first_seen_at, NULL AS device";
$join = fn (string $type, string $alias) => $hasSources
    ? "LEFT JOIN lead_sources ls ON ls.lead_type = '$type' AND ls.lead_id = $alias.id"
    : '';

// The form and date filters go inside each half of the UNION, on the real
// tables' columns: filtering the UNION's constant lead_type column from outside
// made MySQL fail with a 500.
$dateWhere = function (string $alias) use ($fFrom, $fTo): array {
    $w = [];
    $p = [];
    if ($fFrom !== '') {
        $w[] = "$alias.created_at >= ?";
        $p[] = $fFrom . ' 00:00:00';
    }
    if ($fTo !== '') {
        $w[] = "$alias.created_at < ?";
        $p[] = date('Y-m-d', strtotime($fTo . ' +1 day')) . ' 00:00:00';
    }
    return [$w ? ' WHERE ' . implode(' AND ', $w) : '', $p];
};
$parts = [];
$unionParams = [];
if ($hasBookings && $fForm !== 'whatsapp') {
    [$w, $p] = $dateWhere('b');
    $parts[] = "SELECT 'booking' AS lead_type, b.id AS lead_id, b.created_at, b.name, b.email, '' AS phone,
                       b.form_data AS payload, b.booking_date, b.booking_time, b.status, $lsCols
                FROM bookings b " . $join('booking', 'b') . $w;
    array_push($unionParams, ...$p);
}
if ($hasWhatsapp && $fForm !== 'booking') {
    [$w, $p] = $dateWhere('w');
    $parts[] = "SELECT 'whatsapp' AS lead_type, w.id AS lead_id, w.created_at, '' AS name, '' AS email, w.phone,
                       w.answers AS payload, NULL AS booking_date, NULL AS booking_time, NULL AS status, $lsCols
                FROM whatsapp_leads w " . $join('whatsapp', 'w') . $w;
    array_push($unionParams, ...$p);
}

/** WHERE clause + params for the current filters; $withChannel = false leaves the source filter out (for the per-source counts). */
$buildWhere = function (bool $withChannel) use ($fChannel, $fQuery, $unionParams): array {
    $where = [];
    $params = $unionParams; // the UNION's own placeholders come first in the SQL
    if ($withChannel && $fChannel !== '') {
        $where[] = 'l.channel = ?';
        $params[] = $fChannel;
    }
    if ($fQuery !== '') {
        $like = '%' . addcslashes($fQuery, '%_\\') . '%';
        $where[] = '(l.name LIKE ? OR l.email LIKE ? OR l.phone LIKE ? OR l.payload LIKE ? OR l.utm_campaign LIKE ? OR l.page_url LIKE ?)';
        array_push($params, $like, $like, $like, $like, $like, $like);
    }
    return [$where ? 'WHERE ' . implode(' AND ', $where) : '', $params];
};

$leads = [];
$total = 0;
$byChannel = [];
$byForm = ['booking' => 0, 'whatsapp' => 0];
$isExport = ($_GET['export'] ?? '') === 'csv';
$queryError = '';

if ($parts) try {
    $union = '(' . implode(' UNION ALL ', $parts) . ') l';

    [$whereAll, $paramsAll] = $buildWhere(false);
    $stmt = $pdo->prepare("SELECT l.channel, l.lead_type, COUNT(*) AS n FROM $union $whereAll GROUP BY l.channel, l.lead_type");
    $stmt->execute($paramsAll);
    foreach ($stmt->fetchAll() as $r) {
        $byChannel[$r['channel']] = ($byChannel[$r['channel']] ?? 0) + (int) $r['n'];
        if ($fChannel === '' || $fChannel === $r['channel']) {
            $byForm[$r['lead_type']] += (int) $r['n'];
        }
    }
    arsort($byChannel);

    [$where, $params] = $buildWhere(true);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM $union $where");
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();
    // Deleting the last lead on the last page would otherwise land on an empty page.
    $page = min($page, max(1, (int) ceil($total / $perPage)));

    $limit = $isExport ? '' : ' LIMIT ' . $perPage . ' OFFSET ' . (($page - 1) * $perPage);
    $stmt = $pdo->prepare("SELECT l.* FROM $union $where ORDER BY l.created_at DESC, l.lead_id DESC" . $limit);
    $stmt->execute($params);
    $leads = $stmt->fetchAll();
} catch (Throwable $e) {
    error_log('admin/leads.php: ' . $e->getMessage());
    $queryError = $e->getMessage();
    $leads = [];
    $total = 0;
    $byChannel = [];
    $byForm = ['booking' => 0, 'whatsapp' => 0];
}

// Booking form labels, and which booking fields hold a phone number.
$fieldLabels = [];
$phoneKeys = [];
if ($hasBookings && migration_table_exists($pdo, 'booking_form_fields')) {
    foreach ($pdo->query('SELECT field_key, label, field_type FROM booking_form_fields') as $f) {
        $fieldLabels[$f['field_key']] = $f['label'];
        if ($f['field_type'] === 'phone') {
            $phoneKeys[] = $f['field_key'];
        }
    }
}

/** Turn a raw lead row into what the page shows: contact details + the form answers as [label, value] pairs. */
function lead_view(array $l, array $fieldLabels, array $phoneKeys): array
{
    $answers = [];
    $phone = (string) $l['phone'];
    $data = json_decode((string) $l['payload'], true) ?: [];
    if ($l['lead_type'] === 'booking') {
        foreach ($data as $key => $val) {
            $val = is_array($val) ? implode(', ', $val) : (string) $val;
            if ($val === '' || in_array($key, ['name', 'email'], true)) {
                continue;
            }
            if ($phone === '' && in_array($key, $phoneKeys, true)) {
                $phone = $val;
                continue;
            }
            $answers[] = [$fieldLabels[$key] ?? $key, $val];
        }
    } else {
        foreach ($data as $a) {
            if (is_array($a) && ($a['answer'] ?? '') !== '') {
                $answers[] = [(string) ($a['question'] ?? ''), (string) $a['answer']];
            }
        }
    }
    return ['phone' => $phone, 'answers' => $answers];
}

function lead_form_label(string $type): string
{
    return $type === 'booking' ? 'Calendar booking' : 'WhatsApp chat';
}

function lead_click_label(string $id): string
{
    return [
        'gclid' => 'Google Ads click', 'gbraid' => 'Google Ads click', 'wbraid' => 'Google Ads click',
        'fbclid' => 'Facebook / Instagram link', 'msclkid' => 'Microsoft Ads click',
        'li_fat_id' => 'LinkedIn Ads click', 'ttclid' => 'TikTok Ads click',
    ][$id] ?? $id;
}

// ── CSV export (same filters, all rows) ──
if ($isExport) {
    $cell = function ($v): string {
        $v = (string) $v;
        if ($v !== '' && strpbrk($v[0], "=+-@\t\r") !== false) {
            $v = "'" . $v; // stop spreadsheets running it as a formula
        }
        return $v;
    };
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="leads-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads it right
    fputcsv($out, ['Received', 'Form', 'Name', 'Email', 'Phone', 'Source', 'Source detail', 'Sent from page', 'Opened from',
        'Landing page', 'Referrer', 'UTM source', 'UTM medium', 'UTM campaign', 'UTM term', 'UTM content', 'Ad click',
        'Device', 'First source', 'First landing page', 'First visit', 'Appointment', 'Answers']);
    foreach ($leads as $l) {
        $v = lead_view($l, $fieldLabels, $phoneKeys);
        $answers = implode(' | ', array_map(fn ($a) => $a[0] . ': ' . $a[1], $v['answers']));
        $appt = $l['booking_date'] ? date('Y-m-d', strtotime($l['booking_date'])) . ' ' . date('g:i A', strtotime($l['booking_time'])) : '';
        fputcsv($out, array_map($cell, [
            $l['created_at'], lead_form_label($l['lead_type']), $l['name'], $l['email'], $v['phone'],
            $l['channel'], $l['source_detail'], $l['page_url'], $l['trigger_label'],
            $l['landing_page'], $l['referrer'], $l['utm_source'], $l['utm_medium'], $l['utm_campaign'], $l['utm_term'], $l['utm_content'],
            $l['click_id'] ? lead_click_label($l['click_id']) : '',
            $l['device'], $l['first_channel'], $l['first_landing_page'], $l['first_seen_at'], $appt, $answers,
        ]));
    }
    fclose($out);
    exit;
}

/** Colour per channel group, for the dot in the source pill and the bar. */
function lead_channel_color(string $c): string
{
    return match (true) {
        in_array($c, ['Google Ads', 'Meta Ads', 'LinkedIn Ads', 'Microsoft Ads'], true) => '#d97706',
        in_array($c, ['Google Search', 'Bing Search', 'Other Search', 'Google Business Profile'], true) => '#2563eb',
        in_array($c, ['Facebook', 'Instagram', 'LinkedIn', 'YouTube', 'X (Twitter)', 'Other Social'], true) => '#9333ea',
        $c === 'WhatsApp' => '#16a34a',
        $c === 'Email' => '#0891b2',
        $c === 'AI Chat' => '#db2777',
        in_array($c, ['Referral', 'Other Campaign'], true) => '#0d9488',
        $c === 'Direct' => '#475569',
        default => '#a1a1aa',
    };
}

/** Current filters as a query string, with some keys changed. */
function leads_url(array $changes = []): string
{
    $q = array_merge(array_intersect_key($_GET, array_flip(['source', 'form', 'q', 'from', 'to'])), $changes);
    $q = array_filter($q, fn ($v) => $v !== '' && $v !== null);
    return 'leads.php' . ($q ? '?' . http_build_query($q) : '');
}

$today = date('Y-m-d');
$presets = [
    'Today'        => [$today, $today],
    'Last 7 days'  => [date('Y-m-d', strtotime('-6 days')), $today],
    'Last 30 days' => [date('Y-m-d', strtotime('-29 days')), $today],
    'Last 90 days' => [date('Y-m-d', strtotime('-89 days')), $today],
    'All time'     => ['', ''],
];
$allCount = array_sum($byChannel);
$pages = max(1, (int) ceil($total / $perPage));
$channelOptions = array_unique(array_merge(LEAD_SOURCE_CHANNELS, [LEAD_SOURCE_NOT_RECORDED], array_keys($byChannel)));
$topChannel = '';
foreach ($byChannel as $c => $n) {
    if ($c !== LEAD_SOURCE_NOT_RECORDED) {
        $topChannel = $c;
        break;
    }
}

$pageTitle = 'Lead Source';
$pageSub = 'Every lead from the booking calendar and WhatsApp chat, and where it came from.';
$activeNav = 'leads';
include __DIR__ . '/includes/header.php';
?>

<?php if (!$hasSources): ?>
<div class="alert alert-error">
  Lead sources aren't being recorded yet. Run <strong>Migration 030</strong> on the
  <a href="run-migrations.php">Migrations</a> page — until then every lead shows as "Not recorded".
</div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Lead deleted.</div><?php endif; ?>
<?php if (isset($_GET['delete_failed'])): ?><div class="alert alert-error">That lead couldn't be deleted. Please try again.</div><?php endif; ?>

<?php if ($queryError): ?>
<div class="alert alert-error">
  The leads couldn't be loaded for these filters. <a href="leads.php">Clear filters</a>.
  <div class="t-sub" style="margin-top:.4rem">Database error: <?= h($queryError) ?></div>
</div>
<?php endif; ?>

<?php
// ── Drawlead OS connection card ──
$osCfg = os_leads_config($pdo);
$osOn = $osCfg['url'] !== '' && $osCfg['key'] !== '';
$osStatus = os_leads_status($pdo);
$osSync = os_leads_sync_state($pdo);
$osFlash = $_SESSION['os_flash'] ?? null;
unset($_SESSION['os_flash']);
$osKeyHint = $osCfg['key'] !== '' ? mb_substr($osCfg['key'], 0, 8) . '…' . mb_substr($osCfg['key'], -4) : '';
[$osPillClass, $osPillText] = !$osOn ? ['off', 'Not connected'] : ($osStatus['last_error'] !== '' ? ['err', 'Connection problem'] : ['on', 'Connected']);
$osWhen = fn (string $dt): string => $dt !== '' ? date('M j, Y g:i A', strtotime($dt)) : '';
?>
<?php if ($osFlash): ?><div class="alert alert-<?= $osFlash['ok'] ? 'success' : 'error' ?>"><?= h($osFlash['msg']) ?></div><?php endif; ?>

<div class="card os-card" id="os-connection">
  <div class="toolbar" style="margin-bottom:.6rem">
    <div>
      <div class="card-title">Drawlead OS connection</div>
      <div class="card-desc" style="margin-bottom:0">Sends every calendar booking and WhatsApp chat to Drawlead OS → Sales Department → Pre-sales → Lead, with source “Website/Popup”, “Website/Calendar” or “Website/WhatsApp” for the form it came through.</div>
    </div>
    <span class="os-pill os-pill-<?= $osPillClass ?>"><span class="ls-dot"></span><?= h($osPillText) ?></span>
  </div>

  <?php if ($osOn): ?>
  <div class="os-status t-sub">
    <?php if ($osStatus['last_ok_at']): ?>Last successful contact: <?= h($osWhen($osStatus['last_ok_at'])) ?>.<?php else: ?>No lead sent yet.<?php endif; ?>
    <?php if ($osSync['behind']): ?>
    The leads already here are being sent to the OS automatically in the background (tried again every <?= OS_LEADS_RETRY_MINUTES ?> minutes if the OS can't take them).
    <?php elseif ($osSync['synced_at'] !== ''): ?>
    Every lead here has been sent to the OS. New ones go automatically.
    <?php endif; ?>
    <?php if ($osStatus['last_error'] !== ''): ?><div class="os-error">Last problem (<?= h($osWhen($osStatus['last_error_at'])) ?>): <?= h($osStatus['last_error']) ?></div><?php endif; ?>
  </div>
  <div class="os-actions">
    <form method="post" action="<?= h(leads_url()) ?>">
      <?= csrf_field() ?><input type="hidden" name="action" value="os_test">
      <button type="submit" class="btn btn-ghost btn-sm">Test connection</button>
    </form>
    <form method="post" action="<?= h(leads_url()) ?>" onsubmit="return confirm(<?= h(json_encode('Send all ' . ($allCount ?? 0) . ' website leads to the OS now? This happens automatically anyway; leads the OS already has are not added again. This can take a minute.', JSON_UNESCAPED_UNICODE)) ?>);">
      <?= csrf_field() ?><input type="hidden" name="action" value="os_send_all">
      <button type="submit" class="btn btn-ghost btn-sm">Send all leads now</button>
    </form>
    <form method="post" action="<?= h(leads_url()) ?>" onsubmit="return confirm('Disconnect from the OS? New leads will stay on the website only.');">
      <?= csrf_field() ?><input type="hidden" name="action" value="os_disconnect">
      <button type="submit" class="btn btn-danger btn-sm">Disconnect</button>
    </form>
  </div>
  <?php endif; ?>

  <details class="os-settings"<?= $osOn ? '' : ' open' ?>>
    <summary><?= $osOn ? 'Change connection settings' : 'Connect to Drawlead OS' ?></summary>
    <form method="post" action="<?= h(leads_url()) ?>" class="os-form" autocomplete="off">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="os_save">
      <div class="field">
        <label for="os-url">Endpoint URL</label>
        <input type="url" id="os-url" name="os_url" value="<?= h($osCfg['url']) ?>" placeholder="https://your-os-domain/api/leads-v2/public/leads" required>
      </div>
      <div class="field">
        <label for="os-key">API key</label>
        <input type="password" id="os-key" name="os_key" autocomplete="off" spellcheck="false"
          placeholder="<?= h($osOn ? 'Saved (' . $osKeyHint . ') — leave blank to keep it' : 'dlk_…') ?>"<?= $osOn ? '' : ' required' ?>>
      </div>
      <div class="os-form-foot">
        <button type="submit" class="btn btn-primary btn-sm">Save &amp; test</button>
        <span class="t-sub">Get both from Drawlead OS → Sales Department → <b>Website</b> tab → Generate API key.</span>
      </div>
    </form>
  </details>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-v"><?= $byForm['booking'] + $byForm['whatsapp'] ?></div>
    <div class="stat-l">Leads<?= ($fFrom || $fTo) ? ' in period' : '' ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-v"><?= $byForm['booking'] ?></div>
    <div class="stat-l">Calendar bookings</div>
  </div>
  <div class="stat-card">
    <div class="stat-v"><?= $byForm['whatsapp'] ?></div>
    <div class="stat-l">WhatsApp chats</div>
  </div>
  <div class="stat-card">
    <div class="stat-v ls-top"><?= h($topChannel ?: '—') ?></div>
    <div class="stat-l">Top source</div>
  </div>
</div>

<div class="card">
  <form method="get" class="ls-filters">
    <div class="field">
      <label for="ls-q">Search</label>
      <input type="search" id="ls-q" name="q" value="<?= h($fQuery) ?>" placeholder="Name, email, phone, campaign…">
    </div>
    <div class="field">
      <label for="ls-source">Source</label>
      <select id="ls-source" name="source">
        <option value="">All sources</option>
        <?php foreach ($channelOptions as $c): ?>
        <option value="<?= h($c) ?>" <?= $fChannel === $c ? 'selected' : '' ?>><?= h($c) ?><?= isset($byChannel[$c]) ? ' (' . $byChannel[$c] . ')' : '' ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label for="ls-form">Form</label>
      <select id="ls-form" name="form">
        <option value="">Both forms</option>
        <option value="booking" <?= $fForm === 'booking' ? 'selected' : '' ?>>Calendar booking</option>
        <option value="whatsapp" <?= $fForm === 'whatsapp' ? 'selected' : '' ?>>WhatsApp chat</option>
      </select>
    </div>
    <div class="field">
      <label for="ls-from">From</label>
      <input type="date" id="ls-from" name="from" value="<?= h($fFrom) ?>">
    </div>
    <div class="field">
      <label for="ls-to">To</label>
      <input type="date" id="ls-to" name="to" value="<?= h($fTo) ?>">
    </div>
    <div class="ls-filter-actions">
      <button type="submit" class="btn btn-primary btn-sm">Apply</button>
      <?php if ($fChannel || $fForm || $fQuery || $fFrom || $fTo): ?><a class="btn btn-ghost btn-sm" href="leads.php">Clear</a><?php endif; ?>
    </div>
  </form>
  <div class="ls-presets">
    <?php foreach ($presets as $label => [$from, $to]):
      $on = $fFrom === $from && $fTo === $to; ?>
      <a class="ls-chip<?= $on ? ' on' : '' ?>" href="<?= h(leads_url(['from' => $from, 'to' => $to, 'page' => null])) ?>"><?= h($label) ?></a>
    <?php endforeach; ?>
  </div>
</div>

<div class="card">
  <div class="toolbar" style="margin-bottom:.8rem">
    <div>
      <div class="card-title">All leads</div>
      <div class="card-desc" style="margin-bottom:0"><?= $total ?> lead<?= $total === 1 ? '' : 's' ?>, newest first.</div>
    </div>
    <?php if ($total): ?><a class="btn btn-ghost btn-sm" href="<?= h(leads_url(['export' => 'csv'])) ?>">Export CSV ↓</a><?php endif; ?>
  </div>

  <?php if (!$leads): ?>
    <div class="empty-state"><p><?= $allCount ? 'No leads match these filters.' : 'No leads yet. They show up here when someone books a call or finishes the WhatsApp chat.' ?></p></div>
  <?php else: ?>
  <div class="ls-table-wrap">
  <table class="ls-table">
    <thead><tr><th>Received</th><th>Lead</th><th>Form</th><th>Source</th><th>Sent from</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($leads as $i => $l):
        $v = lead_view($l, $fieldLabels, $phoneKeys);
        $recorded = $l['channel'] !== LEAD_SOURCE_NOT_RECORDED;
        $rowId = 'ls-d-' . $l['lead_type'] . '-' . (int) $l['lead_id'];
        $waDigits = preg_replace('/[^0-9]/', '', $v['phone']);
      ?>
      <tr>
        <td class="ls-nowrap"><?= h(date('M j, Y', strtotime($l['created_at']))) ?><div class="t-sub"><?= h(date('g:i A', strtotime($l['created_at']))) ?></div></td>
        <td>
          <div class="t-name"><?= h($l['name'] ?: ($v['phone'] ?: '—')) ?></div>
          <?php if ($l['email']): ?><div class="t-sub"><a class="ls-link" href="mailto:<?= h($l['email']) ?>"><?= h($l['email']) ?></a></div><?php endif; ?>
          <?php if ($v['phone'] && $l['name']): ?><div class="t-sub"><?= h($v['phone']) ?></div><?php endif; ?>
          <?php if ($waDigits && strlen($waDigits) >= 7): ?><div class="t-sub"><a class="ls-link" href="https://wa.me/<?= h($waDigits) ?>" target="_blank" rel="noopener">WhatsApp ↗</a></div><?php endif; ?>
        </td>
        <td>
          <span class="ls-form ls-form-<?= h($l['lead_type']) ?>"><?= h(lead_form_label($l['lead_type'])) ?></span>
          <?php if ($l['lead_type'] === 'booking' && $l['booking_date']): ?>
            <div class="t-sub">Call: <?= h(date('M j', strtotime($l['booking_date']))) ?>, <?= h(date('g:i A', strtotime($l['booking_time']))) ?><?= $l['status'] === 'cancelled' ? ' · cancelled' : '' ?></div>
          <?php endif; ?>
        </td>
        <td>
          <a class="ls-pill" href="<?= h(leads_url(['source' => $l['channel'], 'page' => null])) ?>"><span class="ls-dot" style="background:<?= lead_channel_color($l['channel']) ?>"></span><?= h($l['channel']) ?></a>
          <?php if ($recorded && ($l['utm_campaign'] || $l['source_detail'])): ?>
            <div class="t-sub ls-clip"><?= $l['utm_campaign'] ? 'Campaign: ' . h($l['utm_campaign']) : h($l['source_detail']) ?></div>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($recorded && $l['page_url']): ?>
            <div class="ls-clip"><a class="ls-link" href="<?= h($l['page_url']) ?>" target="_blank" rel="noopener" title="<?= h($l['page_url']) ?>"><?= h($l['page_title'] ?: $l['page_url']) ?></a></div>
            <div class="t-sub ls-clip"><?= h($l['page_url']) ?></div>
          <?php else: ?><span class="t-sub">—</span><?php endif; ?>
        </td>
        <td class="ls-nowrap">
          <button type="button" class="ls-more" aria-haspopup="dialog" data-ls-open="<?= h($rowId) ?>">Details</button>
          <template id="<?= h($rowId) ?>"
            data-title="<?= h($l['name'] ?: ($v['phone'] ?: 'Lead')) ?>"
            data-sub="<?= h(lead_form_label($l['lead_type']) . ' · ' . date('M j, Y g:i A', strtotime($l['created_at']))) ?>"
            data-lead-type="<?= h($l['lead_type']) ?>" data-lead-id="<?= (int) $l['lead_id'] ?>"
            data-confirm="<?= h('Delete the lead from ' . ($l['name'] ?: ($v['phone'] ?: 'this person')) . '?'
              . ($l['lead_type'] === 'booking' ? ' This also deletes their calendar booking, so the time slot can be booked again.' : '')
              . ' This can’t be undone.') ?>">
          <div class="ls-detail-grid">
            <div>
              <div class="ls-h">How they found you</div>
              <?php if (!$recorded): ?>
                <p class="t-sub">Received before Lead Source tracking was switched on, so where it came from wasn't recorded.</p>
              <?php else: ?>
              <dl class="ls-dl">
                <dt>Source</dt><dd><?= h($l['channel']) ?><?= $l['source_detail'] ? ' · ' . h($l['source_detail']) : '' ?></dd>
                <dt>Opened from</dt><dd><?= h($l['trigger_label'] ?: '—') ?></dd>
                <dt>Landing page</dt><dd class="ls-break"><?= h($l['landing_page'] ?: '—') ?></dd>
                <dt>Referrer</dt><dd class="ls-break"><?= h($l['referrer'] ?: 'None (typed the address, a bookmark, or an app)') ?></dd>
                <?php foreach (['utm_source' => 'UTM source', 'utm_medium' => 'UTM medium', 'utm_campaign' => 'UTM campaign', 'utm_term' => 'UTM term', 'utm_content' => 'UTM content'] as $k => $lbl): ?>
                  <?php if ($l[$k]): ?><dt><?= $lbl ?></dt><dd class="ls-break"><?= h($l[$k]) ?></dd><?php endif; ?>
                <?php endforeach; ?>
                <?php if ($l['click_id']): ?><dt>Ad click</dt><dd><?= h(lead_click_label($l['click_id'])) ?></dd><?php endif; ?>
                <?php if ($l['device']): ?><dt>Device</dt><dd><?= h($l['device']) ?></dd><?php endif; ?>
              </dl>
              <?php if ($l['first_channel'] && ($l['first_channel'] !== $l['channel'] || $l['first_landing_page'] !== $l['landing_page'])): ?>
              <div class="ls-h" style="margin-top:1rem">First visit</div>
              <dl class="ls-dl">
                <dt>Source</dt><dd><?= h($l['first_channel']) ?><?= $l['first_detail'] ? ' · ' . h($l['first_detail']) : '' ?></dd>
                <?php if ($l['first_utm_campaign']): ?><dt>Campaign</dt><dd><?= h($l['first_utm_campaign']) ?></dd><?php endif; ?>
                <dt>Landing page</dt><dd class="ls-break"><?= h($l['first_landing_page'] ?: '—') ?></dd>
                <?php if ($l['first_seen_at']): ?><dt>When</dt><dd><?= h(date('M j, Y g:i A', strtotime($l['first_seen_at']))) ?></dd><?php endif; ?>
              </dl>
              <?php endif; ?>
              <?php endif; ?>
            </div>
            <div>
              <div class="ls-h"><?= $l['lead_type'] === 'booking' ? 'Booking form answers' : 'Chat answers' ?></div>
              <?php if (!$v['answers']): ?><p class="t-sub">No other answers.</p><?php else: ?>
              <dl class="ls-dl">
                <?php foreach ($v['answers'] as [$q, $a]): ?><dt><?= h($q) ?></dt><dd><?= h($a) ?></dd><?php endforeach; ?>
              </dl>
              <?php endif; ?>
            </div>
          </div>
          </template>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>

  <?php if ($pages > 1): ?>
  <div class="ls-pager">
    <?php if ($page > 1): ?><a class="btn btn-ghost btn-sm" href="<?= h(leads_url(['page' => $page - 1])) ?>">← Newer</a><?php endif; ?>
    <span class="t-sub">Page <?= $page ?> of <?= $pages ?></span>
    <?php if ($page < $pages): ?><a class="btn btn-ghost btn-sm" href="<?= h(leads_url(['page' => $page + 1])) ?>">Older →</a><?php endif; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<div class="access-note">
  How sources are worked out: an ad click or UTM tags in the link win first (tag your ad and social links with
  <code>utm_source</code> / <code>utm_medium</code> / <code>utm_campaign</code> for the clearest results), then the site the visitor came from
  (Google, Instagram, ChatGPT…). "Direct" means no referrer, for example a typed address, a bookmark, or a link opened from an app that
  hides where it came from. A visitor keeps their last real source for 90 days, so someone who finds you on Google and comes back later by
  typing the address still counts as Google.
</div>

<?php if ($leads): ?>
<dialog class="media-dialog ls-dialog" id="lsDialog" aria-labelledby="lsDialogTitle">
  <div class="media-dialog-form">
    <div class="media-dialog-head">
      <div>
        <h2 id="lsDialogTitle"></h2>
        <div class="t-sub" data-ls-sub></div>
      </div>
      <button type="button" class="media-dialog-x" data-ls-close aria-label="Close">×</button>
    </div>
    <div class="ls-dialog-body" data-ls-body></div>
    <div class="media-dialog-foot">
      <form method="post" action="<?= h(leads_url(['page' => $page > 1 ? $page : null])) ?>" data-ls-delete>
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete_lead">
        <input type="hidden" name="lead_type" value="">
        <input type="hidden" name="lead_id" value="">
        <button type="submit" class="btn btn-danger">Delete lead</button>
      </form>
      <button type="button" class="btn btn-ghost" data-ls-close>Close</button>
    </div>
  </div>
</dialog>
<?php endif; ?>

<style>
.os-card{margin-bottom:1.25rem}
.os-pill{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:700;padding:5px 12px;border-radius:20px;white-space:nowrap}
.os-pill-on{background:#e9f9f0;color:#0f7a4c}.os-pill-on .ls-dot{background:#16a34a}
.os-pill-off{background:var(--bg);color:var(--g500)}.os-pill-off .ls-dot{background:var(--g300)}
.os-pill-err{background:#fdecef;color:#b3123a}.os-pill-err .ls-dot{background:var(--danger)}
.os-status{margin-bottom:.9rem}
.os-error{color:#b3123a;margin-top:3px}
.os-actions{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:1rem}
.os-actions form{margin:0}
.os-settings summary{cursor:pointer;font-size:12.5px;font-weight:700;color:var(--g600);user-select:none}
.os-form{display:grid;grid-template-columns:1.4fr 1fr;gap:12px;margin-top:1rem;align-items:end}
.os-form .field{margin-bottom:0}
.os-form-foot{grid-column:1/-1;display:flex;align-items:center;gap:12px;flex-wrap:wrap}
@media(max-width:700px){.os-form{grid-template-columns:1fr}}
.ls-top{font-size:20px;line-height:1.25;padding-top:4px}
.ls-filters{display:grid;grid-template-columns:2fr 1.3fr 1.1fr 1fr 1fr auto;gap:12px;align-items:end}
.ls-filters .field{margin-bottom:0}
.ls-filter-actions{display:flex;gap:8px;padding-bottom:1px}
.ls-presets{display:flex;flex-wrap:wrap;gap:6px;margin-top:1rem}
.ls-chip{font-size:11.5px;font-weight:700;color:var(--g500);text-decoration:none;padding:6px 12px;border:1.5px solid var(--border);border-radius:20px}
.ls-chip:hover{border-color:var(--g400);color:var(--black)}
.ls-chip.on{background:var(--black);border-color:var(--black);color:#fff}
.ls-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;display:inline-block}
.ls-pill{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:700;padding:4px 10px;border:1.5px solid var(--border);border-radius:20px;text-decoration:none;color:var(--black);white-space:nowrap}
.ls-pill:hover{border-color:var(--g400)}
.ls-form{display:inline-block;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;padding:3px 9px;border-radius:20px;white-space:nowrap}
.ls-form-booking{background:#eaf1fe;color:#1d5fd6}
.ls-form-whatsapp{background:#e9f9f0;color:#0f7a4c}
.ls-table-wrap{overflow-x:auto}
.ls-table td{vertical-align:top}
.ls-nowrap{white-space:nowrap}
.ls-clip{max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ls-link{color:inherit;text-decoration:none;font-weight:600}
.ls-link:hover{color:var(--violet);text-decoration:underline}
.ls-more{font:inherit;font-size:12px;font-weight:700;background:none;border:1.5px solid var(--border);border-radius:6px;padding:5px 10px;cursor:pointer;color:var(--g600)}
.ls-more:hover{border-color:var(--blue);color:var(--violet)}
.ls-dialog .media-dialog-foot{justify-content:space-between}
.ls-dialog .media-dialog-foot form{margin:0}
.ls-dialog{width:min(820px,calc(100vw - 32px))}
.ls-dialog .media-dialog-head{align-items:flex-start}
.ls-dialog .media-dialog-head h2{font-size:16px;margin-bottom:2px}
.ls-dialog-body{padding:1.25rem;overflow-y:auto}
.ls-detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.75rem}
.ls-h{font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:var(--g400);font-weight:800;margin-bottom:.5rem}
.ls-dl{display:grid;grid-template-columns:120px 1fr;gap:6px 12px;font-size:12.5px}
.ls-dl dt{color:var(--g500);font-weight:600}
.ls-dl dd{font-weight:600}
.ls-break{word-break:break-all}
.ls-pager{display:flex;align-items:center;justify-content:center;gap:14px;margin-top:1.1rem}
.access-note code{font-size:11px;background:var(--white);padding:1px 5px;border-radius:4px}
@media(max-width:1100px){.ls-filters{grid-template-columns:1fr 1fr 1fr}.ls-filter-actions{grid-column:1/-1}}
@media(max-width:700px){
  .stat-grid{grid-template-columns:1fr 1fr}
  .ls-filters{grid-template-columns:1fr 1fr}
  .ls-filters .field:first-child{grid-column:1/-1}
  .ls-detail-grid{grid-template-columns:1fr}
  .ls-dl{grid-template-columns:100px 1fr}
}
</style>

<script>
(function () {
  var dialog = document.getElementById('lsDialog');
  if (!dialog) return;
  var body = dialog.querySelector('[data-ls-body]');
  var delForm = dialog.querySelector('[data-ls-delete]');
  var confirmText = '';
  delForm.addEventListener('submit', function (e) {
    if (!delForm.elements.lead_id.value || !confirm(confirmText)) e.preventDefault();
  });
  var close = function () {
    if (typeof dialog.close === 'function') dialog.close(); else dialog.removeAttribute('open');
  };

  document.querySelectorAll('[data-ls-open]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var tpl = document.getElementById(btn.getAttribute('data-ls-open'));
      if (!tpl) return;
      document.getElementById('lsDialogTitle').textContent = tpl.getAttribute('data-title');
      dialog.querySelector('[data-ls-sub]').textContent = tpl.getAttribute('data-sub');
      delForm.elements.lead_type.value = tpl.getAttribute('data-lead-type');
      delForm.elements.lead_id.value = tpl.getAttribute('data-lead-id');
      confirmText = tpl.getAttribute('data-confirm');
      body.replaceChildren(tpl.content.cloneNode(true));
      body.scrollTop = 0;
      if (typeof dialog.showModal === 'function') dialog.showModal(); else dialog.setAttribute('open', '');
    });
  });

  dialog.querySelectorAll('[data-ls-close]').forEach(function (b) { b.addEventListener('click', close); });
  // A click on the dimmed backdrop lands on the <dialog> itself, not its contents.
  dialog.addEventListener('click', function (e) { if (e.target === dialog) close(); });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
