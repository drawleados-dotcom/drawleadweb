<?php
/**
 * Site navigation: the menu edited in Admin → Menu and drawn by
 * templates/partials/nav.php.
 *
 * Stored as JSON in settings.site_navigation:
 *   {"version":2,"items":[{id,label,url,visible,new_tab,children:[…],mega:{…}|null}, …]}
 *
 * Items nest up to NAV_MENU_MAX_DEPTH levels below the top level (a sub menu,
 * and a sub menu of that). Only top-level items can have a mega menu; when it
 * is switched on the desktop header shows it instead of the item's dropdown,
 * and the item's sub items still show in the mobile menu.
 *
 * A mega menu is a list of columns plus an optional booking box. Each column
 * has an icon from nav_menu_icons() (code-defined SVG, never user markup), two
 * hex colours for the icon's gradient, a title, a line of text, bullet points
 * (or the latest case studies for a service, filled in live), and a link.
 * Layout "columns" is the wide Solutions / Case Studies panel; "tiles" is the
 * compact Industries grid.
 *
 * Saves from before Admin → Menu could add items (the fixed six links with
 * label/url/visible/mega flags, no "version") are read into the default tree.
 */

const NAV_MENU_MAX_DEPTH = 2;        // 0 top level, 1 sub menu, 2 sub-sub menu
const NAV_MENU_MAX_ITEMS = 80;
const NAV_MEGA_MAX_COLUMNS = 24;     // the Industries panel has 20 tiles
const NAV_MEGA_MAX_BULLETS = 8;

/** Icons a mega menu column can use: key => [label, svg]. */
function nav_menu_icons(): array
{
    static $icons = null;
    if ($icons !== null) {
        return $icons;
    }
    $icons = [
        'erp' => ['ERP (blocks)', '<svg width="19" height="19" viewBox="0 0 40 40" fill="none"><rect x="5" y="5" width="13" height="13" rx="2.5" fill="white"/><rect x="22" y="5" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="5" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="22" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.85)"/><path d="M18 11.5 L22 11.5 M11.5 18 L11.5 22 M28.5 18 L28.5 22 M18 28.5 L22 28.5" stroke="rgba(255,255,255,.5)" stroke-width="2" stroke-linecap="round"/></svg>'],
        'ecommerce' => ['Ecommerce (cart)', '<svg width="19" height="19" viewBox="0 0 40 40" fill="none"><path d="M4 10 L10 10 L14 27 L32 27" stroke="rgba(255,255,255,.55)" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M11.5 14 L35 14 L32 24 L13.8 24 Z" fill="white"/><circle cx="16" cy="33" r="3" fill="rgba(255,255,255,.85)"/><circle cx="30" cy="33" r="3" fill="rgba(255,255,255,.85)"/></svg>'],
        'marketing' => ['Marketing (funnel)', '<svg width="19" height="19" viewBox="0 0 40 40" fill="none"><path d="M4 6 L36 6 L24 21 L24 34 L16 30 L16 21 Z" fill="white"/><path d="M16 21 L24 21 L24 27 L16 27 Z" fill="rgba(255,255,255,.55)"/><path d="M4 6 L36 6 L31 12 L9 12 Z" fill="rgba(255,255,255,.6)"/></svg>'],
    ];
    if (function_exists('industries_ordered')) {
        foreach (industries_ordered() as $entry) {
            $icons['industry-' . $entry['key']] = [$entry['industry']['name'], $entry['industry']['icon']];
        }
    }
    return $icons;
}

/** A mega menu column with every field present. */
function nav_menu_column(array $c = []): array
{
    return array_merge([
        'icon' => '', 'color' => '#32b46f', 'color_to' => '#14855a', 'title' => '', 'text' => '',
        'bullets' => [], 'case_studies' => '', 'link_label' => 'Know More →', 'link_url' => '',
    ], $c);
}

/** The menu as it was before it became editable: the six links and their three mega menus. */
function nav_menu_defaults(): array
{
    $book = 'Book a Free Consultation →';
    $solutions = [
        'enabled' => true, 'layout' => 'columns',
        'columns' => [
            nav_menu_column(['icon' => 'erp', 'title' => 'Custom ERP Solution', 'link_url' => '/custom-erp-solution', 'bullets' => [
                'Custom modules for your exact operating process', 'Role-based access, approvals & audit trails', 'Migration from spreadsheets & legacy systems']]),
            nav_menu_column(['icon' => 'ecommerce', 'title' => 'Ecommerce Solutions', 'link_url' => '/ecommerce-solutions', 'bullets' => [
                'Shopify, WooCommerce & custom storefront builds', 'Live inventory sync across every sales channel', 'Automated order, invoice & GST workflows']]),
            nav_menu_column(['icon' => 'marketing', 'title' => 'Marketing Solutions', 'link_url' => '/marketing-solutions', 'bullets' => [
                'Technical SEO, Core Web Vitals & site architecture', 'Google, Meta & LinkedIn performance campaigns', 'Instant WhatsApp & email follow-up on every lead']]),
        ],
        'cta' => ['enabled' => true, 'title' => 'Not sure which fits?', 'text' => "Get a free consultation and we'll map the right solution to your business.", 'button' => $book],
    ];
    $caseStudies = ['enabled' => true, 'layout' => 'columns', 'columns' => [], 'cta' => [
        'enabled' => true, 'title' => 'Want results like these?', 'text' => 'See how Drawlead can do the same for your business — starting with a free call.', 'button' => $book]];
    foreach (['erp' => 'Custom ERP Solution', 'ecommerce' => 'Ecommerce Solutions', 'marketing' => 'Marketing Solutions'] as $icon => $service) {
        $caseStudies['columns'][] = nav_menu_column(['icon' => $icon, 'title' => $service, 'case_studies' => $service,
            'link_label' => 'View All →', 'link_url' => '/case-studies?service=' . urlencode($service)]);
    }
    $industries = ['enabled' => true, 'layout' => 'tiles', 'columns' => [], 'cta' => ['enabled' => false, 'title' => '', 'text' => '', 'button' => $book]];
    if (function_exists('industries_ordered')) {
        foreach (industries_ordered() as $entry) {
            $ind = $entry['industry'];
            $industries['columns'][] = nav_menu_column(['icon' => 'industry-' . $entry['key'], 'color' => $ind['color'], 'color_to' => '#0a1310',
                'title' => $ind['name'], 'text' => $ind['tag'], 'link_url' => '/industry-' . $entry['key']]);
        }
    }
    $item = fn (string $id, string $label, string $url, ?array $mega = null): array =>
        ['id' => $id, 'label' => $label, 'url' => $url, 'visible' => true, 'new_tab' => false, 'children' => [], 'mega' => $mega];
    return [
        $item('home', 'Home', '/'),
        $item('about-us', 'About Us', '/about-us'),
        $item('solutions', 'Solutions', '/#solutions', $solutions),
        $item('case-studies', 'Case Studies', '/#case-studies', $caseStudies),
        $item('industries', 'Industries', '/#industries', $industries),
        $item('blog', 'Blog', '/blog'),
    ];
}

/** The saved menu, or the defaults. Never throws: the header is on every page. */
function nav_menu_load(PDO $pdo): array
{
    $saved = null;
    try {
        $raw = $pdo->query("SELECT `value` FROM settings WHERE `key`='site_navigation'")->fetchColumn();
        $saved = is_string($raw) ? json_decode($raw, true) : null;
    } catch (Throwable $e) {
        // keep the defaults
    }
    if (is_array($saved) && ($saved['version'] ?? 0) === 2 && isset($saved['items']) && is_array($saved['items'])) {
        $errors = [];
        return nav_menu_clean($saved['items'], $errors);
    }
    $items = nav_menu_defaults();
    if (!is_array($saved)) {
        return $items;
    }
    // Version 1: the fixed six links, keyed by id.
    foreach ($items as &$it) {
        $key = $it['id'];
        $s = $saved['items'][$key] ?? null;
        if (is_array($s)) {
            if (isset($s['label']) && is_string($s['label']) && trim($s['label']) !== '') $it['label'] = $s['label'];
            if (isset($s['url']) && is_string($s['url']) && trim($s['url']) !== '') $it['url'] = $s['url'];
        }
        if (isset($saved['visible']) && is_array($saved['visible']) && array_key_exists($key, $saved['visible'])) {
            $it['visible'] = (bool) $saved['visible'][$key];
        }
        if ($it['mega']) {
            $it['mega']['enabled'] = isset($saved['mega']) && is_array($saved['mega']) && array_key_exists($key, $saved['mega'])
                ? (bool) $saved['mega'][$key]
                : (!array_key_exists('mega_enabled', $saved) || (bool) $saved['mega_enabled']);
        }
    }
    unset($it);
    return $items;
}

/** Whether $url is something a menu link may point at: a local path, a #section, or an http(s) address. */
function nav_menu_url_ok(string $url): bool
{
    return mb_strlen($url) <= 255 && !preg_match('/\s/', $url)
        && preg_match('~^(?:/(?!/)|#[A-Za-z][A-Za-z0-9_-]*$|https?://[^/]|mailto:|tel:)~i', $url);
}

/**
 * Normalise a menu tree from storage or from the Admin → Menu form. Problems a
 * person must fix are added to $errors (the save is refused); anything
 * structural (wrong types, too deep, too many) is trimmed silently.
 */
function nav_menu_clean($items, array &$errors, int $depth = 0, ?array &$ids = null, ?int &$count = null): array
{
    if ($ids === null) $ids = [];
    if ($count === null) $count = 0;
    $out = [];
    if (!is_array($items)) {
        return $out;
    }
    $str = fn ($v, int $max): string => is_string($v) ? mb_substr(trim($v), 0, $max) : '';
    foreach ($items as $raw) {
        if (!is_array($raw) || $count >= NAV_MENU_MAX_ITEMS) {
            continue;
        }
        $count++;
        $label = $str($raw['label'] ?? '', 80);
        $url = $str($raw['url'] ?? '', 255);
        $name = $label !== '' ? '“' . $label . '”' : 'an item';
        if ($label === '') {
            $errors[] = 'Every menu item needs a label.';
        }
        if ($url !== '' && !nav_menu_url_ok($url)) {
            $errors[] = "The link for $name must be a page path like /about-us, a #section, or a full https:// address.";
        }
        $id = is_string($raw['id'] ?? null) && preg_match('/^[a-z0-9-]{1,40}$/', $raw['id']) && !isset($ids[$raw['id']])
            ? $raw['id'] : 'item-' . bin2hex(random_bytes(4));
        $ids[$id] = true;

        $item = [
            'id' => $id, 'label' => $label, 'url' => $url,
            'visible' => !empty($raw['visible']), 'new_tab' => !empty($raw['new_tab']),
            'children' => $depth < NAV_MENU_MAX_DEPTH ? nav_menu_clean($raw['children'] ?? [], $errors, $depth + 1, $ids, $count) : [],
            'mega' => null,
        ];
        if ($depth === 0 && is_array($raw['mega'] ?? null)) {
            $item['mega'] = nav_menu_clean_mega($raw['mega'], $errors, $name);
        }
        $out[] = $item;
    }
    return $out;
}

function nav_menu_clean_mega(array $m, array &$errors, string $name): array
{
    $str = fn ($v, int $max): string => is_string($v) ? mb_substr(trim($v), 0, $max) : '';
    $hex = fn ($v, string $fallback): string => is_string($v) && preg_match('/^#[0-9a-f]{6}$/i', $v) ? strtolower($v) : $fallback;
    $icons = nav_menu_icons();
    $mega = [
        'enabled' => !empty($m['enabled']),
        'layout' => ($m['layout'] ?? '') === 'tiles' ? 'tiles' : 'columns',
        'columns' => [],
        'cta' => [
            'enabled' => !empty($m['cta']['enabled']),
            'title' => $str($m['cta']['title'] ?? '', 120),
            'text' => $str($m['cta']['text'] ?? '', 300),
            'button' => $str($m['cta']['button'] ?? '', 60) ?: 'Book a Free Consultation →',
        ],
    ];
    foreach (array_slice(is_array($m['columns'] ?? null) ? $m['columns'] : [], 0, NAV_MEGA_MAX_COLUMNS) as $c) {
        if (!is_array($c)) {
            continue;
        }
        $bullets = [];
        foreach (is_array($c['bullets'] ?? null) ? $c['bullets'] : [] as $b) {
            $b = $str($b, 200);
            if ($b !== '' && count($bullets) < NAV_MEGA_MAX_BULLETS) $bullets[] = $b;
        }
        $col = nav_menu_column([
            'icon' => is_string($c['icon'] ?? null) && isset($icons[$c['icon']]) ? $c['icon'] : '',
            'color' => $hex($c['color'] ?? null, '#32b46f'),
            'color_to' => $hex($c['color_to'] ?? null, '#14855a'),
            'title' => $str($c['title'] ?? '', 120),
            'text' => $str($c['text'] ?? '', 200),
            'bullets' => $bullets,
            'case_studies' => $str($c['case_studies'] ?? '', 120),
            'link_label' => $str($c['link_label'] ?? '', 60),
            'link_url' => $str($c['link_url'] ?? '', 255),
        ]);
        if ($mega['enabled'] && $col['title'] === '') {
            $errors[] = "Every column in the mega menu of $name needs a title.";
        }
        if ($mega['enabled'] && $col['link_url'] !== '' && !nav_menu_url_ok($col['link_url'])) {
            $errors[] = "A column link in the mega menu of $name must be a page path, a #section, or a full https:// address.";
        }
        $mega['columns'][] = $col;
    }
    return $mega;
}

/** Where a menu URL points on this site: "/" is the home page, and "#x" / "/#x" are sections of it. */
function nav_menu_href(string $url, string $homeUrl): string
{
    $base = rtrim($homeUrl, '/') ?: '/';
    if ($url === '/') return $homeUrl;
    if (str_starts_with($url, '/#')) return $base . substr($url, 1);
    if (str_starts_with($url, '#')) return $base . $url;
    return $url;
}
