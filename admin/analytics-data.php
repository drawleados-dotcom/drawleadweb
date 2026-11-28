<?php
/**
 * JSON for the analytics popups on admin/analytics.php.
 *   GET ?source=ga|gsc|clarity&days=N[&refresh=1]
 * Answers {ok: true, ...dashboard} or {ok: false, error, setup, sa_email}.
 * The work is done in includes/analytics-dash.php.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/analytics-dash.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if (!current_user() || current_user()['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Your session has ended or you are not an admin. Reload the page and log in again.', 'setup' => '']);
    exit;
}
// a dashboard can make several API calls; let it finish even if one is slow
@set_time_limit(60);
session_write_close();

$source = (string) ($_GET['source'] ?? '');
$days = (int) ($_GET['days'] ?? 0);
$refresh = ($_GET['refresh'] ?? '') === '1';

try {
    $out = dash_report($pdo, $source, $days, $refresh);
} catch (DashError $e) {
    $out = ['ok' => false, 'error' => $e->getMessage(), 'setup' => $e->setup];
} catch (Throwable $e) {
    error_log('analytics-data: ' . $e->getMessage());
    $out = ['ok' => false, 'error' => 'Something went wrong while loading these numbers. Try again in a minute.', 'setup' => ''];
}
if (!$out['ok']) {
    $out['sa_email'] = dash_service_account($pdo)['client_email'] ?? '';
}
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
