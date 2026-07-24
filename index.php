<?php
/**
 * Front controller for the whole public site.
 * .htaccess routes every request that isn't a real file/directory here.
 * It looks the requested path up in the `pages` table (or the `blogs`
 * table for /blog and /blog/{slug}) and renders the matching template —
 * so editing a page's name/URL/meta fields in the admin changes the
 * live site immediately, the way WordPress permalinks work.
 */
require_once __DIR__ . '/includes/bootstrap.php';

// No cron on shared hosting — flip any due scheduled posts to published
// on every request, so they go live the moment their time arrives.
publish_due_scheduled_posts($pdo);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = rawurldecode($uri);
$uri = rtrim($uri, '/');
if ($uri === '') {
    $uri = '/';
}

// ── Blog routes ──
if ($uri === '/blog') {
    $metaTitle = get_setting($pdo, 'blog_meta_title', 'Blog | Drawlead');
    $metaDescription = get_setting($pdo, 'blog_meta_description', '');
    $posts = $pdo->query(
        "SELECT id, title, slug, excerpt, featured_image, featured_image_alt, created_at
         FROM blogs WHERE status = 'published' ORDER BY created_at DESC"
    )->fetchAll();

    include __DIR__ . '/templates/layout-start.php';
    include __DIR__ . '/templates/blog-list-body.php';
    include __DIR__ . '/templates/layout-end.php';
    exit;
}

if (strpos($uri, '/blog/') === 0) {
    $slug = substr($uri, strlen('/blog/'));
    $stmt = $pdo->prepare("SELECT b.*, u.name AS author_name FROM blogs b
                            LEFT JOIN users u ON u.id = b.author_id
                            WHERE b.slug = ? AND b.status = 'published'");
    $stmt->execute([$slug]);
    $post = $stmt->fetch();

    if (!$post) {
        http_response_code(404);
        include __DIR__ . '/templates/layout-start.php';
        include __DIR__ . '/templates/404.php';
        include __DIR__ . '/templates/layout-end.php';
        exit;
    }

    $metaTitle = $post['meta_title'] ?: $post['title'];
    $metaDescription = $post['meta_description'] ?: $post['excerpt'];

    include __DIR__ . '/templates/layout-start.php';
    include __DIR__ . '/templates/blog-post-body.php';
    include __DIR__ . '/templates/layout-end.php';
    exit;
}

// ── Case study routes ──
if ($uri === '/case-studies') {
    $studies = $pdo->query(
        "SELECT id, title, slug, client_name, description, services, desktop_image
         FROM case_studies WHERE status = 'published' ORDER BY created_at DESC"
    )->fetchAll();

    $metaTitle = 'Case Studies | Drawlead';
    $metaDescription = 'Real results from real clients — see how Drawlead has helped businesses across construction, healthcare, and marketing streamline operations and grow.';

    include __DIR__ . '/templates/layout-start.php';
    include __DIR__ . '/templates/case-study-list-body.php';
    include __DIR__ . '/templates/layout-end.php';
    exit;
}

if (strpos($uri, '/case-studies/') === 0) {
    $slug = substr($uri, strlen('/case-studies/'));
    $stmt = $pdo->prepare("SELECT * FROM case_studies WHERE slug = ? AND status = 'published'");
    $stmt->execute([$slug]);
    $caseStudy = $stmt->fetch();

    if (!$caseStudy) {
        http_response_code(404);
        include __DIR__ . '/templates/layout-start.php';
        include __DIR__ . '/templates/404.php';
        include __DIR__ . '/templates/layout-end.php';
        exit;
    }

    $moreStmt = $pdo->prepare(
        "SELECT id, title, slug, client_name, description, services, desktop_image
         FROM case_studies WHERE status = 'published' AND id <> ? ORDER BY created_at DESC LIMIT 3"
    );
    $moreStmt->execute([$caseStudy['id']]);
    $moreCaseStudies = $moreStmt->fetchAll();

    $metaTitle = $caseStudy['meta_title'] ?: $caseStudy['title'];
    $metaDescription = $caseStudy['meta_description'] ?: $caseStudy['description'];

    include __DIR__ . '/templates/layout-start.php';
    include __DIR__ . '/templates/case-study-body.php';
    include __DIR__ . '/templates/layout-end.php';
    exit;
}

// ── Generic database-driven pages (Home, About Us, ...) ──
$stmt = $pdo->prepare('SELECT * FROM pages WHERE slug = ?');
$stmt->execute([$uri]);
$page = $stmt->fetch();

if (!$page) {
    http_response_code(404);
    $metaTitle = 'Page Not Found | Drawlead';
    $metaDescription = '';
    include __DIR__ . '/templates/layout-start.php';
    include __DIR__ . '/templates/404.php';
    include __DIR__ . '/templates/layout-end.php';
    exit;
}

$metaTitle = $page['meta_title'];
$metaDescription = $page['meta_description'];

$templateFile = __DIR__ . '/templates/' . basename($page['template']) . '-body.php';
if (!is_file($templateFile)) {
    http_response_code(500);
    die('This page has no template configured.');
}

include __DIR__ . '/templates/layout-start.php';
include $templateFile;
include __DIR__ . '/templates/layout-end.php';
