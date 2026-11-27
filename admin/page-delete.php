<?php
/**
 * Deletes one or more pages (Admin → Pages → Delete / Delete selected). Admins only.
 *
 * Takes id (one page) or ids[] (several). The live homepage (slug "/") is
 * never deleted: point another page at "/" first. Saved Page Content and its
 * history go with each page (foreign keys cascade). Editor access and popup
 * targeting are stored by page id without a foreign key, so they are cleaned
 * up here. All selected pages go in one transaction: if anything fails,
 * nothing is deleted. Afterwards, links to them come out of the header and
 * footer menus (Admin → Menu), so neither is left with a dead link.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/nav-menu.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pages.php');
    exit;
}

csrf_verify();

$ids = isset($_POST['ids']) && is_array($_POST['ids']) ? $_POST['ids'] : [$_POST['id'] ?? 0];
$ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
if (!$ids) {
    header('Location: pages.php?msg=none');
    exit;
}

$in = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT id, slug FROM pages WHERE id IN ($in)");
$stmt->execute($ids);
$found = $stmt->fetchAll();

$skippedHome = false;
$toDelete = [];
$deletedSlugs = [];
foreach ($found as $row) {
    if ($row['slug'] === '/') {
        $skippedHome = true;
        continue;
    }
    $toDelete[] = (int) $row['id'];
    $deletedSlugs[] = $row['slug'];
}

if (!$toDelete) {
    header('Location: pages.php?msg=' . ($skippedHome ? 'home' : 'notfound'));
    exit;
}

try {
    $pdo->beginTransaction();
    $in = implode(',', array_fill(0, count($toDelete), '?'));

    $pdo->prepare("DELETE FROM user_access WHERE item_type = 'page' AND item_id IN ($in)")->execute($toDelete);

    // drop "page:ID" from every popup that targets one of these pages
    try {
        $keys = array_map(fn ($id) => 'page:' . $id, $toDelete);
        $popups = $pdo->query('SELECT id, targets FROM popups')->fetchAll();
        $upd = $pdo->prepare('UPDATE popups SET targets = ? WHERE id = ?');
        foreach ($popups as $p) {
            $current = popup_target_keys($p['targets']);
            $kept = array_values(array_diff($current, $keys));
            if (count($kept) !== count($current)) {
                $upd->execute([implode(',', $kept), $p['id']]);
            }
        }
    } catch (PDOException $e) {
        // popups table not migrated yet: nothing to clean up
    }

    $pdo->prepare("DELETE FROM pages WHERE id IN ($in)")->execute($toDelete);
    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: pages.php?msg=error');
    exit;
}

foreach ($deletedSlugs as $slug) {
    nav_menu_retarget($pdo, $slug, null);
}

header('Location: pages.php?msg=deleted&n=' . count($toDelete) . ($skippedHome ? '&home=1' : ''));
exit;
