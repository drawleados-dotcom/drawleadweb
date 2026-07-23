<?php
/**
 * Drawlead CMS — shared bootstrap.
 * Included by both the public front controller (index.php) and every
 * admin/*.php page. Sets up the DB connection, session, and auth/CSRF helpers.
 */

// Real DB credentials live in config.php, which is git-ignored — never
// committed. See config.sample.php for the template. Each server (staging
// subdomain, later production) gets its own config.php created directly
// on that server.
$__config = __DIR__ . '/config.php';
if (!is_file($__config)) {
    http_response_code(500);
    die('Missing includes/config.php — copy includes/config.sample.php to includes/config.php and fill in your database credentials.');
}
require_once $__config;

// Do NOT show raw PHP errors to site visitors in production.
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', '/uploads/');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log('Drawlead CMS: DB connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Site is temporarily unavailable. Please try again shortly.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** Escape a string for safe HTML output. */
function h(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/** Read a value from the settings table (GA id, GSC tag, etc). */
function get_setting(PDO $pdo, string $key, string $default = ''): string
{
    static $cache = [];
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    $stmt = $pdo->prepare('SELECT `value` FROM settings WHERE `key` = ?');
    $stmt->execute([$key]);
    $v = $stmt->fetchColumn();
    return $cache[$key] = ($v !== false ? $v : $default);
}

/** Save/overwrite a setting. */
function set_setting(PDO $pdo, string $key, string $value): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
    );
    $stmt->execute([$key, $value]);
}

// ── Auth ──

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!current_user()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void
{
    require_login();
    if (current_user()['role'] !== 'admin') {
        http_response_code(403);
        die('You do not have permission to view this page. Ask an admin for access.');
    }
}

function user_has_page_access(PDO $pdo, int $userId, string $role, int $pageId): bool
{
    if ($role === 'admin') {
        return true;
    }
    $stmt = $pdo->prepare(
        "SELECT 1 FROM user_access WHERE user_id = ? AND item_type = 'page' AND item_id = ?"
    );
    $stmt->execute([$userId, $pageId]);
    return (bool) $stmt->fetchColumn();
}

function user_has_blogs_access(PDO $pdo, int $userId, string $role): bool
{
    if ($role === 'admin') {
        return true;
    }
    $stmt = $pdo->prepare(
        "SELECT 1 FROM user_access WHERE user_id = ? AND item_type = 'blogs'"
    );
    $stmt->execute([$userId]);
    return (bool) $stmt->fetchColumn();
}

function require_blogs_access(PDO $pdo): void
{
    require_login();
    $u = current_user();
    if (!user_has_blogs_access($pdo, $u['id'], $u['role'])) {
        http_response_code(403);
        die('You do not have access to the Blogs module. Ask an admin for access.');
    }
}

// ── CSRF ──

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', is_string($token) ? $token : '')) {
        http_response_code(403);
        die('Your session expired or this request looks invalid. Please go back and try again.');
    }
}

/**
 * Flip any scheduled posts whose time has arrived to 'published'. There's
 * no cron on shared hosting, so this runs opportunistically — called near
 * the top of the public front controller and the admin Blogs pages, so a
 * scheduled post goes live the moment anyone next hits the site or admin
 * after its scheduled time.
 */
function publish_due_scheduled_posts(PDO $pdo): void
{
    $pdo->exec("UPDATE blogs SET status = 'published' WHERE status = 'scheduled' AND scheduled_at <= NOW()");
}

/** Turn "My New Page" into "my-new-page". */
function slugify(string $s): string
{
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

/**
 * Very small allowlist-based HTML sanitizer for blog post content.
 * Only trusted, logged-in editors with Blogs access can submit this —
 * this is defense-in-depth, not a public-input sanitizer.
 */
function sanitize_blog_html(string $html): string
{
    $allowed = '<p><br><b><strong><i><em><u><a><ul><ol><li><h2><h3><h4>'
             . '<blockquote><img><figure><figcaption><span><hr>';
    $html = strip_tags($html, $allowed);
    // Strip inline event handlers (onclick=, onerror=, ...).
    $html = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $html);
    $html = preg_replace("/\son\w+\s*=\s*'[^']*'/i", '', $html);
    // Neutralize javascript: URLs in href/src.
    $html = preg_replace('/(href|src)\s*=\s*"javascript:[^"]*"/i', '$1="#"', $html);
    $html = preg_replace("/(href|src)\s*=\s*'javascript:[^']*'/i", "$1='#'", $html);
    return $html;
}
