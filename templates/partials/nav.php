<?php
/**
 * Shared site nav, included by every public template.
 * Set $activePage (e.g. home9, about-us, blog) before including this to
 * highlight the current link. The look is assets/site-header.css, linked from
 * layout-start.php; the menu's behaviour is the script at the end of this file.
 *
 * The links, their sub menus (two levels deep) and the mega menus come from
 * Admin → Menu; includes/nav-menu.php holds the saved format and the defaults,
 * which are the original six links (Home, About Us, Solutions, Case Studies,
 * Industries, Blog) with their three mega menus. Home 10 is the home page: "/"
 * and the logo go to it, and "/#section" links open sections there. On desktop
 * a mega menu replaces its item's dropdown; the mobile drawer lists the items
 * and their sub items. Admin → Menu is the only place links are added; the
 * footer's links are its Footer tab (footer_menu_links()).
 */
require_once __DIR__ . '/../../includes/nav-menu.php';
$activePage = $activePage ?? '';
$navMenu = nav_menu_load($pdo);
// The home page's address (Admin → Pages decides it). Templates use $homeUrl too.
$homeUrl = nav_home_url($pdo);

// Which link the current page belongs under: the built-in items by id, any
// other item whose address is this page. A parent counts when one of its sub
// items does. site-header.css draws the green pill from aria-current.
$navSection = [
    'home9' => 'home', 'home10' => 'home',
    'about-us' => 'about-us',
    'custom-erp-solution' => 'solutions', 'ecommerce-solutions' => 'solutions',
    'marketing-solutions' => 'solutions', 'crm-solution' => 'solutions', 'business-systems' => 'solutions', 'digital-commerce' => 'solutions',
    'case-studies' => 'case-studies',
    'blog' => 'blog',
][$activePage] ?? (strpos($activePage, 'industry-') === 0 ? 'industries' : '');
$navPath = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/') ?: '/';
$navIsCurrent = function (array $item) use (&$navIsCurrent, $navSection, $navPath): bool {
    if ($item['id'] === $navSection) return true;
    $u = $item['url'];
    if ($u !== '' && $u !== '/' && $u[0] === '/' && !str_contains($u, '#')
        && (rtrim((string) parse_url($u, PHP_URL_PATH), '/') ?: '/') === $navPath) return true;
    foreach ($item['children'] as $c) {
        if ($c['visible'] && $navIsCurrent($c)) return true;
    }
    return false;
};
$navVisible = fn (array $items): array => array_values(array_filter($items, fn ($i) => $i['visible'] && $i['label'] !== ''));
$navLink = function (array $item) use ($homeUrl, $navIsCurrent): string {
    $a = '';
    if ($item['url'] !== '') {
        $a .= ' href="' . h(nav_menu_href($item['url'], $homeUrl)) . '"';
        if ($item['new_tab']) $a .= ' target="_blank" rel="noopener"';
    } else {
        $a .= ' tabindex="0"'; // a parent with no page of its own: focus opens its dropdown
    }
    if ($navIsCurrent($item)) $a .= ' aria-current="true"';
    return '<a' . $a . '>' . h($item['label']) . '</a>';
};
$navSub = function (array $items, int $depth) use (&$navSub, $navLink, $navVisible): void {
    echo '<ul class="sub-menu">';
    foreach ($items as $it) {
        $kids = $depth < NAV_MENU_MAX_DEPTH ? $navVisible($it['children']) : [];
        echo '<li', $kids ? ' class="has-sub"' : '', '>', $navLink($it);
        if ($kids) $navSub($kids, $depth + 1);
        echo '</li>';
    }
    echo '</ul>';
};
$navMega = function (array $mega) use ($pdo, $homeUrl): void {
    $icons = nav_menu_icons();
    $tiles = $mega['layout'] === 'tiles';
    $cta = $mega['cta']['enabled'];
    $n = count($mega['columns']) + ($cta ? 1 : 0);
    echo '<div class="mega-panel"><div class="mega-inner', $tiles ? ' mega-inner-ind"' : '" style="--mega-cols:' . max(1, min($n, 5)) . '"', '>';
    foreach ($mega['columns'] as $c) {
        $icon = isset($icons[$c['icon']])
            ? '<div class="mega-col-icon" style="background:linear-gradient(135deg,' . $c['color'] . ',' . $c['color_to'] . ')">' . $icons[$c['icon']][1] . '</div>' : '';
        $link = $c['link_url'] !== ''
            ? '<a href="' . h(nav_menu_href($c['link_url'], $homeUrl)) . '" class="mega-know">' . h($c['link_label'] ?: 'Know More →') . '</a>' : '';
        if ($tiles) {
            echo '<div class="mega-ind-col">', $icon, '<div class="mega-ind-title">', h($c['title']), '</div>',
                $c['text'] !== '' ? '<div class="mega-ind-desc">' . h($c['text']) . '</div>' : '', $link, '</div>';
            continue;
        }
        echo '<div class="mega-col">', $icon, '<div class="mega-col-title">', h($c['title']), '</div>';
        if ($c['text'] !== '') echo '<p class="mega-col-text">', h($c['text']), '</p>';
        if ($c['case_studies'] !== '') {
            $list = [];
            try {
                $list = get_case_studies_by_service($pdo, $c['case_studies'], 3);
            } catch (Throwable $e) {
                // no case studies table yet: show the empty note
            }
            if ($list) {
                echo '<ul class="mega-list mega-cs-list">';
                foreach ($list as $cs) echo '<li><a href="/case-studies/', h($cs['slug']), '">', h($cs['title']), '</a></li>';
                echo '</ul>';
            } else {
                echo '<p class="mega-cs-empty">Case studies coming soon.</p>';
            }
        } elseif ($c['bullets']) {
            echo '<ul class="mega-list">';
            foreach ($c['bullets'] as $b) echo '<li>', h($b), '</li>';
            echo '</ul>';
        } else {
            echo '<div class="mega-col-fill"></div>';
        }
        echo $link, '</div>';
    }
    if ($cta) {
        echo '<div class="mega-cta">',
            $mega['cta']['title'] !== '' ? '<div class="mega-cta-title">' . h($mega['cta']['title']) . '</div>' : '',
            $mega['cta']['text'] !== '' ? '<p class="mega-cta-text">' . h($mega['cta']['text']) . '</p>' : '',
            '<button type="button" data-book class="mega-cta-btn">', h($mega['cta']['button']), '</button></div>';
    }
    echo '</div></div>';
};
$navDrawer = function (array $items, int $depth) use (&$navDrawer, $navLink, $navVisible): void {
    foreach ($items as $it) {
        echo $it['url'] !== '' ? $navLink($it) : '<span class="nav-drawer-label">' . h($it['label']) . '</span>';
        $kids = $depth < NAV_MENU_MAX_DEPTH ? $navVisible($it['children']) : [];
        if ($kids) {
            echo '<div class="nav-drawer-sub">';
            $navDrawer($kids, $depth + 1);
            echo '</div>';
        }
    }
};
?>
<nav>
 <a href="<?= h($homeUrl) ?>" class="logo" aria-label="Drawlead — home"><img src="/assets/drawlead-logo.webp" alt="Drawlead — Digital Transformation Company" width="705" height="138"></a>
 <ul class="nav-links">
 <?php foreach ($navVisible($navMenu) as $navItem):
   $navHasMega = $navItem['mega'] && $navItem['mega']['enabled'] && ($navItem['mega']['columns'] || $navItem['mega']['cta']['enabled']);
   $navKids = $navVisible($navItem['children']);
   $navClass = $navHasMega ? 'has-mega' : ($navKids ? 'has-sub' : ''); ?>
  <li<?= $navClass ? ' class="' . $navClass . '"' : '' ?>><?= $navLink($navItem) ?><?php if ($navHasMega) $navMega($navItem['mega']); elseif ($navKids) $navSub($navKids, 1); ?></li>
 <?php endforeach; ?>
 </ul>
 <div class="nav-end">
  <?php if ($activePage === 'home8' || $activePage === 'home9'): ?>
  <!-- Home 8 and Home 9 only: the dark/light switch. Its styles are in
       assets/home8-base.css and the sheet it switches is assets/home8.css. Both
       pages keep the choice under the same key, so it carries between them. -->
  <button type="button" class="theme-toggle" id="themeToggle" aria-pressed="true" aria-label="Dark mode" title="Switch to light mode">
   <svg class="tt-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
   <svg class="tt-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
  </button>
  <?php endif; ?>
  <button type="button" data-book class="nav-btn">Free Consultation Call</button>
  <button type="button" class="nav-burger" aria-expanded="false" aria-controls="navDrawer" aria-label="Open menu">
   <svg class="nb-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
   <svg class="nb-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
  </button>
 </div>
 <div class="nav-drawer" id="navDrawer" hidden>
  <?php $navDrawer($navVisible($navMenu), 0); ?>
  <button type="button" data-book class="nav-drawer-cta">Free Consultation Call</button>
 </div>
</nav>
<?php if ($activePage === 'home8' || $activePage === 'home9'): ?>
<script>
(function(){
 // Home 8's theme toggle. Dark is the page's own theme and the default; light
 // switches the dark sheet (link#home8Dark) off so Home 7's colours show.
 // The page applies a saved choice before first paint (see the script after
 // that <link> in templates/home8-body.php); this only handles the clicks.
 var btn = document.getElementById('themeToggle');
 if (!btn) return;
 function sync(light){
  btn.setAttribute('aria-pressed', light ? 'false' : 'true');
  btn.title = light ? 'Switch to dark mode' : 'Switch to light mode';
 }
 var saved = null;
 try { saved = localStorage.getItem('home8-theme'); } catch (e) {}
 sync(saved === 'light');
 btn.addEventListener('click', function(){
  var sheet = document.getElementById('home8Dark');
  if (!sheet) return;
  var light = document.documentElement.getAttribute('data-theme') !== 'light';
  sheet.media = light ? 'not all' : 'all';
  if (light) document.documentElement.setAttribute('data-theme', 'light');
  else document.documentElement.removeAttribute('data-theme');
  sync(light);
  try { localStorage.setItem('home8-theme', light ? 'light' : 'dark'); } catch (e) {}
 });
})();
</script>
<?php endif; ?>
<script>
(function(){
 var nav = document.querySelector('body > nav');
 if (!nav) return;

 // The floating bar turns more opaque once the page has been scrolled.
 function onScroll(){ nav.classList.toggle('is-scrolled', window.scrollY > 24); }
 window.addEventListener('scroll', onScroll, {passive: true});
 onScroll();

 // Mobile menu: the burger replaces the links at 960px and below.
 var burger = nav.querySelector('.nav-burger'), drawer = document.getElementById('navDrawer');
 function setDrawer(open){
  burger.setAttribute('aria-expanded', open ? 'true' : 'false');
  burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
  drawer.hidden = !open;
 }
 burger.addEventListener('click', function(){ setDrawer(drawer.hidden); });
 drawer.addEventListener('click', function(e){ if (e.target.closest('a, button')) setDrawer(false); });
 document.addEventListener('keydown', function(e){ if (e.key === 'Escape') setDrawer(false); });
 document.addEventListener('click', function(e){ if (!drawer.hidden && !nav.contains(e.target)) setDrawer(false); });
})();
(function(){
 // Pure-CSS :hover closes the mega menu the instant the mouse leaves the
 // trigger <li>, so a fast/diagonal move toward the panel below can drop
 // out of :hover before reaching the buttons. This adds a short grace
 // period (hover-intent) via a JS-driven class, on top of the existing
 // CSS :hover/:focus-within, so the panel stays open long enough to reach.
 // The same goes for the dropdown sub menus (.has-sub). Opening one closes
 // its open neighbours at once, and a dropdown that would run off the right
 // edge of the window opens leftwards (.sub-left) instead.
 document.querySelectorAll('body > nav .has-mega, body > nav .has-sub').forEach(function(li){
  var closeTimer = null;
  function open(){
   clearTimeout(closeTimer);
   Array.prototype.forEach.call(li.parentElement.children, function(sib){
    if (sib !== li) sib.classList.remove('mega-open');
   });
   li.classList.add('mega-open');
   var sub = li.querySelector(':scope > .sub-menu');
   if (sub) {
    li.classList.remove('sub-left');
    if (sub.getBoundingClientRect().right > window.innerWidth - 8) li.classList.add('sub-left');
   }
  }
  function scheduleClose(){
   clearTimeout(closeTimer);
   closeTimer = setTimeout(function(){ li.classList.remove('mega-open'); }, 350);
  }
  li.addEventListener('mouseenter', open);
  li.addEventListener('mouseleave', scheduleClose);
  li.addEventListener('focusin', open);
  li.addEventListener('focusout', function(e){
   if (!li.contains(e.relatedTarget)) scheduleClose();
  });
 });
})();
</script>
