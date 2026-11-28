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
require_once __DIR__ . '/includes/os-leads.php';

// No cron on shared hosting — flip any due scheduled posts to published
// on every request, so they go live the moment their time arrives.
publish_due_scheduled_posts($pdo);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = rawurldecode($uri);
$uri = rtrim($uri, '/');
if ($uri === '') {
    $uri = '/';
}

// ── Admin → SEO → Redirects: applied before any route, so an old URL can
// point anywhere — even over a page that still exists. 410 = "gone". ──
$seoRedirect = seo_find_redirect($pdo, $uri, true);
$seoGone = false;
if ($seoRedirect) {
    if ((int) $seoRedirect['code'] === 410) {
        $seoGone = true;
    } else {
        header('Location: ' . $seoRedirect['location'], true, (int) $seoRedirect['code']);
        exit;
    }
}

// ── Admin → SEO → 404 Logs: whatever route ends in a 404, record it. ──
register_shutdown_function(function () use ($pdo, $uri) {
    if (http_response_code() === 404 && !isset($_GET['pc_preview'])) {
        seo_log_404($pdo, $uri);
    }
});

// ── Drawlead OS: once the page is out, send the OS any leads it's missing.
// Registered after the 404 log so that still runs first. See includes/os-leads.php. ──
register_shutdown_function('os_leads_catch_up_after_response', $pdo);

// ── robots.txt — edited in Admin → SEO → Robots (.htaccess routes it here). ──
if ($uri === '/robots.txt') {
    header('Content-Type: text/plain; charset=UTF-8');
    echo seo_robots_txt($pdo);
    exit;
}

// ── XML sitemap — every published page, blog post, and case study, excluding
// anything marked noindex in its SEO panel or excluded in Admin → SEO → Sitemap. ──
if ($uri === '/sitemap.xml') {
    if (!seo_sitemap_settings($pdo)['enabled']) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "The sitemap is switched off.\n";
        exit;
    }
    header('Content-Type: application/xml; charset=UTF-8');
    echo seo_sitemap_xml($pdo);
    exit;
}

// Admin → SEO → Image SEO: give <img> tags with no alt text one from the file
// name. Not in the page editor's live preview, which needs the exact markup.
if (!isset($_GET['pc_preview']) && seo_image_settings($pdo)['auto_alt']) {
    ob_start('seo_auto_alt_filter');
}

// Shared by any route that hits a 404 — never index a missing page.
$notFoundSeo = [
    'title' => 'Page Not Found | Drawlead', 'description' => '',
    'canonical' => '', 'robots_index' => 'noindex', 'robots_follow' => 'nofollow',
    'og_title' => '', 'og_description' => '', 'og_image' => '', 'og_type' => 'website', 'schema' => null,
];

// A 410 redirect: the content was removed on purpose.
if ($seoGone) {
    http_response_code(410);
    $seo = $notFoundSeo;
    include __DIR__ . '/templates/layout-start.php';
    include __DIR__ . '/templates/404.php';
    include __DIR__ . '/templates/layout-end.php';
    exit;
}

// ── Blog routes ──
if ($uri === '/blog') {
    $posts = $pdo->query(
        "SELECT id, title, slug, excerpt, featured_image, featured_image_alt, created_at
         FROM blogs WHERE status = 'published' ORDER BY created_at DESC"
    )->fetchAll();

    $seo = [
        'title' => get_setting($pdo, 'blog_meta_title', 'Blog | Drawlead'),
        'description' => get_setting($pdo, 'blog_meta_description', ''),
        'canonical' => site_base_url() . '/blog',
        'robots_index' => 'index', 'robots_follow' => 'follow',
        'og_title' => '', 'og_description' => '', 'og_image' => '', 'og_type' => 'website', 'schema' => null,
    ];

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
        $seo = $notFoundSeo;
        include __DIR__ . '/templates/layout-start.php';
        include __DIR__ . '/templates/404.php';
        include __DIR__ . '/templates/layout-end.php';
        exit;
    }

    $recentPosts = get_recent_blog_posts($pdo, (int) $post['id']);

    $canonicalPath = '/blog/' . $post['slug'];
    $seo = build_seo_from_row(
        $post, $canonicalPath, $post['title'], $post['excerpt'], 'article',
        article_schema($post['title'], $post['meta_description'] ?: $post['excerpt'], $canonicalPath, $post['featured_image'], $post['author_name'] ?? '', $post['created_at'], $post['updated_at'])
    );
    if ($seo['og_image'] === '' && $post['featured_image']) {
        $seo['og_image'] = site_base_url() . UPLOAD_URL . $post['featured_image'];
    }

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

    // Only offer a filter tab for services that at least one published
    // case study actually uses — no point linking to an empty result.
    $usedServiceNames = [];
    foreach ($studies as $s) {
        foreach (array_map('trim', explode(',', $s['services'])) as $sv) {
            if ($sv !== '') {
                $usedServiceNames[$sv] = true;
            }
        }
    }
    $caseStudyServices = array_values(array_filter(
        get_case_study_services($pdo),
        fn ($s) => isset($usedServiceNames[$s['name']])
    ));

    $seo = [
        'title' => 'Case Studies | Drawlead',
        'description' => 'Real results from real clients — see how Drawlead has helped businesses across construction, healthcare, and marketing streamline operations and grow.',
        'canonical' => site_base_url() . '/case-studies',
        'robots_index' => 'index', 'robots_follow' => 'follow',
        'og_title' => '', 'og_description' => '', 'og_image' => '', 'og_type' => 'website', 'schema' => null,
    ];

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
        $seo = $notFoundSeo;
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

    $recentPosts = get_recent_blog_posts($pdo);

    $canonicalPath = '/case-studies/' . $caseStudy['slug'];
    $seo = build_seo_from_row(
        $caseStudy, $canonicalPath, $caseStudy['title'], $caseStudy['description'], 'article',
        article_schema($caseStudy['title'], $caseStudy['meta_description'] ?: $caseStudy['description'], $canonicalPath, $caseStudy['desktop_image'], '', $caseStudy['created_at'], $caseStudy['updated_at'])
    );
    if ($seo['og_image'] === '' && $caseStudy['desktop_image']) {
        $seo['og_image'] = site_base_url() . UPLOAD_URL . $caseStudy['desktop_image'];
    }

    include __DIR__ . '/templates/layout-start.php';
    include __DIR__ . '/templates/case-study-body.php';
    include __DIR__ . '/templates/layout-end.php';
    exit;
}

// ── Standalone booking page (calendar only, no nav/footer) ──
if ($uri === '/book') {
    $seo = [
        'title' => 'Book a Free Consultation | Drawlead',
        'description' => 'Pick a date and time for a free 30-minute consultation call with Drawlead.',
        'canonical' => site_base_url() . '/book',
        'robots_index' => 'index', 'robots_follow' => 'follow',
        'og_title' => '', 'og_description' => '', 'og_image' => '', 'og_type' => 'website', 'schema' => null,
    ];

    include __DIR__ . '/templates/layout-start.php';
    include __DIR__ . '/templates/book-body.php';
    include __DIR__ . '/templates/layout-end.php';
    exit;
}

// ── Drawlead Analyze ──
if ($uri === '/analyze') {
    // Admin-controlled via Admin → Pages (migration 019 adds the row). Falls
    // back to "published" if that row doesn't exist yet, so the tool keeps
    // working in the gap between deploying this code and running the migration.
    $analyzePageStmt = $pdo->prepare('SELECT * FROM pages WHERE slug = ?');
    $analyzePageStmt->execute(['/analyze']);
    $analyzePage = $analyzePageStmt->fetch();

    if ($analyzePage && ($analyzePage['status'] ?? 'published') === 'draft') {
        http_response_code(404);
        $seo = $notFoundSeo;
        include __DIR__ . '/templates/layout-start.php';
        include __DIR__ . '/templates/404.php';
        include __DIR__ . '/templates/layout-end.php';
        exit;
    }

    $analyzeError = '';
    $analyzeUrlValue = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $analyzeUrlValue = trim($_POST['url'] ?? '');
        $normalizedUrl = analyze_normalize_url($analyzeUrlValue);

        if ($normalizedUrl === '' || !filter_var($normalizedUrl, FILTER_VALIDATE_URL)) {
            $analyzeError = 'Enter a valid website URL, e.g. yourbusiness.com';
        } else {
            $result = analyze_run($normalizedUrl);
            if (!$result['ok']) {
                $analyzeError = $result['error'];
            } else {
                $token = analyze_save_report($pdo, $normalizedUrl, $result);
                header('Location: /analyze/' . $token);
                exit;
            }
        }
    }

    $fallbackTitle = 'Drawlead Analyze — Free CRO Website Analysis';
    $fallbackDescription = 'Enter your website URL and get a free, rule-based conversion-rate-optimization scorecard plus a rebuilt version of your page in a modern, high-converting layout.';
    $seo = $analyzePage
        ? build_seo_from_row($analyzePage, '/analyze', $fallbackTitle, $fallbackDescription, 'website')
        : [
            'title' => $fallbackTitle, 'description' => $fallbackDescription,
            'canonical' => site_base_url() . '/analyze',
            'robots_index' => 'index', 'robots_follow' => 'follow',
            'og_title' => '', 'og_description' => '', 'og_image' => '', 'og_type' => 'website', 'schema' => null,
        ];

    include __DIR__ . '/templates/layout-start.php';
    include __DIR__ . '/templates/analyze-form-body.php';
    include __DIR__ . '/templates/layout-end.php';
    exit;
}

if (preg_match('#^/analyze/([a-zA-Z0-9]{4,32})$#', $uri, $m)) {
    $report = analyze_get_report($pdo, $m[1]);

    if (!$report) {
        http_response_code(404);
        $seo = $notFoundSeo;
        include __DIR__ . '/templates/layout-start.php';
        include __DIR__ . '/templates/404.php';
        include __DIR__ . '/templates/layout-end.php';
        exit;
    }

    $reportDomain = (string) (parse_url($report['target_url'], PHP_URL_HOST) ?: $report['target_url']);
    $seo = [
        'title' => $reportDomain . ' — CRO Analysis | Drawlead Analyze',
        'description' => 'Free CRO scorecard and rebuilt page preview for ' . $reportDomain . ', generated by Drawlead Analyze.',
        'canonical' => site_base_url() . '/analyze/' . $report['token'],
        'robots_index' => 'noindex', 'robots_follow' => 'follow',
        'og_title' => '', 'og_description' => '', 'og_image' => '', 'og_type' => 'website', 'schema' => null,
    ];

    include __DIR__ . '/templates/layout-start.php';
    include __DIR__ . '/templates/analyze-report-body.php';
    include __DIR__ . '/templates/layout-end.php';
    exit;
}

// ── Generic database-driven pages (Home, About Us, ...) ──
$stmt = $pdo->prepare('SELECT * FROM pages WHERE slug = ?');
$stmt->execute([$uri]);
$page = $stmt->fetch();

// The live preview in Admin → Pages → Edit → Page Content loads the page with
// ?pc_preview=1. For a logged-in editor of this page only, it tags editable
// items for the editor's script and shows the page even while it's a draft.
$pcPreview = false;
if ($page && isset($_GET['pc_preview'])) {
    $pcUser = current_user();
    $pcPreview = $pcUser !== null
        && user_has_page_access($pdo, (int) $pcUser['id'], (string) $pcUser['role'], (int) $page['id']);
}

if ($page && !$pcPreview && ($page['status'] ?? 'published') === 'draft') {
    $page = false;
}

if (!$page) {
    http_response_code(404);
    $seo = $notFoundSeo;
    include __DIR__ . '/templates/layout-start.php';
    include __DIR__ . '/templates/404.php';
    include __DIR__ . '/templates/layout-end.php';
    exit;
}

$seo = build_seo_from_row(
    $page, $uri, $page['name'], '', 'website',
    ['@context' => 'https://schema.org', '@type' => 'WebPage', 'name' => $page['meta_title'] ?: $page['name'], 'description' => $page['meta_description'], 'url' => site_base_url() . $uri]
);

$templateFile = __DIR__ . '/templates/' . basename($page['template']) . '-body.php';
if (!is_file($templateFile)) {
    http_response_code(500);
    die('This page has no template configured.');
}

if ($pcPreview) {
    $seo['robots_index'] = 'noindex';
    header('X-Robots-Tag: noindex, nofollow');
    header('Cache-Control: no-store, private');
}

include __DIR__ . '/templates/layout-start.php';
// Text/image edits and section layout from Admin → Pages → Edit → Page Content
// are spliced into the template's output; with none saved it passes through as is.
// &pc_version=ID (with pc_preview): the editor's History shows that earlier
// version exactly as it looked live — read-only, so no editing markers.
$pcVersionOverrides = null;
if ($pcPreview && isset($_GET['pc_version'])) {
    $pcVersion = pc_version_rows($pdo, (int) $page['id'], (int) $_GET['pc_version']);
    $pcVersionOverrides = $pcVersion ? pc_rows_to_overrides($pcVersion['rows']) : [];
}
ob_start();
include $templateFile;
echo page_content_apply($pdo, (int) $page['id'], (string) ob_get_clean(), $pcPreview && $pcVersionOverrides === null, $pcVersionOverrides);
include __DIR__ . '/templates/layout-end.php';
