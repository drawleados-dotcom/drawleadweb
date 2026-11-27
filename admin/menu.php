<?php
/**
 * Admin → Menu: the only place the site's links are set. Two tabs:
 *
 *   Header — the navigation bar. Items can be added (a page or a custom link),
 *            edited, hidden, removed, and dragged into order and into sub
 *            menus up to two levels deep; top-level items can carry an
 *            editable mega menu. Drawn by templates/partials/nav.php.
 *   Footer — the footer's two rows of plain links (footer_menu_rows()), drawn
 *            by every footer in templates/ through footer_menu_links().
 *
 * The builder is admin/assets/menu-builder.js, which posts the tab's lists as
 * JSON; includes/nav-menu.php checks and stores them. A page's URL change or
 * delete in Admin → Pages updates the links here (nav_menu_retarget()).
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/nav-menu.php';
require_admin();

$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$tab = (($isPost ? $_POST['menu'] ?? '' : $_GET['menu'] ?? '') === 'footer') ? 'footer' : 'header';
$errors = [];
$header = $tab === 'header' ? nav_menu_load($pdo) : [];
$footer = $tab === 'footer' ? footer_menu_load($pdo) : [];

if ($isPost) {
    csrf_verify();
    $posted = json_decode((string) ($_POST['menu_json'] ?? ''), true);
    if (!is_array($posted) || ($tab === 'header' && !is_array($posted['header'] ?? null))) {
        $errors[] = 'The menu could not be read. Reload the page and try again.';
    } else {
        // On a problem the page shows what was posted, so no edits are lost.
        if ($tab === 'footer') {
            $footer = footer_menu_clean($posted, $errors);
        } else {
            $header = nav_menu_clean($posted['header'], $errors);
        }
        if (!$errors) {
            try {
                if ($tab === 'footer') {
                    footer_menu_save($pdo, $footer);
                } else {
                    nav_menu_save($pdo, $header);
                }
                header('Location: menu.php?' . ($tab === 'footer' ? 'menu=footer&' : '') . 'saved=1');
                exit;
            } catch (PDOException $e) {
                error_log('admin/menu.php: ' . $e->getMessage());
                $errors[] = 'Could not save the menu. Please try again.';
            }
        }
    }
}
$errors = array_values(array_unique($errors));

// What the "Add menu items" box offers.
$addGroups = ['Pages' => [], 'Site sections' => [['Blog', '/blog'], ['Case Studies', '/case-studies']], 'Industries' => []];
try {
    foreach ($pdo->query("SELECT name, slug FROM pages WHERE COALESCE(status, 'published') <> 'draft' ORDER BY name") as $p) {
        $addGroups['Pages'][] = [$p['name'], $p['slug']];
    }
} catch (Throwable $e) {
    // no pages table: the other groups still work
}
foreach (industries_ordered() as $entry) {
    $addGroups['Industries'][] = [$entry['industry']['name'], '/industry-' . $entry['key']];
}

// The lists on this tab: key => [title, description (HTML), builder settings].
$lists = [];
if ($tab === 'header') {
    $lists['header'] = ['Header menu', 'Drag <span class="mb-key">⠿</span> to reorder. Drop an item a little to the right, below another item, to make it a sub item; sub items can have their own sub items. The arrow buttons do the same. Click an item to edit it, hide it, or give a top-level item a mega menu.',
        ['items' => $header, 'maxDepth' => NAV_MENU_MAX_DEPTH, 'mega' => true, 'needsUrl' => false, 'showLabel' => 'Show in menu']];
} else {
    foreach (footer_menu_rows() as $key => [$rowName, $rowWhere]) {
        $lists[$key] = [$rowName, h($rowWhere) . ' Drag <span class="mb-key">⠿</span> or use the arrows to reorder; click a link to edit or hide it.',
            ['items' => $footer[$key] ?? [], 'maxDepth' => 0, 'mega' => false, 'needsUrl' => true, 'showLabel' => 'Show in footer']];
    }
}
$services = [];
if ($tab === 'header') {
    try {
        $services = array_column(get_case_study_services($pdo), 'name');
    } catch (Throwable $e) {
        // no case study services yet
    }
}

$builderData = [
    'lists' => array_map(fn ($l) => $l[2], $lists),
    'icons' => $tab === 'header' ? nav_menu_icons() : [],
    'services' => $services,
    'maxColumns' => NAV_MEGA_MAX_COLUMNS,
    'maxBullets' => NAV_MEGA_MAX_BULLETS,
];

$pageTitle = 'Menu';
$pageSub = 'The site’s header and footer links. Every page uses them.';
$activeNav = 'menu';
include __DIR__ . '/includes/header.php';
?>

<div class="page-tabs">
  <a class="page-tab<?= $tab === 'header' ? ' active' : '' ?>" href="menu.php"<?= $tab === 'header' ? ' aria-current="page"' : '' ?>>Header</a>
  <a class="page-tab<?= $tab === 'footer' ? ' active' : '' ?>" href="menu.php?menu=footer"<?= $tab === 'footer' ? ' aria-current="page"' : '' ?>>Footer</a>
</div>

<?php if (isset($_GET['saved']) && !$errors): ?><div class="alert alert-success"><?= $tab === 'footer' ? 'Footer saved. Every page’s footer shows it now.' : 'Header menu saved. Every page’s header shows it now.' ?></div><?php endif; ?>
<?php if ($errors): ?>
<div class="alert alert-error">
  The menu wasn't saved. Fix these and save again:
  <ul class="alert-list"><?php foreach ($errors as $err): ?><li><?= h($err) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="post" id="menuForm" action="menu.php<?= $tab === 'footer' ? '?menu=footer' : '' ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="menu" value="<?= h($tab) ?>">
  <input type="hidden" name="menu_json" id="menuJson" value="">
  <div class="mb-layout">
    <div class="mb-main">
      <?php foreach ($lists as $key => [$title, $desc]): ?>
      <div class="card">
        <div class="card-title"><?= h($title) ?></div>
        <div class="card-desc"><?= $desc ?></div>
        <ol class="mb-list" data-list="<?= h($key) ?>" aria-label="<?= h($title) ?>"></ol>
        <div class="empty-state mb-empty" hidden><p>No links yet. Add pages or links from the panel on the right.</p></div>
      </div>
      <?php endforeach; ?>
    </div>

    <aside class="mb-side">
      <div class="card">
        <div class="card-title">Add menu items</div>
        <?php if (count($lists) > 1): ?>
        <div class="field">
          <label for="mbAddTo">Add to</label>
          <select id="mbAddTo">
            <?php foreach ($lists as $key => [$title]): ?><option value="<?= h($key) ?>"><?= h($title) ?></option><?php endforeach; ?>
          </select>
          <div class="field-hint">New items go to the end of that list. Drag them where you want them.</div>
        </div>
        <?php else: ?>
        <div class="card-desc">New items go to the end of the menu. Drag them where you want them.</div>
        <?php endif; ?>
        <div class="field">
          <label for="mbPage">Page</label>
          <select id="mbPage">
            <?php foreach ($addGroups as $group => $opts): if (!$opts) continue; ?>
            <optgroup label="<?= h($group) ?>">
              <?php foreach ($opts as [$name, $url]): ?><option value="<?= h($url) ?>" data-label="<?= h($name) ?>"><?= h($name) ?> — <?= h($url) ?></option><?php endforeach; ?>
            </optgroup>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="button" class="btn btn-ghost btn-sm" id="mbAddPage">Add page</button>

        <div class="mb-divider"></div>

        <div class="field">
          <label for="mbLinkLabel">Custom link: label</label>
          <input type="text" id="mbLinkLabel" maxlength="80" placeholder="e.g. Pricing">
        </div>
        <div class="field">
          <label for="mbLinkUrl">Custom link: address</label>
          <input type="text" id="mbLinkUrl" maxlength="255" placeholder="/pricing, #section or https://…">
          <?php if ($tab === 'header'): ?><div class="field-hint">Leave the address empty for a heading that only opens its sub menu.</div><?php endif; ?>
        </div>
        <div class="mb-add-err" id="mbAddErr" hidden></div>
        <button type="button" class="btn btn-ghost btn-sm" id="mbAddLink">Add link</button>
      </div>
    </aside>
  </div>

  <div class="mb-savebar">
    <button type="submit" class="btn btn-primary"><?= $tab === 'footer' ? 'Save Footer' : 'Save Header Menu' ?></button>
    <span class="mb-dirty" id="mbDirty" hidden>Unsaved changes</span>
  </div>
</form>

<script type="application/json" id="menuData"><?= json_encode($builderData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>

<style>
.mb-layout{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:20px;align-items:start}
.mb-side{position:sticky;top:20px}
.mb-key{display:inline-block;padding:0 5px;border:1px solid var(--border);border-radius:4px;background:var(--white);font-weight:800;color:var(--g600)}
.mb-divider{height:1px;background:var(--border);margin:1.2rem 0}
.mb-add-err{font-size:12px;font-weight:600;color:var(--danger);margin:-.4rem 0 .8rem}
.mb-list{list-style:none;margin:1rem 0 0;padding:0;display:flex;flex-direction:column;gap:6px}
.mb-item,.mb-ph{margin-left:calc(var(--d, 0) * 34px)}
.mb-item{background:var(--white);border:1.5px solid var(--border);border-radius:10px;transition:margin-left .15s,border-color .15s}
.mb-item.is-open{border-color:var(--g300)}
.mb-item.mb-new{animation:mbFlash 1.6s ease}
@keyframes mbFlash{0%,30%{background:#e9f9f0;border-color:#9fdcbc}}
.mb-bar{display:flex;align-items:center;gap:6px;padding:5px 6px;min-height:50px}
.mb-handle{flex-shrink:0;width:30px;height:36px;border:0;border-radius:6px;background:none;color:var(--g400);cursor:grab;touch-action:none;display:flex;align-items:center;justify-content:center}
.mb-handle:hover{background:var(--bg);color:var(--black)}
.mb-toggle{flex:1;min-width:0;display:flex;align-items:baseline;gap:10px;padding:8px 4px;border:0;background:none;font:inherit;text-align:left;cursor:pointer;color:inherit}
.mb-title{font-size:13.5px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:55%}
.mb-url{font-size:11.5px;font-weight:500;color:var(--g400);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mb-badges{display:flex;gap:4px;flex-shrink:0}
.mb-badge{font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;padding:3px 8px;border-radius:20px;background:var(--bg);color:var(--g500);white-space:nowrap}
.mb-badge[hidden]{display:none}
.mb-badge-mega{background:#e9f9f0;color:#0f7a4c}
.mb-moves{display:flex;gap:1px;flex-shrink:0}
.mb-moves button,.mb-chev{width:28px;height:30px;border:0;border-radius:6px;background:none;color:var(--g500);cursor:pointer;display:flex;align-items:center;justify-content:center}
.mb-moves button:hover:not(:disabled),.mb-chev:hover{background:var(--bg);color:var(--black)}
.mb-moves button:disabled{opacity:.22;cursor:default}
.mb-chev svg{transition:transform .2s}
.mb-item.is-open .mb-chev svg{transform:rotate(180deg)}
.mb-panel{border-top:1px solid var(--border);padding:1rem 1.1rem 1rem}
.mb-grid{display:grid;grid-template-columns:1fr 1.4fr;gap:0 12px}
.mb-checks{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:.9rem}
.mb-checks .checkbox-row{cursor:pointer}
.mb-checks [hidden],.mb-empty[hidden]{display:none}
.mb-main{display:flex;flex-direction:column;gap:20px;min-width:0}
.mb-main>.card+.card{margin-top:0}
.mb-note{font-size:12px;line-height:1.5;color:#8a5a14;background:#fdf6e8;border:1px solid #f1dcb2;border-radius:8px;padding:8px 11px;margin-bottom:.9rem}
.mb-note[hidden]{display:none}
.mb-panel-foot{display:flex;justify-content:flex-end}
.mb-ph{border:2px dashed var(--blue);border-radius:10px;background:#f1faf5;list-style:none}
.mb-ghost{position:fixed;z-index:1000;margin:0;padding:0;list-style:none;pointer-events:none;display:flex;flex-direction:column;gap:6px;opacity:.95;filter:drop-shadow(0 16px 30px rgba(0,0,0,.18))}
.mb-ghost .mb-panel{display:none}
body.mb-dragging,body.mb-dragging *{cursor:grabbing!important;user-select:none!important}
/* mega menu editor */
.mb-mega{border:1.5px solid var(--border);border-radius:10px;padding:1rem;background:#fafaf9;margin-bottom:.9rem}
.mb-mega[hidden]{display:none}
.mb-mega-head{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;flex-wrap:wrap}
.mb-mega-head .field{margin-bottom:.8rem;min-width:260px}
.mb-sub-h{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--g500);margin:.2rem 0 .5rem}
.mb-cols{display:flex;flex-direction:column;gap:6px;margin-bottom:.7rem}
.mb-col{background:var(--white);border:1.5px solid var(--border);border-radius:8px}
.mb-col>summary{display:flex;align-items:center;gap:10px;padding:7px 8px 7px 10px;cursor:pointer;list-style:none}
.mb-col>summary::-webkit-details-marker{display:none}
.mb-col-icon{width:28px;height:28px;flex-shrink:0;border-radius:7px;display:flex;align-items:center;justify-content:center;background:var(--g300)}
.mb-col-icon svg{width:15px;height:15px}
.mb-col-name{flex:1;min-width:0;font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mb-col-name:empty::before{content:'Untitled column';color:var(--g400);font-weight:600}
.mb-col-tools{display:flex;gap:1px}
.mb-col-tools button{height:28px;min-width:28px;padding:0 6px;border:0;border-radius:6px;background:none;color:var(--g500);font:inherit;font-size:11.5px;font-weight:700;cursor:pointer}
.mb-col-tools button:hover:not(:disabled){background:var(--bg);color:var(--black)}
.mb-col-tools button:disabled{opacity:.22;cursor:default}
.mb-col-tools .mb-col-del:hover{color:var(--danger);background:#fdecef}
.mb-col-body{display:grid;grid-template-columns:1fr 1fr;gap:0 12px;padding:.4rem 1rem .2rem;border-top:1px solid var(--border)}
.mb-col-body .mb-span{grid-column:1/-1}
.mb-colors{display:flex;gap:8px}
.mb-colors input[type=color]{width:46px;height:42px;padding:3px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);cursor:pointer}
.mb-mega.is-tiles .mb-only-columns{display:none}
.mb-cta{border-top:1px solid var(--border);margin-top:.9rem;padding-top:.9rem}
.mb-cta-fields{display:grid;grid-template-columns:1fr 1fr;gap:0 12px;margin-top:.8rem}
.mb-cta-fields[hidden]{display:none}
.mb-savebar{position:sticky;bottom:0;z-index:5;display:flex;align-items:center;gap:14px;padding:14px 0 10px;background:linear-gradient(rgba(244,245,244,0),var(--bg) 35%)}
.mb-dirty{font-size:12px;font-weight:700;color:#8a5a14}
@media(max-width:1100px){.mb-layout{grid-template-columns:1fr}.mb-side{position:static;order:-1}}
@media(max-width:700px){
  .mb-item,.mb-ph{margin-left:calc(var(--d, 0) * 16px)}
  .mb-grid,.mb-col-body,.mb-cta-fields{grid-template-columns:1fr}
  .mb-url,.mb-badges{display:none}
}
</style>

<script src="<?= asset_url('/admin/assets/menu-builder.js') ?>"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
