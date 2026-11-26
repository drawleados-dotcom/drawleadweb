<?php
/**
 * SEO module — everything behind Admin → SEO.
 *
 * Settings live in the existing `settings` table as JSON under seo_* keys, so
 * Global Settings, Schema, Sitemap, Robots and Image SEO work without a
 * migration. Redirects, 404 Logs and custom schema blocks need the three
 * tables from migration 031 (seo_redirects, seo_404_log, seo_schemas).
 *
 * The public site uses this file from index.php (redirects, 404 logging,
 * /robots.txt, /sitemap.xml, missing-alt filter) and from
 * templates/layout-start.php (global tags + JSON-LD). Every public-facing
 * call is wrapped so a missing table or bad setting can never take a page down.
 */

// ── Admin menu ──

/** SEO submenu: activeNav key => [label, file]. Rendered by admin/includes/header.php. */
function seo_admin_nav(): array
{
    return [
        'seo' => ['SEO Overview', 'seo.php'],
        'seo-pages' => ['Page SEO', 'seo-pages.php'],
        'seo-settings' => ['Global Settings', 'seo-settings.php'],
        'seo-schema' => ['Schema', 'seo-schema.php'],
        'seo-sitemap' => ['Sitemap', 'seo-sitemap.php'],
        'seo-robots' => ['Robots', 'seo-robots.php'],
        'seo-redirects' => ['Redirects', 'seo-redirects.php'],
        'seo-404' => ['404 Logs', 'seo-404.php'],
        'seo-images' => ['Image SEO', 'seo-images.php'],
        'seo-audit' => ['SEO Audit', 'seo-audit.php'],
        'seo-bulk' => ['Bulk Editor', 'seo-bulk.php'],
    ];
}

/** True once migration 031 has created $table. */
function seo_table_ready(PDO $pdo, string $table): bool
{
    static $cache = [];
    return $cache[$table] ??= migration_table_exists($pdo, $table);
}

/** Migration 031 — same SQL as sql/migrations/031_seo_module.sql (sql/ isn't deployed). */
function seo_migration_031_statements(): array
{
    return [
        "CREATE TABLE IF NOT EXISTS seo_redirects (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            source      VARCHAR(500) NOT NULL,
            match_type  VARCHAR(10)  NOT NULL DEFAULT 'exact',
            target      VARCHAR(500) NOT NULL DEFAULT '',
            code        SMALLINT     NOT NULL DEFAULT 301,
            enabled     TINYINT(1)   NOT NULL DEFAULT 1,
            note        VARCHAR(190) NOT NULL DEFAULT '',
            hits        INT          NOT NULL DEFAULT 0,
            last_hit_at DATETIME     NULL,
            created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
            KEY idx_source (source(191))
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS seo_404_log (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            path        VARCHAR(500) NOT NULL,
            path_hash   CHAR(40)     NOT NULL,
            hits        INT          NOT NULL DEFAULT 1,
            referrer    VARCHAR(500) NOT NULL DEFAULT '',
            user_agent  VARCHAR(255) NOT NULL DEFAULT '',
            is_bot      TINYINT(1)   NOT NULL DEFAULT 0,
            first_seen  DATETIME     NULL,
            last_seen   DATETIME     NULL,
            UNIQUE KEY uniq_path (path_hash),
            KEY idx_last_seen (last_seen)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS seo_schemas (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            name        VARCHAR(190) NOT NULL DEFAULT '',
            target      TEXT,
            schema_json MEDIUMTEXT   NOT NULL,
            enabled     TINYINT(1)   NOT NULL DEFAULT 1,
            created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];
}

function seo_module_ready(PDO $pdo): bool
{
    return seo_table_ready($pdo, 'seo_redirects')
        && seo_table_ready($pdo, 'seo_404_log')
        && seo_table_ready($pdo, 'seo_schemas');
}

/** Alert shown on pages that need migration 031. */
function seo_migration_notice(): string
{
    return '<div class="alert alert-error">This needs migration 031 (SEO module). '
        . '<a href="run-migrations.php" style="color:inherit">Open Migrations</a> and run it — it only adds new tables, nothing on the live site changes.</div>';
}

// ── JSON settings (settings table, seo_* keys) ──

/** All seo_* settings, loaded in one query per request. */
function seo_settings_raw(PDO $pdo, bool $refresh = false): array
{
    static $cache = null;
    if ($cache === null || $refresh) {
        $cache = [];
        try {
            $stmt = $pdo->query("SELECT `key`, `value` FROM settings WHERE `key` LIKE 'seo\\_%'");
            foreach ($stmt->fetchAll() as $r) {
                $cache[$r['key']] = (string) $r['value'];
            }
        } catch (PDOException $e) {
            error_log('seo_settings_raw: ' . $e->getMessage());
        }
    }
    return $cache;
}

function seo_json_setting(PDO $pdo, string $key, array $defaults): array
{
    $raw = seo_settings_raw($pdo)[$key] ?? '';
    $data = $raw !== '' ? json_decode($raw, true) : null;
    if (!is_array($data)) {
        return $defaults;
    }
    // Only known keys, each keeping its default's type.
    $out = $defaults;
    foreach ($defaults as $k => $d) {
        if (!array_key_exists($k, $data)) {
            continue;
        }
        $v = $data[$k];
        if (is_int($d)) {
            $out[$k] = (int) $v;
        } elseif (is_array($d)) {
            $out[$k] = is_array($v) ? $v : $d;
        } else {
            $out[$k] = is_scalar($v) ? (string) $v : $d;
        }
    }
    return $out;
}

function seo_save_json_setting(PDO $pdo, string $key, array $data): void
{
    set_setting($pdo, $key, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    seo_settings_raw($pdo, true);
}

function seo_global_defaults(): array
{
    return [
        'site_name' => 'Drawlead',
        'separator' => '|',
        'append_site_name' => 0,
        'default_description' => '',
        'default_og_image' => '',
        'twitter_handle' => '',
        'facebook_app_id' => '',
        'bing_verification' => '',
        'yandex_verification' => '',
        'pinterest_verification' => '',
        'baidu_verification' => '',
    ];
}

function seo_global(PDO $pdo): array
{
    return seo_json_setting($pdo, 'seo_global', seo_global_defaults());
}

function seo_schema_defaults(): array
{
    return [
        'org_enabled' => 0,
        'org_type' => 'Organization',
        'org_name' => 'Drawlead',
        'org_url' => '',
        'org_logo' => '',
        'org_description' => '',
        'org_email' => '',
        'org_phone' => '',
        'street' => '',
        'city' => '',
        'region' => '',
        'postal_code' => '',
        'country' => '',
        'same_as' => '',
        'website_enabled' => 0,
        'breadcrumbs_enabled' => 0,
        'article_type' => 'Article',
    ];
}

function seo_schema_settings(PDO $pdo): array
{
    return seo_json_setting($pdo, 'seo_schema', seo_schema_defaults());
}

/** Organization types offered on the Schema page. */
function seo_org_types(): array
{
    return [
        'Organization' => 'Organization',
        'Corporation' => 'Corporation',
        'LocalBusiness' => 'Local Business',
        'ProfessionalService' => 'Professional Service',
        'ITService' => 'IT Service (Local Business)',
        'OnlineBusiness' => 'Online Business',
    ];
}

function seo_sitemap_defaults(): array
{
    return [
        'enabled' => 1,
        'include_pages' => 1,
        'include_blogs' => 1,
        'include_case_studies' => 1,
        'include_images' => 1,
        'exclude' => '',
        'home_priority' => '1.0', 'home_changefreq' => 'weekly',
        'page_priority' => '0.8', 'page_changefreq' => 'monthly',
        'blog_priority' => '0.6', 'blog_changefreq' => 'monthly',
        'case_study_priority' => '0.6', 'case_study_changefreq' => 'monthly',
    ];
}

function seo_sitemap_settings(PDO $pdo): array
{
    return seo_json_setting($pdo, 'seo_sitemap', seo_sitemap_defaults());
}

function seo_changefreqs(): array
{
    return ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];
}

function seo_robots_defaults(): array
{
    return ['content' => '', 'block_all' => 0];
}

function seo_robots_settings(PDO $pdo): array
{
    return seo_json_setting($pdo, 'seo_robots', seo_robots_defaults());
}

function seo_404_defaults(): array
{
    return ['enabled' => 1, 'ignore_bots' => 0, 'retention_days' => 90];
}

function seo_404_settings(PDO $pdo): array
{
    return seo_json_setting($pdo, 'seo_404', seo_404_defaults());
}

function seo_image_defaults(): array
{
    return ['auto_alt' => 0];
}

function seo_image_settings(PDO $pdo): array
{
    return seo_json_setting($pdo, 'seo_images', seo_image_defaults());
}

// ── URL helpers ──

/** "/x" → "https://host/x"; absolute URLs and '' pass through. */
function seo_absolute_url(string $url): string
{
    $url = trim($url);
    if ($url === '' || preg_match('#^https?://#i', $url) || str_starts_with($url, 'data:')) {
        return $url;
    }
    if (str_starts_with($url, '//')) {
        return 'https:' . $url;
    }
    return site_base_url() . '/' . ltrim($url, '/');
}

/**
 * Normalizes what an admin typed as a site path: a full URL on any host is
 * cut down to its path, the query and fragment are dropped, it always starts
 * with "/" and never ends with one (matching how index.php builds $uri).
 * Returns '' for nothing usable.
 */
function seo_normalize_path(string $input): string
{
    $input = trim($input);
    if ($input === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $input)) {
        $input = (string) (parse_url($input, PHP_URL_PATH) ?? '');
    }
    $input = (string) preg_replace('/[?#].*$/s', '', $input);
    $input = trim(rawurldecode($input));
    $input = '/' . ltrim($input, '/');
    $input = rtrim($input, '/');
    return $input === '' ? '/' : $input;
}

/**
 * "*" = every page, "/blog/*" = /blog and everything under it,
 * "/industry-*" = every path starting with /industry-, otherwise an exact path.
 * Case-insensitive, like the database lookups.
 */
function seo_path_matches(string $pattern, string $uri): bool
{
    $pattern = trim($pattern);
    if ($pattern === '') {
        return false;
    }
    if ($pattern === '*' || $pattern === '/*') {
        return true;
    }
    if (str_ends_with($pattern, '/*')) {
        $base = seo_normalize_path(substr($pattern, 0, -2));
        return strcasecmp($uri, $base) === 0 || stripos($uri, $base . '/') === 0;
    }
    if (str_ends_with($pattern, '*')) {
        $base = '/' . ltrim(substr($pattern, 0, -1), '/');
        return stripos($uri, $base) === 0;
    }
    return strcasecmp(seo_normalize_path($pattern), $uri) === 0;
}

// ── <head>: global settings + JSON-LD (templates/layout-start.php) ──

/** Fills the per-page $seo array from Global Settings before seo_head_tags() prints it. */
function seo_apply_globals(PDO $pdo, array $seo, string $uri): array
{
    try {
        $g = seo_global($pdo);
        $siteName = trim($g['site_name']);

        if ((string) ($seo['description'] ?? '') === '' && ($seo['robots_index'] ?? 'index') !== 'noindex') {
            $seo['description'] = $g['default_description'];
        }
        if ($g['append_site_name'] && $siteName !== '' && (string) ($seo['title'] ?? '') !== ''
            && stripos((string) $seo['title'], $siteName) === false) {
            $sep = trim($g['separator']) !== '' ? trim($g['separator']) : '|';
            $seo['title'] = $seo['title'] . ' ' . $sep . ' ' . $siteName;
        }
        if ((string) ($seo['og_image'] ?? '') === '') {
            $seo['og_image'] = $g['default_og_image'];
        }
        $seo['og_image'] = seo_absolute_url((string) ($seo['og_image'] ?? ''));

        // Robots → "Discourage search engines" switches the whole site to noindex.
        if (seo_robots_settings($pdo)['block_all']) {
            $seo['robots_index'] = 'noindex';
        }

        // Blog posts: Article → BlogPosting / NewsArticle, as chosen on the Schema page.
        $articleType = seo_schema_settings($pdo)['article_type'];
        if (str_starts_with($uri, '/blog/') && is_array($seo['schema'] ?? null)
            && ($seo['schema']['@type'] ?? '') === 'Article'
            && in_array($articleType, ['Article', 'BlogPosting', 'NewsArticle'], true)) {
            $seo['schema']['@type'] = $articleType;
        }
    } catch (Throwable $e) {
        error_log('seo_apply_globals: ' . $e->getMessage());
    }
    return $seo;
}

function seo_json_ld(array $schema): string
{
    $json = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return '';
    }
    // Never let a value close the <script> element.
    $json = str_ireplace('</script', '<\/script', $json);
    return '<script type="application/ld+json">' . $json . "</script>\n";
}

/** Extra <head> tags printed after seo_head_tags(): site name, verification codes, global + custom schema. */
function seo_extra_head_tags(PDO $pdo, array $seo, string $uri): string
{
    try {
        $g = seo_global($pdo);
        $out = '';
        if (trim($g['site_name']) !== '') {
            $out .= '<meta property="og:site_name" content="' . h(trim($g['site_name'])) . "\">\n";
        }
        $handle = trim($g['twitter_handle']);
        if ($handle !== '') {
            $out .= '<meta name="twitter:site" content="' . h('@' . ltrim($handle, '@')) . "\">\n";
        }
        if (trim($g['facebook_app_id']) !== '') {
            $out .= '<meta property="fb:app_id" content="' . h(trim($g['facebook_app_id'])) . "\">\n";
        }
        $verify = [
            'bing_verification' => 'msvalidate.01',
            'yandex_verification' => 'yandex-verification',
            'pinterest_verification' => 'p:domain_verify',
            'baidu_verification' => 'baidu-site-verification',
        ];
        foreach ($verify as $key => $metaName) {
            if (trim($g[$key]) !== '') {
                $out .= '<meta name="' . $metaName . '" content="' . h(trim($g[$key])) . "\">\n";
            }
        }

        // No schema on 404 / 410 pages.
        if (in_array(http_response_code(), [404, 410], true)) {
            return $out;
        }

        foreach (seo_schemas_for_uri($pdo, $uri, (string) ($seo['title'] ?? '')) as $schema) {
            $out .= seo_json_ld($schema);
        }
        return $out;
    } catch (Throwable $e) {
        error_log('seo_extra_head_tags: ' . $e->getMessage());
        return '';
    }
}

/** Global (Organization, WebSite, breadcrumbs) and custom schema blocks that apply to $uri. */
function seo_schemas_for_uri(PDO $pdo, string $uri, string $pageTitle): array
{
    $s = seo_schema_settings($pdo);
    $out = [];
    if ($s['org_enabled'] && ($org = seo_organization_schema($s))) {
        $out[] = $org;
    }
    if ($s['website_enabled']) {
        $out[] = seo_website_schema($pdo, $s);
    }
    if ($s['breadcrumbs_enabled'] && $uri !== '/' && ($bc = seo_breadcrumb_schema($uri, $pageTitle))) {
        $out[] = $bc;
    }
    foreach (seo_custom_schemas_for($pdo, $uri) as $c) {
        $out[] = $c;
    }
    return $out;
}

function seo_organization_schema(array $s): ?array
{
    $name = trim($s['org_name']);
    if ($name === '') {
        return null;
    }
    $type = array_key_exists($s['org_type'], seo_org_types()) ? $s['org_type'] : 'Organization';
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => $type,
        '@id' => site_base_url() . '/#organization',
        'name' => $name,
        'url' => trim($s['org_url']) !== '' ? trim($s['org_url']) : site_base_url() . '/',
    ];
    if (trim($s['org_logo']) !== '') {
        $schema['logo'] = seo_absolute_url(trim($s['org_logo']));
        if (in_array($type, ['LocalBusiness', 'ProfessionalService', 'ITService'], true)) {
            $schema['image'] = $schema['logo'];
        }
    }
    foreach (['org_description' => 'description', 'org_email' => 'email', 'org_phone' => 'telephone'] as $k => $prop) {
        if (trim($s[$k]) !== '') {
            $schema[$prop] = trim($s[$k]);
        }
    }
    $address = array_filter([
        'streetAddress' => trim($s['street']),
        'addressLocality' => trim($s['city']),
        'addressRegion' => trim($s['region']),
        'postalCode' => trim($s['postal_code']),
        'addressCountry' => trim($s['country']),
    ], fn ($v) => $v !== '');
    if ($address) {
        $schema['address'] = ['@type' => 'PostalAddress'] + $address;
    }
    $sameAs = array_values(array_filter(array_map('trim', preg_split('/\R/', $s['same_as']) ?: []),
        fn ($v) => preg_match('#^https?://#i', $v)));
    if ($sameAs) {
        $schema['sameAs'] = $sameAs;
    }
    return $schema;
}

function seo_website_schema(PDO $pdo, array $s): array
{
    $g = seo_global($pdo);
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => site_base_url() . '/#website',
        'name' => trim($g['site_name']) !== '' ? trim($g['site_name']) : trim($s['org_name']),
        'url' => site_base_url() . '/',
    ];
    if ($s['org_enabled'] && trim($s['org_name']) !== '') {
        $schema['publisher'] = ['@id' => site_base_url() . '/#organization'];
    }
    return $schema;
}

/** Home › Blog › Post title — from the URL path, with the page's own title last. */
function seo_breadcrumb_schema(string $uri, string $pageTitle): ?array
{
    $parts = array_values(array_filter(explode('/', trim($uri, '/')), 'strlen'));
    if (!$parts) {
        return null;
    }
    $labels = ['blog' => 'Blog', 'case-studies' => 'Case Studies'];
    $items = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => site_base_url() . '/']];
    $path = '';
    foreach ($parts as $i => $part) {
        $path .= '/' . $part;
        $isLast = $i === count($parts) - 1;
        $name = $isLast && $pageTitle !== ''
            ? trim((string) preg_replace('/\s[|\-–—]\s[^|\-–—]*$/u', '', $pageTitle))
            : ($labels[$part] ?? ucwords(str_replace('-', ' ', $part)));
        $items[] = ['@type' => 'ListItem', 'position' => $i + 2, 'name' => $name !== '' ? $name : $part, 'item' => site_base_url() . $path];
    }
    return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
}

/** Enabled custom schema blocks (Schema page) whose target matches $uri, decoded. */
function seo_custom_schemas_for(PDO $pdo, string $uri): array
{
    try {
        $rows = $pdo->query('SELECT target, schema_json FROM seo_schemas WHERE enabled = 1 ORDER BY id')->fetchAll();
    } catch (PDOException $e) {
        return []; // migration 031 not run yet
    }
    $out = [];
    foreach ($rows as $r) {
        $matches = false;
        foreach (preg_split('/[\r\n,]+/', (string) $r['target']) ?: [] as $pattern) {
            if (seo_path_matches($pattern, $uri)) {
                $matches = true;
                break;
            }
        }
        if (!$matches) {
            continue;
        }
        $data = json_decode((string) $r['schema_json'], true);
        if (is_array($data)) {
            $out[] = $data;
        }
    }
    return $out;
}

/** Ready-made JSON-LD starting points for the custom schema form. */
function seo_schema_templates(): array
{
    $base = 'https://drawlead.com';
    return [
        'FAQPage' => [
            '@context' => 'https://schema.org', '@type' => 'FAQPage',
            'mainEntity' => [
                ['@type' => 'Question', 'name' => 'Your question?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Your answer.']],
                ['@type' => 'Question', 'name' => 'Another question?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Another answer.']],
            ],
        ],
        'Service' => [
            '@context' => 'https://schema.org', '@type' => 'Service',
            'name' => 'Custom ERP Development', 'serviceType' => 'Software development',
            'description' => 'What the service does.',
            'provider' => ['@type' => 'Organization', 'name' => 'Drawlead', 'url' => $base],
            'areaServed' => 'IN',
        ],
        'Product' => [
            '@context' => 'https://schema.org', '@type' => 'Product',
            'name' => 'Product name', 'description' => 'Product description.', 'image' => $base . '/uploads/media/product.jpg',
            'brand' => ['@type' => 'Brand', 'name' => 'Drawlead'],
            'offers' => ['@type' => 'Offer', 'price' => '0.00', 'priceCurrency' => 'INR', 'availability' => 'https://schema.org/InStock'],
        ],
        'LocalBusiness' => [
            '@context' => 'https://schema.org', '@type' => 'LocalBusiness',
            'name' => 'Drawlead', 'url' => $base, 'telephone' => '+91 00000 00000',
            'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Street', 'addressLocality' => 'City', 'postalCode' => '000000', 'addressCountry' => 'IN'],
            'openingHours' => 'Mo-Fr 10:00-18:00',
        ],
        'HowTo' => [
            '@context' => 'https://schema.org', '@type' => 'HowTo', 'name' => 'How to …',
            'step' => [
                ['@type' => 'HowToStep', 'name' => 'Step 1', 'text' => 'Do this first.'],
                ['@type' => 'HowToStep', 'name' => 'Step 2', 'text' => 'Then do this.'],
            ],
        ],
        'Event' => [
            '@context' => 'https://schema.org', '@type' => 'Event', 'name' => 'Event name',
            'startDate' => date('Y-m-d') . 'T10:00:00+05:30', 'endDate' => date('Y-m-d') . 'T12:00:00+05:30',
            'eventAttendanceMode' => 'https://schema.org/OnlineEventAttendanceMode',
            'location' => ['@type' => 'VirtualLocation', 'url' => $base . '/book'],
            'organizer' => ['@type' => 'Organization', 'name' => 'Drawlead', 'url' => $base],
        ],
        'Review' => [
            '@context' => 'https://schema.org', '@type' => 'Review',
            'itemReviewed' => ['@type' => 'Organization', 'name' => 'Drawlead'],
            'reviewRating' => ['@type' => 'Rating', 'ratingValue' => '5', 'bestRating' => '5'],
            'author' => ['@type' => 'Person', 'name' => 'Client name'],
            'reviewBody' => 'What the client said.',
        ],
        'VideoObject' => [
            '@context' => 'https://schema.org', '@type' => 'VideoObject', 'name' => 'Video title',
            'description' => 'What the video shows.', 'thumbnailUrl' => $base . '/uploads/media/video-thumb.jpg',
            'uploadDate' => date('Y-m-d'), 'contentUrl' => 'https://www.youtube.com/watch?v=XXXX',
        ],
    ];
}

/**
 * Validates pasted JSON-LD. Returns [normalized JSON, error]. Adds @context
 * when it's missing; accepts one object, a list, or an @graph.
 */
function seo_validate_schema_json(string $json): array
{
    $json = trim($json);
    // Accept a pasted <script type="application/ld+json"> wrapper.
    $json = trim((string) preg_replace('#^<script[^>]*>|</script>$#i', '', $json));
    if ($json === '') {
        return ['', 'Paste the JSON-LD for this schema.'];
    }
    $data = json_decode($json, true);
    if (!is_array($data)) {
        return ['', 'That is not valid JSON: ' . json_last_error_msg() . '. Check for a missing comma, quote or bracket.'];
    }
    $isList = array_keys($data) === range(0, count($data) - 1);
    $nodes = $isList ? $data : [$data];
    foreach ($nodes as $i => $node) {
        if (!is_array($node)) {
            return ['', 'Each schema item must be a JSON object.'];
        }
        if (!isset($node['@type']) && !isset($node['@graph'])) {
            return ['', 'Add an "@type" (e.g. "FAQPage") to ' . ($isList ? 'item ' . ($i + 1) : 'the schema') . '.'];
        }
    }
    if (!$isList && !isset($data['@context'])) {
        $data = ['@context' => 'https://schema.org'] + $data;
    }
    return [json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ''];
}

// ── Redirects (index.php, before routing) ──

function seo_redirect_codes(): array
{
    return [
        301 => '301 — Moved permanently',
        302 => '302 — Found (temporary)',
        307 => '307 — Temporary redirect',
        308 => '308 — Permanent redirect',
        410 => '410 — Gone (content deleted)',
    ];
}

/**
 * The redirect for $uri, or null. An exact source wins over a "/old/*"
 * prefix; among prefixes the longest wins. 'location' is where to send the
 * visitor (unset for 410). $countHit bumps the hit counter.
 */
function seo_find_redirect(PDO $pdo, string $uri, bool $countHit = false): ?array
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM seo_redirects WHERE enabled = 1 AND (source = ? OR match_type = 'prefix')");
        $stmt->execute([$uri]);
        $rows = $stmt->fetchAll();
    } catch (PDOException $e) {
        return null; // migration 031 not run yet
    }
    $best = null;
    foreach ($rows as $r) {
        $src = (string) $r['source'];
        if ($r['match_type'] !== 'prefix') {
            if (strcasecmp($src, $uri) === 0) {
                $best = $r;
                break;
            }
            continue;
        }
        if ((strcasecmp($src, $uri) === 0 || stripos($uri, $src . '/') === 0)
            && ($best === null || strlen($src) > strlen((string) $best['source']))) {
            $best = $r;
        }
    }
    if (!$best) {
        return null;
    }
    $best['location'] = seo_redirect_location($best, $uri);
    if ($countHit) {
        try {
            $pdo->prepare('UPDATE seo_redirects SET hits = hits + 1, last_hit_at = NOW() WHERE id = ?')->execute([$best['id']]);
        } catch (PDOException $e) {
            error_log('seo_find_redirect hit: ' . $e->getMessage());
        }
    }
    return $best;
}

/** Target URL for a matched redirect: "/new/*" carries over what followed the prefix; the query string is kept. */
function seo_redirect_location(array $row, string $uri): string
{
    if ((int) $row['code'] === 410) {
        return '';
    }
    $target = trim((string) $row['target']);
    if ($row['match_type'] === 'prefix' && str_ends_with($target, '/*')) {
        $rest = substr($uri, strlen((string) $row['source']));
        $target = rtrim(substr($target, 0, -2), '/') . $rest;
        if ($target === '') {
            $target = '/';
        }
    }
    $query = (string) ($_SERVER['QUERY_STRING'] ?? '');
    if ($query !== '' && !str_contains($target, '?')) {
        $target .= '?' . $query;
    }
    return $target;
}

/** Normalized source from the form: [source, match_type]. "/old/*" = prefix. */
function seo_parse_redirect_source(string $input): array
{
    $input = trim($input);
    $prefix = str_ends_with($input, '*');
    if ($prefix) {
        $input = rtrim(substr($input, 0, -1), '/');
    }
    return [seo_normalize_path($input), $prefix ? 'prefix' : 'exact'];
}

/** Redirect target as typed: a site path ("/new-page", "/new/*") or a full http(s) URL. '' if invalid. */
function seo_clean_redirect_target(string $input): string
{
    $input = trim($input);
    if ($input === '') {
        return '';
    }
    if (preg_match('#^https?://[^\s]+$#i', $input)) {
        return filter_var($input, FILTER_VALIDATE_URL) ? $input : '';
    }
    $wild = str_ends_with($input, '/*');
    $query = '';
    if (($q = strpos($input, '?')) !== false) {
        $query = substr($input, $q);
        $input = substr($input, 0, $q);
    }
    $path = seo_normalize_path($wild ? substr($input, 0, -2) : $input);
    if ($path === '') {
        return '';
    }
    return ($wild ? rtrim($path, '/') . '/*' : $path) . $query;
}

// ── 404 log (index.php shutdown hook) ──

function seo_is_bot(string $ua): bool
{
    return $ua === '' || (bool) preg_match('/bot|crawl|spider|slurp|curl|wget|python|scrapy|httpclient|java\/|go-http|headless|lighthouse|monitor|preview|facebookexternalhit|semrush|ahrefs|mj12|dotbot|petalbot|bytespider|gptbot/i', $ua);
}

/** Records one 404 hit for $uri. Never throws. */
function seo_log_404(PDO $pdo, string $uri): void
{
    try {
        $cfg = seo_404_settings($pdo);
        if (!$cfg['enabled']) {
            return;
        }
        $ua = mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
        $isBot = seo_is_bot($ua) ? 1 : 0;
        if ($isBot && $cfg['ignore_bots']) {
            return;
        }
        $path = mb_substr($uri, 0, 500);
        $ref = mb_substr((string) ($_SERVER['HTTP_REFERER'] ?? ''), 0, 500);
        $pdo->prepare(
            'INSERT INTO seo_404_log (path, path_hash, hits, referrer, user_agent, is_bot, first_seen, last_seen)
             VALUES (?, ?, 1, ?, ?, ?, NOW(), NOW())
             ON DUPLICATE KEY UPDATE hits = hits + 1, last_seen = NOW(),
               referrer = IF(VALUES(referrer) <> \'\', VALUES(referrer), referrer),
               user_agent = VALUES(user_agent), is_bot = VALUES(is_bot)'
        )->execute([$path, sha1(strtolower($path)), $ref, $ua, $isBot]);

        // Housekeeping now and then: drop entries nobody has hit in a while.
        if (random_int(1, 50) === 1) {
            $days = max(7, (int) $cfg['retention_days']);
            $pdo->exec("DELETE FROM seo_404_log WHERE last_seen < DATE_SUB(NOW(), INTERVAL $days DAY)");
        }
    } catch (Throwable $e) {
        // Table missing (migration 031 not run) or DB hiccup — a 404 page must still render.
    }
}

// ── robots.txt ──

function seo_robots_default_content(): string
{
    return "User-agent: *\nDisallow: /admin/\nAllow: /\n\nSitemap: {sitemap}\n";
}

/** The robots.txt body the site serves. {sitemap} becomes this site's /sitemap.xml URL. */
function seo_robots_txt(PDO $pdo): string
{
    $r = seo_robots_settings($pdo);
    if ($r['block_all']) {
        return "# Search engines are blocked: Admin → SEO → Robots → \"Discourage search engines\".\nUser-agent: *\nDisallow: /\n";
    }
    $content = trim($r['content']) !== '' ? $r['content'] : seo_robots_default_content();
    $content = str_replace('{sitemap}', site_base_url() . '/sitemap.xml', $content);
    $content = str_replace("\r\n", "\n", $content);
    return rtrim($content) . "\n";
}

/**
 * Would $userAgent be allowed to crawl $path under $content? Follows
 * Google's rules: the most specific user-agent group applies (else "*"),
 * the longest matching rule wins, Allow wins a tie, "*" and "$" work.
 * Returns ['allowed' => bool, 'rule' => matched line or '', 'group' => agent].
 */
function seo_robots_check(string $content, string $userAgent, string $path): array
{
    $groups = [];
    $current = null;
    $lastWasAgent = false;
    foreach (preg_split('/\R/', $content) ?: [] as $line) {
        $line = trim((string) preg_replace('/#.*$/', '', $line));
        if ($line === '' || !str_contains($line, ':')) {
            continue;
        }
        [$field, $value] = array_map('trim', explode(':', $line, 2));
        $field = strtolower($field);
        if ($field === 'user-agent') {
            if (!$lastWasAgent) {
                $current = count($groups);
                $groups[$current] = ['agents' => [], 'rules' => []];
            }
            $groups[$current]['agents'][] = strtolower($value);
            $lastWasAgent = true;
            continue;
        }
        $lastWasAgent = false;
        if ($current !== null && ($field === 'allow' || $field === 'disallow')) {
            $groups[$current]['rules'][] = [$field, $value];
        }
    }

    $ua = strtolower($userAgent);
    $chosen = null;
    $chosenAgent = '';
    foreach ($groups as $g) {
        foreach ($g['agents'] as $agent) {
            if ($agent !== '*' && $agent !== '' && str_contains($ua, $agent) && strlen($agent) > strlen($chosenAgent)) {
                $chosen = $g;
                $chosenAgent = $agent;
            }
        }
    }
    if ($chosen === null) {
        foreach ($groups as $g) {
            if (in_array('*', $g['agents'], true)) {
                $chosen = $chosen === null ? $g : ['agents' => ['*'], 'rules' => array_merge($chosen['rules'], $g['rules'])];
                $chosenAgent = '*';
            }
        }
    }
    if ($chosen === null) {
        return ['allowed' => true, 'rule' => '', 'group' => ''];
    }

    $best = null;
    $bestLen = -1;
    foreach ($chosen['rules'] as [$type, $pattern]) {
        if ($pattern === '') {
            continue; // "Disallow:" with no value allows everything
        }
        $anchored = str_ends_with($pattern, '$');
        $body = $anchored ? substr($pattern, 0, -1) : $pattern;
        $regex = '#^' . str_replace('\*', '.*', preg_quote($body, '#')) . ($anchored ? '$' : '') . '#';
        if (preg_match($regex, $path)) {
            $len = strlen($pattern);
            if ($len > $bestLen || ($len === $bestLen && $type === 'allow')) {
                $best = [$type, $pattern];
                $bestLen = $len;
            }
        }
    }
    if ($best === null) {
        return ['allowed' => true, 'rule' => '', 'group' => $chosenAgent];
    }
    return ['allowed' => $best[0] === 'allow', 'rule' => ucfirst($best[0]) . ': ' . $best[1], 'group' => $chosenAgent];
}

// ── Sitemap ──

function seo_sitemap_exclusions(array $settings): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $settings['exclude']) ?: []), 'strlen'));
}

/**
 * Every URL the sitemap lists: published, not noindex, not excluded on the
 * Sitemap page. Each entry: path, loc, lastmod, changefreq, priority, type, images.
 */
function seo_sitemap_entries(PDO $pdo, ?array $s = null): array
{
    $s = $s ?? seo_sitemap_settings($pdo);
    $excluded = seo_sitemap_exclusions($s);
    $entries = [];
    $add = function (string $path, ?string $lastmod, string $type, array $images = []) use (&$entries, $s, $excluded) {
        foreach ($excluded as $pattern) {
            if (seo_path_matches($pattern, $path)) {
                return;
            }
        }
        $key = $type === 'home' ? 'home' : $type;
        $entries[] = [
            'path' => $path,
            'loc' => site_base_url() . ($path === '/' ? '/' : $path),
            'lastmod' => $lastmod,
            'changefreq' => $s[$key . '_changefreq'] ?? 'monthly',
            'priority' => $s[$key . '_priority'] ?? '0.5',
            'type' => $type,
            'images' => $s['include_images'] ? $images : [],
        ];
    };

    $home = null;
    $pages = $pdo->query('SELECT * FROM pages ORDER BY id')->fetchAll();
    foreach ($pages as $p) {
        if ($p['slug'] === '/') {
            $home = $p;
        }
    }
    if (!$home || (($home['robots_index'] ?? 'index') !== 'noindex' && ($home['status'] ?? 'published') !== 'draft')) {
        $add('/', $home['updated_at'] ?? null, 'home');
    }

    if ($s['include_pages']) {
        foreach ($pages as $p) {
            if ($p['slug'] === '/' || ($p['robots_index'] ?? 'index') === 'noindex' || ($p['status'] ?? 'published') === 'draft') {
                continue;
            }
            $add((string) $p['slug'], $p['updated_at'] ?? null, 'page');
        }
    }

    if ($s['include_blogs']) {
        $posts = $pdo->query("SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC")->fetchAll();
        $add('/blog', $posts[0]['updated_at'] ?? null, 'page');
        foreach ($posts as $b) {
            if (($b['robots_index'] ?? 'index') === 'noindex') {
                continue;
            }
            $imgs = $b['featured_image'] ? [['loc' => site_base_url() . UPLOAD_URL . $b['featured_image'], 'title' => $b['featured_image_alt'] ?: $b['title']]] : [];
            $add('/blog/' . $b['slug'], $b['updated_at'] ?? null, 'blog', $imgs);
        }
    }

    if ($s['include_case_studies']) {
        try {
            $studies = $pdo->query("SELECT * FROM case_studies WHERE status = 'published' ORDER BY created_at DESC")->fetchAll();
        } catch (PDOException $e) {
            $studies = [];
        }
        $add('/case-studies', $studies[0]['updated_at'] ?? null, 'page');
        foreach ($studies as $c) {
            if (($c['robots_index'] ?? 'index') === 'noindex') {
                continue;
            }
            $imgs = [];
            foreach (['desktop_image', 'mobile_image', 'result_image'] as $col) {
                if (!empty($c[$col])) {
                    $imgs[] = ['loc' => site_base_url() . UPLOAD_URL . $c[$col], 'title' => $c['title']];
                }
            }
            $add('/case-studies/' . $c['slug'], $c['updated_at'] ?? null, 'case_study', $imgs);
        }
    }

    return $entries;
}

function seo_sitemap_xml(PDO $pdo): string
{
    $entries = seo_sitemap_entries($pdo);
    $hasImages = false;
    foreach ($entries as $e) {
        if ($e['images']) {
            $hasImages = true;
            break;
        }
    }
    $out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
          . ($hasImages ? ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"' : '') . ">\n";
    foreach ($entries as $e) {
        $out .= "  <url>\n    <loc>" . h($e['loc']) . "</loc>\n";
        if (!empty($e['lastmod']) && strtotime($e['lastmod'])) {
            $out .= '    <lastmod>' . date('Y-m-d', strtotime($e['lastmod'])) . "</lastmod>\n";
        }
        if (in_array($e['changefreq'], seo_changefreqs(), true)) {
            $out .= '    <changefreq>' . $e['changefreq'] . "</changefreq>\n";
        }
        if (is_numeric($e['priority'])) {
            $out .= '    <priority>' . number_format(max(0, min(1, (float) $e['priority'])), 1) . "</priority>\n";
        }
        foreach ($e['images'] as $img) {
            $out .= "    <image:image>\n      <image:loc>" . h($img['loc']) . "</image:loc>\n    </image:image>\n";
        }
        $out .= "  </url>\n";
    }
    return $out . "</urlset>\n";
}

// ── Pages, blog posts and case studies as one list ──

/** type => [table, label, path prefix]. */
function seo_content_types(): array
{
    return [
        'page' => ['pages', 'Page', ''],
        'blog' => ['blogs', 'Blog post', '/blog/'],
        'case_study' => ['case_studies', 'Case study', '/case-studies/'],
    ];
}

/** One DB row → the shape every SEO screen uses. */
function seo_item_from_row(string $type, array $r): array
{
    $isPage = $type === 'page';
    $name = (string) ($isPage ? $r['name'] : $r['title']);
    $path = $isPage ? (string) $r['slug'] : seo_content_types()[$type][2] . $r['slug'];
    $status = (string) ($r['status'] ?? 'published');
    $live = $isPage ? $status !== 'draft' : $status === 'published';

    if ($type === 'blog') {
        $fallbackDesc = (string) $r['excerpt'];
        $content = trim(html_entity_decode(strip_tags((string) $r['content']), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $image = (string) $r['featured_image'];
        $edit = 'blog-edit.php?id=' . (int) $r['id'];
    } elseif ($type === 'case_study') {
        $fallbackDesc = (string) $r['description'];
        $content = trim(implode(' ', array_map(fn ($c) => strip_tags((string) ($r[$c] ?? '')), ['problem', 'solution', 'process', 'result', 'outcome'])));
        $image = (string) $r['desktop_image'];
        $edit = 'case-study-edit.php?id=' . (int) $r['id'];
    } else {
        $fallbackDesc = '';
        $content = null; // page templates aren't scored on content (same as the page editor)
        $image = '';
        $edit = 'page-edit.php?id=' . (int) $r['id'] . '&tab=settings';
    }

    return [
        'type' => $type,
        'id' => (int) $r['id'],
        'key' => $type . ':' . (int) $r['id'],
        'name' => $name,
        'path' => $path,
        'status' => $status,
        'live' => $live,
        'meta_title' => (string) ($r['meta_title'] ?? ''),
        'meta_description' => (string) ($r['meta_description'] ?? ''),
        'focus_keyword' => (string) ($r['focus_keyword'] ?? ''),
        'canonical_url' => (string) ($r['canonical_url'] ?? ''),
        'robots_index' => (string) ($r['robots_index'] ?? 'index'),
        'robots_follow' => (string) ($r['robots_follow'] ?? 'follow'),
        'og_title' => (string) ($r['og_title'] ?? ''),
        'og_description' => (string) ($r['og_description'] ?? ''),
        'og_image' => (string) ($r['og_image'] ?? ''),
        'title' => (string) ($r['meta_title'] ?? '') !== '' ? (string) $r['meta_title'] : $name,
        'description' => (string) ($r['meta_description'] ?? '') !== '' ? (string) $r['meta_description'] : $fallbackDesc,
        'image' => $image,
        'content' => $content,
        'updated_at' => (string) ($r['updated_at'] ?? ''),
        'edit_url' => $edit,
    ];
}

/** Every page, blog post and case study ($type limits it to one kind). */
function seo_items(PDO $pdo, string $type = ''): array
{
    $items = [];
    foreach (seo_content_types() as $t => [$table]) {
        if ($type !== '' && $type !== $t) {
            continue;
        }
        try {
            $rows = $pdo->query("SELECT * FROM $table ORDER BY " . ($t === 'page' ? 'id' : 'created_at DESC'))->fetchAll();
        } catch (PDOException $e) {
            continue; // e.g. case_studies before migration 005
        }
        foreach ($rows as $r) {
            $items[] = seo_item_from_row($t, $r);
        }
    }
    return $items;
}

function seo_item(PDO $pdo, string $type, int $id): ?array
{
    $types = seo_content_types();
    if (!isset($types[$type])) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT * FROM ' . $types[$type][0] . ' WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ? seo_item_from_row($type, $row) : null;
}

/** Saves the SEO columns of one page/post/case study. Only known fields; lengths capped like the edit forms. */
function seo_update_item(PDO $pdo, string $type, int $id, array $fields): void
{
    $types = seo_content_types();
    if (!isset($types[$type])) {
        return;
    }
    $limits = [
        'meta_title' => 190, 'meta_description' => 320, 'focus_keyword' => 190, 'canonical_url' => 255,
        'og_title' => 190, 'og_description' => 320, 'og_image' => 255,
    ];
    $set = [];
    $params = [];
    foreach ($fields as $k => $v) {
        if (isset($limits[$k])) {
            $set[] = "$k = ?";
            $params[] = mb_substr(trim((string) $v), 0, $limits[$k]);
        } elseif ($k === 'robots_index') {
            $set[] = 'robots_index = ?';
            $params[] = $v === 'noindex' ? 'noindex' : 'index';
        } elseif ($k === 'robots_follow') {
            $set[] = 'robots_follow = ?';
            $params[] = $v === 'nofollow' ? 'nofollow' : 'follow';
        }
    }
    if (!$set) {
        return;
    }
    $params[] = $id;
    $pdo->prepare('UPDATE ' . $types[$type][0] . ' SET ' . implode(', ', $set) . ' WHERE id = ?')->execute($params);
}

/**
 * Score 0–100, the same checks and weights as the live analyzer in the edit
 * forms (admin/assets/seo-analyzer.js). null when there's no focus keyword.
 */
function seo_score(array $item): ?array
{
    $keyword = mb_strtolower(trim($item['focus_keyword']));
    if ($keyword === '') {
        return null;
    }
    $title = $item['meta_title'] !== '' ? $item['meta_title'] : $item['name'];
    $desc = $item['meta_description'];
    $path = $item['path'];
    $checks = [];
    $earned = 0;
    $possible = 0;
    $add = function (string $label, bool $pass, int $weight) use (&$checks, &$earned, &$possible) {
        $possible += $weight;
        if ($pass) {
            $earned += $weight;
        }
        $checks[] = ['label' => $label, 'pass' => $pass];
    };
    $tLen = mb_strlen($title);
    $dLen = mb_strlen($desc);
    $add('Focus keyword in SEO title', str_contains(mb_strtolower($title), $keyword), 15);
    $add('Focus keyword in meta description', str_contains(mb_strtolower($desc), $keyword), 10);
    $add('Focus keyword in URL', str_contains(mb_strtolower($path), (string) preg_replace('/\s+/', '-', $keyword)), 10);
    $add("SEO title length is 30–60 characters ($tLen now)", $tLen >= 30 && $tLen <= 60, 10);
    $add("Meta description length is 120–160 characters ($dLen now)", $dLen >= 120 && $dLen <= 160, 10);
    $add('URL is reasonably short (' . strlen($path) . ' characters)', strlen($path) <= 75, 5);

    $content = (string) ($item['content'] ?? '');
    if ($content !== '') {
        $words = count(preg_split('/\s+/u', $content, -1, PREG_SPLIT_NO_EMPTY) ?: []);
        $hits = preg_match_all('/\b' . preg_quote($keyword, '/') . '\b/iu', $content);
        $density = $words ? ($hits / $words) * 100 : 0;
        $add('Focus keyword found in the content', str_contains(mb_strtolower($content), $keyword), 10);
        $add("Content is at least 300 words ($words now)", $words >= 300, 10);
        $add('Keyword density is 0.5%–2.5% (' . number_format($density, 1) . '% now)', $density >= 0.5 && $density <= 2.5, 10);
    }

    return ['score' => $possible ? (int) round($earned / $possible * 100) : 0, 'checks' => $checks];
}

function seo_score_class(?int $score): string
{
    if ($score === null) {
        return '';
    }
    return $score >= 80 ? 'good' : ($score >= 50 ? 'ok' : 'bad');
}

/** lowercased value => how many live, indexable items use it (2+ = duplicate). */
function seo_duplicates(array $items, string $field): array
{
    $counts = [];
    foreach ($items as $it) {
        if (!$it['live'] || $it['robots_index'] === 'noindex') {
            continue;
        }
        $v = mb_strtolower(trim($it[$field]));
        if ($v !== '') {
            $counts[$v] = ($counts[$v] ?? 0) + 1;
        }
    }
    return array_filter($counts, fn ($c) => $c > 1);
}

/** What the Page SEO filters offer: code => label. */
function seo_issue_labels(): array
{
    return [
        'no_title' => 'No SEO title',
        'title_length' => 'Title length',
        'no_desc' => 'No meta description',
        'desc_length' => 'Description length',
        'no_keyword' => 'No focus keyword',
        'keyword_title' => 'Keyword not in title',
        'dup_title' => 'Duplicate title',
        'dup_desc' => 'Duplicate description',
        'noindex' => 'Hidden (noindex)',
        'canonical' => 'Canonical elsewhere',
        'no_image' => 'No social image',
        'thin' => 'Thin content',
    ];
}

/**
 * Problems with one item: list of ['level' => error|warning|notice, 'code', 'text'].
 * $dupTitles / $dupDescs come from seo_duplicates() over the whole site.
 */
function seo_item_issues(array $it, array $dupTitles, array $dupDescs, array $global): array
{
    $issues = [];
    $add = function (string $level, string $code, string $text) use (&$issues) {
        $issues[] = ['level' => $level, 'code' => $code, 'text' => $text];
    };
    $tLen = mb_strlen($it['title']);
    $dLen = mb_strlen($it['description']);

    if ($it['meta_title'] === '') {
        $add('warning', 'no_title', 'No SEO title set — Google sees the plain name "' . $it['name'] . '".');
    }
    if ($tLen > 60) {
        $add('warning', 'title_length', "Title is $tLen characters — Google cuts it off after about 60.");
    } elseif ($tLen < 30) {
        $add('warning', 'title_length', "Title is only $tLen characters — aim for 30–60.");
    }
    if ($it['description'] === '' && trim($global['default_description']) === '') {
        $add('error', 'no_desc', 'No meta description — Google will pick random text from the page.');
    } elseif ($it['description'] === '') {
        $add('notice', 'no_desc', 'No meta description of its own — the site-wide default is used.');
    } elseif ($dLen > 160) {
        $add('warning', 'desc_length', "Meta description is $dLen characters — it gets cut off after about 160.");
    } elseif ($dLen < 120) {
        $add('notice', 'desc_length', "Meta description is only $dLen characters — aim for 120–160.");
    }
    if (trim($it['focus_keyword']) === '') {
        $add('notice', 'no_keyword', 'No focus keyword — add one to get an SEO score.');
    } elseif (!str_contains(mb_strtolower($it['title']), mb_strtolower(trim($it['focus_keyword'])))) {
        $add('warning', 'keyword_title', 'The focus keyword "' . $it['focus_keyword'] . '" is not in the SEO title.');
    }
    if ($it['live'] && $it['robots_index'] !== 'noindex') {
        if (isset($dupTitles[mb_strtolower(trim($it['title']))])) {
            $add('warning', 'dup_title', 'Same SEO title as ' . ($dupTitles[mb_strtolower(trim($it['title']))] - 1) . ' other page(s).');
        }
        if ($it['description'] !== '' && isset($dupDescs[mb_strtolower(trim($it['description']))])) {
            $add('warning', 'dup_desc', 'Same meta description as ' . ($dupDescs[mb_strtolower(trim($it['description']))] - 1) . ' other page(s).');
        }
    }
    if ($it['live'] && $it['robots_index'] === 'noindex') {
        $add('notice', 'noindex', 'Set to No Index — hidden from search results and the sitemap.');
    }
    if ($it['canonical_url'] !== '' && rtrim(seo_normalize_path($it['canonical_url']), '/') !== rtrim($it['path'], '/')) {
        $add('notice', 'canonical', 'Canonical URL points to another page: ' . $it['canonical_url']);
    }
    if ($it['og_image'] === '' && $it['image'] === '' && trim($global['default_og_image']) === '') {
        $add('notice', 'no_image', 'No social share image — links shared on LinkedIn, WhatsApp, etc. show no picture.');
    }
    if ($it['content'] !== null && $it['type'] === 'blog') {
        $words = count(preg_split('/\s+/u', (string) $it['content'], -1, PREG_SPLIT_NO_EMPTY) ?: []);
        if ($words < 300) {
            $add('warning', 'thin', "Only $words words — posts under 300 words rarely rank.");
        }
    }
    return $issues;
}

/** Items with score + issues attached, for Page SEO, Overview and Audit. */
function seo_analyzed_items(PDO $pdo): array
{
    $items = seo_items($pdo);
    $g = seo_global($pdo);
    $dupT = seo_duplicates($items, 'title');
    $dupD = seo_duplicates($items, 'description');
    foreach ($items as &$it) {
        $sc = seo_score($it);
        $it['score'] = $sc['score'] ?? null;
        $it['checks'] = $sc['checks'] ?? [];
        $it['issues'] = seo_item_issues($it, $dupT, $dupD, $g);
    }
    unset($it);
    return $items;
}

// ── Site audit ──

/** True if a site path is served by something (page, post, case study, built-in route). */
function seo_path_exists(PDO $pdo, string $path): bool
{
    if (in_array($path, ['/', '/blog', '/case-studies', '/book', '/analyze', '/sitemap.xml', '/robots.txt'], true)) {
        return true;
    }
    if (preg_match('#^/blog/(.+)$#', $path, $m)) {
        $stmt = $pdo->prepare("SELECT 1 FROM blogs WHERE slug = ? AND status = 'published'");
        $stmt->execute([$m[1]]);
        return (bool) $stmt->fetchColumn();
    }
    if (preg_match('#^/case-studies/(.+)$#', $path, $m)) {
        try {
            $stmt = $pdo->prepare("SELECT 1 FROM case_studies WHERE slug = ? AND status = 'published'");
            $stmt->execute([$m[1]]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }
    }
    $stmt = $pdo->prepare("SELECT 1 FROM pages WHERE slug = ? AND (status IS NULL OR status <> 'draft')");
    $stmt->execute([$path]);
    if ($stmt->fetchColumn()) {
        return true;
    }
    // Real files (e.g. /about-us.html, /assets/...).
    $file = realpath(__DIR__ . '/..' . $path);
    $root = realpath(__DIR__ . '/..');
    return $file !== false && $root !== false && str_starts_with($file, $root) && is_file($file);
}

/** Problems with the redirect list: loops, chains, and targets that don't exist. */
function seo_redirect_problems(PDO $pdo): array
{
    try {
        $rows = $pdo->query('SELECT * FROM seo_redirects WHERE enabled = 1')->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
    $bySource = [];
    foreach ($rows as $r) {
        if ($r['match_type'] === 'exact') {
            $bySource[strtolower($r['source'])] = $r;
        }
    }
    $problems = [];
    foreach ($rows as $r) {
        if ((int) $r['code'] === 410) {
            continue;
        }
        $target = (string) $r['target'];
        if (preg_match('#^https?://#i', $target)) {
            $host = strtolower((string) parse_url($target, PHP_URL_HOST));
            if ($host !== strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''))) {
                continue; // external — not ours to check
            }
            $target = seo_normalize_path($target);
        }
        if (str_ends_with($target, '/*')) {
            continue;
        }
        $tPath = seo_normalize_path((string) preg_replace('/\?.*$/', '', $target));
        $seen = [strtolower($r['source']) => true];
        $hop = $tPath;
        $chain = 0;
        $loop = false;
        while (isset($bySource[strtolower($hop)])) {
            if (isset($seen[strtolower($hop)])) {
                $loop = true;
                break;
            }
            $seen[strtolower($hop)] = true;
            $chain++;
            $hop = seo_normalize_path((string) preg_replace('/\?.*$/', '', (string) $bySource[strtolower($hop)]['target']));
            if ($chain > 10) {
                $loop = true;
                break;
            }
        }
        if ($loop) {
            $problems[] = ['level' => 'error', 'id' => (int) $r['id'], 'text' => $r['source'] . ' is part of a redirect loop — visitors never reach a page.'];
        } elseif ($chain > 0) {
            $problems[] = ['level' => 'warning', 'id' => (int) $r['id'], 'text' => $r['source'] . ' → ' . $tPath . ' is redirected again (' . ($chain + 1) . ' hops). Point it straight at ' . $hop . '.'];
        } elseif (!seo_path_exists($pdo, $tPath)) {
            $problems[] = ['level' => 'warning', 'id' => (int) $r['id'], 'text' => $r['source'] . ' redirects to ' . $tPath . ', which doesn\'t exist on this site.'];
        }
    }
    return $problems;
}

/**
 * Whole-site audit. Returns:
 *   checks  list of ['status' => pass|warn|fail, 'title', 'detail', 'link', 'link_label']
 *   items   analyzed items (seo_analyzed_items)
 *   score   0–100 health score
 *   counts  ['error' => n, 'warning' => n, 'notice' => n] over all item issues
 */
function seo_audit(PDO $pdo): array
{
    $items = seo_analyzed_items($pdo);
    $g = seo_global($pdo);
    $schema = seo_schema_settings($pdo);
    $robots = seo_robots_settings($pdo);
    $sitemap = seo_sitemap_settings($pdo);
    $checks = [];
    $add = function (string $status, string $title, string $detail, string $link = '', string $label = '') use (&$checks) {
        $checks[] = ['status' => $status, 'title' => $title, 'detail' => $detail, 'link' => $link, 'link_label' => $label];
    };

    // Crawling & indexing
    if ($robots['block_all']) {
        $add('fail', 'Search engines are blocked', '"Discourage search engines" is on: robots.txt blocks everything and every page says noindex.', 'seo-robots.php', 'Robots');
    } else {
        $rc = seo_robots_check(seo_robots_txt($pdo), 'Googlebot', '/');
        $add($rc['allowed'] ? 'pass' : 'fail', 'robots.txt lets Google crawl the site',
            $rc['allowed'] ? 'Googlebot may crawl the home page.' : 'robots.txt blocks Googlebot from the home page (' . $rc['rule'] . ').', 'seo-robots.php', 'Robots');
        $robotsTxt = seo_robots_txt($pdo);
        $add(stripos($robotsTxt, 'sitemap:') !== false ? 'pass' : 'warn', 'robots.txt points to the sitemap',
            stripos($robotsTxt, 'sitemap:') !== false ? 'Search engines find the sitemap from robots.txt.' : 'Add "Sitemap: {sitemap}" so crawlers find every page.', 'seo-robots.php', 'Robots');
    }
    $entries = $sitemap['enabled'] ? seo_sitemap_entries($pdo, $sitemap) : [];
    $add($sitemap['enabled'] ? 'pass' : 'fail', 'XML sitemap is on',
        $sitemap['enabled'] ? count($entries) . ' URLs listed at /sitemap.xml.' : 'The sitemap is switched off — /sitemap.xml returns 404.', 'seo-sitemap.php', 'Sitemap');
    $https = str_starts_with(site_base_url(), 'https://');
    $add($https ? 'pass' : 'warn', 'Site is served over HTTPS', $https ? 'Canonical and sitemap URLs use https://.' : 'This admin was opened over http:// — make sure the live site forces HTTPS.');

    // Search Console / Analytics
    $add(get_setting($pdo, 'gsc_verification_tag', '') !== '' ? 'pass' : 'warn', 'Google Search Console verified',
        get_setting($pdo, 'gsc_verification_tag', '') !== '' ? 'Verification tag is on every page.' : 'Verify the site to see search queries and indexing errors.', 'analytics.php', 'Analytics & Search');
    $add(get_setting($pdo, 'ga_measurement_id', '') !== '' ? 'pass' : 'warn', 'Google Analytics connected',
        get_setting($pdo, 'ga_measurement_id', '') !== '' ? 'GA4 tracking is on every page.' : 'Add a GA4 Measurement ID to measure organic traffic.', 'analytics.php', 'Analytics & Search');
    $add(get_setting($pdo, 'clarity_project_id', '') !== '' ? 'pass' : 'warn', 'Microsoft Clarity connected',
        get_setting($pdo, 'clarity_project_id', '') !== '' ? 'Clarity heatmaps & recordings are on every page.' : 'Add a Clarity Project ID to see heatmaps and session recordings.', 'analytics.php', 'Analytics & Search');

    // Global settings & schema
    $add(trim($g['default_og_image']) !== '' ? 'pass' : 'warn', 'Default social share image',
        trim($g['default_og_image']) !== '' ? 'Pages without their own image still show one when shared.' : 'Set one so shared links always show a picture.', 'seo-settings.php', 'Global Settings');
    $add($schema['org_enabled'] && trim($schema['org_name']) !== '' ? 'pass' : 'warn', 'Organization schema',
        $schema['org_enabled'] ? 'Google gets your business name, logo and profiles.' : 'Turn it on so Google can show your logo and knowledge panel.', 'seo-schema.php', 'Schema');
    $orgLogo = $schema['org_enabled'] && trim($schema['org_logo']) !== '';
    if ($schema['org_enabled']) {
        $add($orgLogo ? 'pass' : 'warn', 'Organization logo set', $orgLogo ? 'Logo is included in the schema.' : 'Add a logo URL to the Organization schema.', 'seo-schema.php', 'Schema');
    }

    // Content
    $live = array_filter($items, fn ($i) => $i['live'] && $i['robots_index'] !== 'noindex');
    $count = fn (string $code) => count(array_filter($live, fn ($i) => in_array($code, array_column($i['issues'], 'code'), true)));
    $noDesc = count(array_filter($live, fn ($i) => $i['description'] === ''));
    $add($noDesc === 0 ? 'pass' : 'fail', 'Every page has a meta description',
        $noDesc === 0 ? 'All ' . count($live) . ' indexable pages have one.' : "$noDesc indexable page(s) have no meta description.", 'seo-pages.php?issue=no_desc', 'Fix in Page SEO');
    $n = $count('dup_title');
    $add($n === 0 ? 'pass' : 'warn', 'SEO titles are unique', $n === 0 ? 'No two pages share a title.' : "$n page(s) share a title with another page.", 'seo-pages.php?issue=dup_title', 'Fix in Page SEO');
    $n = $count('dup_desc');
    $add($n === 0 ? 'pass' : 'warn', 'Meta descriptions are unique', $n === 0 ? 'No two pages share a description.' : "$n page(s) share a description with another page.", 'seo-pages.php?issue=dup_desc', 'Fix in Page SEO');
    $n = $count('title_length');
    $add($n === 0 ? 'pass' : 'warn', 'SEO title lengths (30–60)', $n === 0 ? 'Every title is a good length.' : "$n title(s) are too short or too long.", 'seo-pages.php?issue=title_length', 'Fix in Page SEO');
    $n = $count('no_keyword');
    $add($n === 0 ? 'pass' : 'warn', 'Focus keywords set', $n === 0 ? 'Every page targets a keyword.' : "$n page(s) have no focus keyword.", 'seo-bulk.php', 'Bulk Editor');
    $scores = array_values(array_filter(array_map(fn ($i) => $i['score'], $live), fn ($s) => $s !== null));
    $avg = $scores ? (int) round(array_sum($scores) / count($scores)) : null;
    if ($avg !== null) {
        $add($avg >= 80 ? 'pass' : ($avg >= 50 ? 'warn' : 'fail'), 'Average on-page SEO score', "$avg/100 across " . count($scores) . ' pages with a focus keyword.', 'seo-pages.php', 'Page SEO');
    }
    $n = $count('thin');
    $add($n === 0 ? 'pass' : 'warn', 'No thin blog posts', $n === 0 ? 'Every post has 300+ words.' : "$n post(s) have fewer than 300 words.", 'seo-pages.php?issue=thin', 'Page SEO');

    // Images
    $inline = seo_blog_inline_images($pdo);
    $missing = count(array_filter($inline, fn ($i) => trim($i['alt']) === ''));
    $add($missing === 0 ? 'pass' : (seo_image_settings($pdo)['auto_alt'] ? 'warn' : 'fail'), 'Blog images have alt text',
        $missing === 0 ? 'Every image inside a blog post has alt text.' : "$missing image(s) in blog posts have no alt text" . (seo_image_settings($pdo)['auto_alt'] ? ' (auto-filled from the file name on the live site).' : '.'), 'seo-images.php', 'Image SEO');

    // Redirects & 404s
    if (seo_module_ready($pdo)) {
        $rp = seo_redirect_problems($pdo);
        $loops = count(array_filter($rp, fn ($p) => $p['level'] === 'error'));
        $add($loops ? 'fail' : (count($rp) ? 'warn' : 'pass'), 'Redirects are clean',
            $rp ? count($rp) . ' redirect problem(s): ' . $rp[0]['text'] : 'No loops, chains or broken targets.', 'seo-redirects.php', 'Redirects');
        $recent = (int) $pdo->query("SELECT COUNT(*) FROM seo_404_log WHERE is_bot = 0 AND last_seen >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
        $add($recent === 0 ? 'pass' : 'warn', 'No broken links hit by visitors (7 days)',
            $recent === 0 ? 'No visitor 404s in the last week.' : "$recent missing URL(s) were visited in the last 7 days — redirect the ones that matter.", 'seo-404.php', '404 Logs');
    } else {
        $add('warn', 'Redirects & 404 tracking', 'Run migration 031 to turn on redirects and 404 logging.', 'run-migrations.php', 'Migrations');
    }

    $weights = ['pass' => 1, 'warn' => 0.5, 'fail' => 0];
    $score = $checks ? (int) round(array_sum(array_map(fn ($c) => $weights[$c['status']], $checks)) / count($checks) * 100) : 0;
    $counts = ['error' => 0, 'warning' => 0, 'notice' => 0];
    foreach ($items as $it) {
        foreach ($it['issues'] as $iss) {
            $counts[$iss['level']]++;
        }
    }
    return ['checks' => $checks, 'items' => $items, 'score' => $score, 'avg_score' => $avg, 'counts' => $counts, 'sitemap_count' => count($entries)];
}

// ── Image SEO ──

/** "drawlead-crm-dashboard-a1b2c3.webp" → "Drawlead crm dashboard". */
function seo_alt_from_filename(string $src): string
{
    if ($src === '' || str_starts_with($src, 'data:')) {
        return '';
    }
    $name = rawurldecode(basename((string) parse_url($src, PHP_URL_PATH)));
    $name = (string) preg_replace('/\.[a-z0-9]{2,5}$/i', '', $name);
    $name = (string) preg_replace('/-[0-9a-f]{6,8}$/i', '', $name);   // media library suffix
    $name = (string) preg_replace('/^(site-sidebar|popup|pc)-/i', '', $name);
    $name = trim((string) preg_replace('/[-_.\s]+/', ' ', $name));
    if ($name === '' || preg_match('/^(img|image|dsc|photo|screenshot)?\s*\d*$/i', $name) || preg_match('/^[0-9a-f\s]{8,}$/i', $name)) {
        return '';
    }
    return mb_strtoupper(mb_substr($name, 0, 1)) . mb_substr($name, 1);
}

/** Output filter (index.php, when Image SEO → auto alt is on): gives <img> tags with no alt one from the file name. */
function seo_auto_alt_filter(string $html): string
{
    if (stripos($html, '<img') === false) {
        return $html;
    }
    return (string) preg_replace_callback('/<img\b[^>]*>/i', function ($m) {
        $tag = $m[0];
        $attrs = pc_parse_attrs($tag);
        $alt = pc_attr($attrs, 'alt');
        if ($alt !== null && trim($alt) !== '') {
            return $tag;
        }
        if (pc_attr($attrs, 'role') === 'presentation' || pc_attr($attrs, 'aria-hidden') === 'true') {
            return $tag;
        }
        $auto = seo_alt_from_filename((string) pc_attr($attrs, 'src'));
        return $auto === '' ? $tag : pc_set_attrs($tag, ['alt' => $auto]);
    }, $html);
}

/** Every <img> inside blog post content: blog_id, index (nth img in that post), src, alt, post title, status. */
function seo_blog_inline_images(PDO $pdo): array
{
    $out = [];
    foreach ($pdo->query('SELECT id, title, slug, status, content FROM blogs ORDER BY created_at DESC')->fetchAll() as $b) {
        if (!preg_match_all('/<img\b[^>]*>/i', (string) $b['content'], $m)) {
            continue;
        }
        foreach ($m[0] as $i => $tag) {
            $attrs = pc_parse_attrs($tag);
            $out[] = [
                'blog_id' => (int) $b['id'],
                'index' => $i,
                'src' => (string) pc_attr($attrs, 'src'),
                'alt' => (string) pc_attr($attrs, 'alt'),
                'post' => (string) $b['title'],
                'slug' => (string) $b['slug'],
                'status' => (string) $b['status'],
            ];
        }
    }
    return $out;
}

/** Sets the alt text of the nth <img> in a blog post's content. Returns the new content, or null if that image is gone. */
function seo_set_inline_alt(string $content, int $index, string $alt): ?string
{
    if (!preg_match_all('/<img\b[^>]*>/i', $content, $m, PREG_OFFSET_CAPTURE) || !isset($m[0][$index])) {
        return null;
    }
    [$tag, $offset] = $m[0][$index];
    $new = pc_set_attrs($tag, ['alt' => $alt]);
    return substr($content, 0, $offset) . $new . substr($content, $offset + strlen($tag));
}

/** Library files with SEO/performance problems: heavy, old formats, meaningless names, oversized. */
function seo_media_report(): array
{
    $rows = [];
    foreach (media_library_images() as $img) {
        $path = UPLOAD_DIR . (str_starts_with(rawurldecode(substr($img['url'], strlen(UPLOAD_URL))), 'media/') ? 'media/' : '') . $img['file'];
        $bytes = is_file($path) ? (int) filesize($path) : 0;
        $ext = strtolower(pathinfo($img['file'], PATHINFO_EXTENSION));
        $problems = [];
        if ($bytes > 300 * 1024) {
            $problems[] = 'Large file (' . media_human_size($bytes) . ') — compress it below 300 KB';
        }
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true) && $bytes > 100 * 1024) {
            $problems[] = 'Old format (' . strtoupper($ext) . ') — WebP or AVIF is usually 30–50% smaller';
        }
        if ($img['w'] > 2400) {
            $problems[] = 'Very wide (' . $img['w'] . 'px) — 1600–2000px is plenty for the web';
        }
        if (seo_alt_from_filename($img['file']) === '') {
            $problems[] = 'File name says nothing about the image — rename it with words (e.g. crm-dashboard.webp)';
        }
        $rows[] = $img + ['bytes' => $bytes, 'ext' => $ext, 'problems' => $problems];
    }
    return $rows;
}

// ── Live page check (SEO Audit) ──

/** Fetches one page of this site and checks what search engines actually receive. */
function seo_live_check(string $path): array
{
    if (!class_exists('DOMDocument')) {
        return ['ok' => false, 'error' => 'The server is missing PHP\'s DOM extension, so pages can\'t be checked.'];
    }
    $url = site_base_url() . ($path === '/' ? '/' : $path);
    $start = microtime(true);
    $res = analyze_fetch_html($url);
    $ms = (int) round((microtime(true) - $start) * 1000);
    if (!$res['ok']) {
        return ['ok' => false, 'error' => $res['error'], 'url' => $url];
    }
    $html = $res['html'];
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOWARNING | LIBXML_NOERROR);
    libxml_clear_errors();
    $xp = new DOMXPath($doc);
    $first = function (string $q, string $attr = '') use ($xp) {
        $n = $xp->query($q);
        if (!$n || !$n->length) {
            return null;
        }
        return trim($attr !== '' ? (string) $n->item(0)->getAttribute($attr) : (string) $n->item(0)->textContent);
    };
    $title = $first('//title');
    $desc = $first('//meta[@name="description"]', 'content');
    $canonical = $first('//link[@rel="canonical"]', 'href');
    $robots = $first('//meta[@name="robots"]', 'content');
    $lang = $first('//html', 'lang');
    $viewport = $first('//meta[@name="viewport"]', 'content');
    $ogTitle = $first('//meta[@property="og:title"]', 'content');
    $ogImage = $first('//meta[@property="og:image"]', 'content');
    $h1s = [];
    foreach ($xp->query('//h1') ?: [] as $n) {
        $h1s[] = trim(preg_replace('/\s+/', ' ', $n->textContent));
    }
    $h2 = $xp->query('//h2')->length;
    $imgs = $xp->query('//img');
    $noAlt = [];
    foreach ($imgs ?: [] as $n) {
        if (trim($n->getAttribute('alt')) === '' && $n->getAttribute('role') !== 'presentation') {
            $noAlt[] = $n->getAttribute('src');
        }
    }
    $schemas = [];
    $badSchema = 0;
    foreach ($xp->query('//script[@type="application/ld+json"]') ?: [] as $n) {
        $d = json_decode(trim($n->textContent), true);
        if (!is_array($d)) {
            $badSchema++;
            continue;
        }
        $isList = $d && array_keys($d) === range(0, count($d) - 1);
        foreach (isset($d['@graph']) && is_array($d['@graph']) ? $d['@graph'] : ($isList ? $d : [$d]) as $node) {
            if (is_array($node) && isset($node['@type'])) {
                $schemas[] = is_array($node['@type']) ? implode('/', $node['@type']) : (string) $node['@type'];
            }
        }
    }
    foreach ($xp->query('//script|//style|//noscript') ?: [] as $n) {
        $n->parentNode->removeChild($n);
    }
    $bodyNode = $xp->query('//body')->item(0);
    $text = $bodyNode ? trim(preg_replace('/\s+/u', ' ', $bodyNode->textContent)) : '';
    $words = $text === '' ? 0 : count(preg_split('/\s+/u', $text));
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    $internal = 0;
    $external = 0;
    foreach ($xp->query('//a[@href]') ?: [] as $a) {
        $href = trim($a->getAttribute('href'));
        if ($href === '' || str_starts_with($href, '#') || preg_match('/^(mailto|tel|javascript):/i', $href)) {
            continue;
        }
        $h = strtolower((string) parse_url($href, PHP_URL_HOST));
        ($h === '' || $h === $host) ? $internal++ : $external++;
    }

    $checks = [];
    $c = function (string $status, string $label, string $detail) use (&$checks) {
        $checks[] = ['status' => $status, 'title' => $label, 'detail' => $detail];
    };
    $tl = $title !== null ? mb_strlen($title) : 0;
    $c($title ? ($tl <= 60 && $tl >= 30 ? 'pass' : 'warn') : 'fail', 'Title tag', $title ? "\"$title\" ($tl characters)" : 'Missing');
    $dl = $desc !== null ? mb_strlen($desc) : 0;
    $c($desc ? ($dl >= 120 && $dl <= 160 ? 'pass' : 'warn') : 'fail', 'Meta description', $desc ? "$dl characters: " . mb_strimwidth($desc, 0, 170, '…') : 'Missing');
    $c(count($h1s) === 1 ? 'pass' : (count($h1s) ? 'warn' : 'fail'), 'One H1 heading', count($h1s) ? count($h1s) . ' found: "' . mb_strimwidth(implode('" · "', $h1s), 0, 160, '…') . '"' : 'No H1 on the page');
    $c($h2 > 0 ? 'pass' : 'warn', 'Subheadings (H2)', $h2 . ' found');
    $c($canonical ? 'pass' : 'warn', 'Canonical URL', $canonical ?: 'Missing');
    $indexable = !$robots || !str_contains(strtolower($robots), 'noindex');
    $c($indexable ? 'pass' : 'fail', 'Indexable', $robots ? "robots meta: $robots" : 'No robots meta (indexable by default)');
    $c(!$noAlt ? 'pass' : 'warn', 'Images have alt text', $imgs->length . ' images, ' . count($noAlt) . ' without alt' . ($noAlt ? ': ' . mb_strimwidth(implode(', ', array_map('basename', $noAlt)), 0, 160, '…') : ''));
    $c($words >= 300 ? 'pass' : 'warn', 'Amount of text', "$words words");
    $c($ogTitle && $ogImage ? 'pass' : 'warn', 'Social share tags', ($ogTitle ? 'og:title ✓' : 'og:title missing') . ' · ' . ($ogImage ? 'og:image ✓' : 'og:image missing'));
    $c($schemas && !$badSchema ? 'pass' : ($badSchema ? 'fail' : 'warn'), 'Structured data (JSON-LD)', ($schemas ? implode(', ', array_unique($schemas)) : 'None') . ($badSchema ? " · $badSchema block(s) with invalid JSON" : ''));
    $c($lang ? 'pass' : 'warn', 'Language attribute', $lang ? "lang=\"$lang\"" : '<html> has no lang attribute');
    $c($viewport ? 'pass' : 'fail', 'Mobile viewport', $viewport ?: 'Missing');
    $c($ms < 1500 ? 'pass' : 'warn', 'Server response time', $ms . ' ms (' . media_human_size(strlen($html)) . ' HTML)');
    $c('pass', 'Links', "$internal internal · $external external");

    return ['ok' => true, 'url' => $url, 'final_url' => $res['final_url'], 'checks' => $checks];
}

// ── Admin view helpers ──

/** One-time message shown after a save + redirect (POST → redirect → GET). */
function seo_flash(string $message, string $type = 'success'): void
{
    $_SESSION['seo_flash'][] = [$type === 'error' ? 'error' : 'success', $message];
}

function seo_flash_html(): string
{
    $out = '';
    foreach ($_SESSION['seo_flash'] ?? [] as [$type, $msg]) {
        $out .= '<div class="alert alert-' . $type . '">' . h($msg) . '</div>';
    }
    unset($_SESSION['seo_flash']);
    return $out;
}

/** Redirect back after a POST. */
function seo_back(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** pass / warn / fail list (Overview, Audit, live check). */
function seo_checks_html(array $checks): string
{
    $icons = ['pass' => '✓', 'warn' => '!', 'fail' => '✕'];
    $out = '<ul class="seo-checks">';
    foreach ($checks as $c) {
        $out .= '<li class="is-' . h($c['status']) . '"><span class="seo-check-icon">' . $icons[$c['status']] . '</span>'
              . '<div class="seo-check-body"><div class="seo-check-title">' . h($c['title']) . '</div>'
              . '<div class="seo-check-detail">' . h($c['detail']) . '</div></div>';
        if (!empty($c['link'])) {
            $out .= '<a class="seo-check-link" href="' . h($c['link']) . '">' . h($c['link_label'] ?: 'Open') . ' →</a>';
        }
        $out .= '</li>';
    }
    return $out . '</ul>';
}

/** Score pill: "—" without a focus keyword. */
function seo_score_html(?int $score): string
{
    return '<span class="seo-score ' . seo_score_class($score) . '" title="' . ($score === null ? 'Add a focus keyword to get a score' : 'On-page SEO score') . '">'
        . ($score === null ? '—' : $score) . '</span>';
}

/** Simple pager links keeping the current query string. */
function seo_pager_html(int $page, int $pages): string
{
    if ($pages <= 1) {
        return '';
    }
    $out = '<nav class="seo-pager">';
    for ($p = 1; $p <= $pages; $p++) {
        if ($p !== 1 && $p !== $pages && abs($p - $page) > 2) {
            if ($p === 2 || $p === $pages - 1) {
                $out .= '<a aria-hidden="true">…</a>';
            }
            continue;
        }
        $q = http_build_query(array_merge($_GET, ['page' => $p]));
        $out .= $p === $page ? '<span>' . $p . '</span>' : '<a href="?' . h($q) . '">' . $p . '</a>';
    }
    return $out . '</nav>';
}
