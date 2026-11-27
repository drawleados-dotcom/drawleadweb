<?php
/**
 * Admin → Menu: build the site navigation. Items can be added (a page or a
 * custom link), edited, hidden, removed, and dragged into order and into sub
 * menus up to two levels deep; top-level items can carry an editable mega
 * menu. The builder is admin/assets/menu-builder.js, which posts the whole
 * tree as JSON; includes/nav-menu.php checks it, and templates/partials/nav.php
 * draws it.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/nav-menu.php';
require_admin();

$errors = [];
$items = nav_menu_load($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $posted = json_decode((string) ($_POST['menu_json'] ?? ''), true);
    if (!is_array($posted)) {
        $errors[] = 'The menu could not be read. Reload the page and try again.';
    } else {
        // On a problem the page shows what was posted, so no edits are lost.
        $items = nav_menu_clean($posted, $errors);
        if (!$errors) {
            try {
                $stmt = $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
                $stmt->execute(['site_navigation', json_encode(['version' => 2, 'items' => $items], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
                header('Location: menu.php?saved=1');
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
$services = [];
try {
    $services = array_column(get_case_study_services($pdo), 'name');
} catch (Throwable $e) {
    // no case study services yet
}

$builderData = [
    'items' => $items,
    'icons' => nav_menu_icons(),
    'services' => $services,
    'maxDepth' => NAV_MENU_MAX_DEPTH,
    'maxColumns' => NAV_MEGA_MAX_COLUMNS,
    'maxBullets' => NAV_MEGA_MAX_BULLETS,
];

$pageTitle = 'Menu';
$pageSub = 'Build the site navigation: links, sub menus and mega menus.';
$activeNav = 'menu';
include __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['saved']) && !$errors): ?><div class="alert alert-success">Menu saved. The site header shows it now.</div><?php endif; ?>
<?php if ($errors): ?>
<div class="alert alert-error">
  The menu wasn't saved. Fix these and save again:
  <ul class="alert-list"><?php foreach ($errors as $err): ?><li><?= h($err) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="post" id="menuForm">
  <?= csrf_field() ?>
  <input type="hidden" name="menu_json" id="menuJson" value="">
  <div class="mb-layout">
    <div class="card mb-main">
      <div class="card-title">Menu structure</div>
      <div class="card-desc">
        Drag <span class="mb-key">⠿</span> to reorder. Drop an item a little to the right, below another item, to make it a sub item;
        sub items can have their own sub items. The arrow buttons do the same. Click an item to edit it, hide it, or give a
        top-level item a mega menu.
      </div>
      <ol class="mb-list" id="mbList" aria-label="Menu items"></ol>
      <div class="empty-state" id="mbEmpty" hidden><p>The menu is empty. Add pages or links from the panel on the right.</p></div>
    </div>

    <aside class="mb-side">
      <div class="card">
        <div class="card-title">Add menu items</div>
        <div class="card-desc">New items go to the end of the menu. Drag them where you want them.</div>
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
          <div class="field-hint">Leave the address empty for a heading that only opens its sub menu.</div>
        </div>
        <div class="mb-add-err" id="mbAddErr" hidden></div>
        <button type="button" class="btn btn-ghost btn-sm" id="mbAddLink">Add link</button>
      </div>
    </aside>
  </div>

  <div class="mb-savebar">
    <button type="submit" class="btn btn-primary">Save Menu</button>
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
.mb-checks [hidden],#mbEmpty[hidden]{display:none}
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
