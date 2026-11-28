<?php
/**
 * Beacon endpoint for the built-in website analytics (assets/pulse.js).
 * Takes one JSON object per request and answers 204 with no body, whatever
 * happens, so a visitor never sees an error from it. The work is done by
 * site_stats_record() in includes/site-stats.php.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/site-stats.php';

header('Cache-Control: no-store');
http_response_code(204);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}
// the site's own logged-in users (admins and editors checking pages) are not visitors
$loggedIn = current_user() !== null;
session_write_close();
if ($loggedIn) {
    exit;
}
$raw = file_get_contents('php://input', false, null, 0, 4096);
try {
    site_stats_record($pdo, is_string($raw) ? json_decode($raw, true) : null);
} catch (Throwable $e) {
    error_log('pulse: ' . $e->getMessage());
}
