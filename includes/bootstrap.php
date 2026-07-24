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

function user_has_case_studies_access(PDO $pdo, int $userId, string $role): bool
{
    if ($role === 'admin') {
        return true;
    }
    $stmt = $pdo->prepare(
        "SELECT 1 FROM user_access WHERE user_id = ? AND item_type = 'case_studies'"
    );
    $stmt->execute([$userId]);
    return (bool) $stmt->fetchColumn();
}

function require_case_studies_access(PDO $pdo): void
{
    require_login();
    $u = current_user();
    if (!user_has_case_studies_access($pdo, $u['id'], $u['role'])) {
        http_response_code(403);
        die('You do not have access to the Case Studies module. Ask an admin for access.');
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
    // Best-effort housekeeping, called on every page load — must never be
    // allowed to take the site down (e.g. if a migration adding the
    // scheduled_at column hasn't been run yet on this database).
    try {
        $pdo->exec("UPDATE blogs SET status = 'published' WHERE status = 'scheduled' AND scheduled_at <= NOW()");
    } catch (PDOException $e) {
        error_log('publish_due_scheduled_posts skipped: ' . $e->getMessage());
    }
}

// ── Schema introspection (used by admin/run-migrations.php) ──

function migration_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );
    $stmt->execute([$table, $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function migration_table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
    );
    $stmt->execute([$table]);
    return (int) $stmt->fetchColumn() > 0;
}

function pending_migrations_exist(PDO $pdo): bool
{
    if (!migration_column_exists($pdo, 'blogs', 'scheduled_at')) {
        return true;
    }
    foreach (['booking_availability', 'booking_form_fields', 'bookings', 'booking_notification_emails', 'whatsapp_flow_steps', 'whatsapp_leads', 'case_studies'] as $t) {
        if (!migration_table_exists($pdo, $t)) {
            return true;
        }
    }
    if (!migration_column_exists($pdo, 'pages', 'focus_keyword')) {
        return true;
    }
    if (!migration_table_exists($pdo, 'case_study_services')) {
        return true;
    }
    if (!migration_table_exists($pdo, 'site_popup')) {
        return true;
    }
    return false;
}

/** Admin-manageable Departments/Services tags for case studies, ordered for display. */
function get_case_study_services(PDO $pdo): array
{
    return $pdo->query('SELECT id, name FROM case_study_services ORDER BY sort_order, name')->fetchAll();
}

/** Recent published blog posts for the sticky sidebar shown on blog post and case study pages. */
function get_recent_blog_posts(PDO $pdo, int $excludeId = 0, int $limit = 4): array
{
    $limit = max(1, $limit);
    $stmt = $pdo->prepare(
        "SELECT id, title, slug, excerpt FROM blogs WHERE status = 'published' AND id <> ? ORDER BY created_at DESC LIMIT $limit"
    );
    $stmt->execute([$excludeId]);
    return $stmt->fetchAll();
}

/** Settings for the site-wide "on open" consultation popup, admin-managed via admin/popup.php. */
function get_site_popup(PDO $pdo): array
{
    $row = $pdo->query('SELECT * FROM site_popup WHERE id = 1')->fetch();
    return $row ?: [
        'enabled' => 0, 'image' => '', 'image_alt' => '', 'title' => '', 'description' => '',
        'points' => '', 'cta_text' => 'Book a Free Consultation', 'cta_use_booking' => 1, 'cta_link' => '',
    ];
}

// ── Booking system ──

function get_booking_availability(PDO $pdo): array
{
    $row = $pdo->query('SELECT * FROM booking_availability WHERE id = 1')->fetch();
    return $row ?: [
        'days_of_week' => '1,2,3,4,5', 'start_time' => '10:00:00', 'end_time' => '18:00:00',
        'slot_interval_minutes' => 30, 'range_start' => date('Y-m-d'), 'range_end' => date('Y-m-d', strtotime('+60 days')),
    ];
}

function get_booking_notification_emails(PDO $pdo): array
{
    return $pdo->query('SELECT id, email FROM booking_notification_emails ORDER BY id')->fetchAll();
}

/** @return array<int,array> Fields ordered for display, with 'options' already JSON-decoded. */
function get_booking_form_fields(PDO $pdo): array
{
    $rows = $pdo->query('SELECT * FROM booking_form_fields ORDER BY sort_order, id')->fetchAll();
    foreach ($rows as &$r) {
        $r['options'] = $r['options'] ? json_decode($r['options'], true) : [];
    }
    return $rows;
}

/** Server-side re-validation that a specific date+time is a real, bookable slot. */
function is_booking_slot_valid(PDO $pdo, string $date, string $time): bool
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^\d{2}:\d{2}$/', $time)) {
        return false;
    }

    $a = get_booking_availability($pdo);
    $daysRaw = trim((string) $a['days_of_week']);
    $allowedDays = $daysRaw === '' ? [] : array_map('intval', explode(',', $daysRaw));
    $dayOfWeek = (int) date('w', strtotime($date));

    if ($date < (string) $a['range_start'] || $date > (string) $a['range_end']) {
        return false;
    }
    if (!in_array($dayOfWeek, $allowedDays, true)) {
        return false;
    }

    $startTs = strtotime($date . ' ' . $a['start_time']);
    $endTs = strtotime($date . ' ' . $a['end_time']);
    $slotTs = strtotime($date . ' ' . $time . ':00');
    $interval = max(5, (int) $a['slot_interval_minutes']);

    if ($slotTs === false || $slotTs < $startTs || $slotTs + $interval * 60 > $endTs) {
        return false;
    }
    if (($slotTs - $startTs) % ($interval * 60) !== 0) {
        return false;
    }
    if ($slotTs < time() + 60 * 60) {
        return false;
    }

    return true;
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

// ── SEO (Rank-Math-style on-page SEO panel) ──

/** Scheme + host of the current request, e.g. "https://drawlead.com" — used to build absolute canonical/OG URLs. */
function site_base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'drawlead.com';
    return $scheme . $host;
}

/**
 * Emits the full SEO <head> block for one page: title, meta description,
 * robots, canonical link, Open Graph + Twitter Card tags, and an optional
 * JSON-LD schema script. Called once from templates/layout-start.php.
 *
 * Expected $seo keys: title, description, canonical, robots_index
 * ('index'|'noindex'), robots_follow ('follow'|'nofollow'), og_title,
 * og_description, og_image, og_type ('website'|'article'), schema
 * (array|null, ready for json_encode).
 */
function seo_head_tags(array $seo): string
{
    $title = $seo['title'] ?? '';
    $description = $seo['description'] ?? '';
    $canonical = $seo['canonical'] ?? '';
    $robotsIndex = ($seo['robots_index'] ?? 'index') === 'noindex' ? 'noindex' : 'index';
    $robotsFollow = ($seo['robots_follow'] ?? 'follow') === 'nofollow' ? 'nofollow' : 'follow';
    $ogType = $seo['og_type'] ?? 'website';
    $ogTitle = ($seo['og_title'] ?? '') !== '' ? $seo['og_title'] : $title;
    $ogDescription = ($seo['og_description'] ?? '') !== '' ? $seo['og_description'] : $description;
    $ogImage = $seo['og_image'] ?? '';

    $out = '<title>' . h($title) . "</title>\n";
    if ($description !== '') {
        $out .= '<meta name="description" content="' . h($description) . "\">\n";
    }
    $out .= '<meta name="robots" content="' . h($robotsIndex . ',' . $robotsFollow) . "\">\n";
    if ($canonical !== '') {
        $out .= '<link rel="canonical" href="' . h($canonical) . "\">\n";
    }

    $out .= '<meta property="og:type" content="' . h($ogType) . "\">\n";
    $out .= '<meta property="og:title" content="' . h($ogTitle) . "\">\n";
    if ($ogDescription !== '') {
        $out .= '<meta property="og:description" content="' . h($ogDescription) . "\">\n";
    }
    if ($canonical !== '') {
        $out .= '<meta property="og:url" content="' . h($canonical) . "\">\n";
    }
    if ($ogImage !== '') {
        $out .= '<meta property="og:image" content="' . h($ogImage) . "\">\n";
    }
    $out .= '<meta name="twitter:card" content="' . ($ogImage !== '' ? 'summary_large_image' : 'summary') . "\">\n";
    $out .= '<meta name="twitter:title" content="' . h($ogTitle) . "\">\n";
    if ($ogDescription !== '') {
        $out .= '<meta name="twitter:description" content="' . h($ogDescription) . "\">\n";
    }
    if ($ogImage !== '') {
        $out .= '<meta name="twitter:image" content="' . h($ogImage) . "\">\n";
    }

    if (!empty($seo['schema'])) {
        $out .= '<script type="application/ld+json">'
              . json_encode($seo['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
              . "</script>\n";
    }

    return $out;
}

/**
 * Builds the $seo array (see seo_head_tags()) from a pages/blogs/case_studies
 * row that carries the shared SEO columns (meta_title, meta_description,
 * canonical_url, robots_index, robots_follow, og_title, og_description,
 * og_image). $fallbackTitle/$fallbackDescription cover a blank meta_title/
 * meta_description. $canonicalPath is this content's own URL path, used
 * when canonical_url is left blank.
 */
function build_seo_from_row(array $row, string $canonicalPath, string $fallbackTitle, string $fallbackDescription, string $ogType, ?array $schema = null): array
{
    return [
        'title' => $row['meta_title'] ?: $fallbackTitle,
        'description' => $row['meta_description'] ?: $fallbackDescription,
        'canonical' => $row['canonical_url'] ?: (site_base_url() . $canonicalPath),
        'robots_index' => $row['robots_index'] ?? 'index',
        'robots_follow' => $row['robots_follow'] ?? 'follow',
        'og_title' => $row['og_title'] ?? '',
        'og_description' => $row['og_description'] ?? '',
        'og_image' => $row['og_image'] ?? '',
        'og_type' => $ogType,
        'schema' => $schema,
    ];
}

/** Article schema.org JSON-LD for a blog post or case study. */
function article_schema(string $headline, string $description, string $canonicalPath, string $imageFile, string $authorName, string $createdAt, string $updatedAt): array
{
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $headline,
        'description' => $description,
        'author' => ['@type' => 'Person', 'name' => $authorName ?: 'Drawlead'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Drawlead'],
        'datePublished' => date('c', strtotime($createdAt)),
        'dateModified' => date('c', strtotime($updatedAt)),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => site_base_url() . $canonicalPath],
    ];
    if ($imageFile !== '') {
        $schema['image'] = [site_base_url() . UPLOAD_URL . $imageFile];
    }
    return $schema;
}
