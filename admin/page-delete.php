<?php
/**
 * Deletes a page (Admin → Pages → Delete). Admins only.
 *
 * The live homepage (slug "/") can't be deleted: point another page at "/"
 * first. Saved Page Content and its history go with the page (foreign keys
 * cascade). Editor access and popup targeting are stored by page id without
 * a foreign key, so they are cleaned up here. Menu links come from the
 * page's own "show in menu" flag, so it leaves the menu automatically.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pages.php');
    exit;
}

csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT id, slug FROM pages WHERE id = ?');
$stmt->execute([$id]);
$page = $stmt->fetch();

if (!$page) {
    header('Location: pages.php?msg=notfound');
    exit;
}
if ($page['slug'] === '/') {
    header('Location: pages.php?msg=home');
    exit;
}

try {
    $pdo->beginTransaction();

    $pdo->prepare("DELETE FROM user_access WHERE item_type = 'page' AND item_id = ?")->execute([$id]);

    // drop "page:ID" from every popup that targets this page
    try {
        $key = 'page:' . $id;
        $popups = $pdo->query('SELECT id, targets FROM popups')->fetchAll();
        $upd = $pdo->prepare('UPDATE popups SET targets = ? WHERE id = ?');
        foreach ($popups as $p) {
            $keys = popup_target_keys($p['targets']);
            if (in_array($key, $keys, true)) {
                $upd->execute([implode(',', array_values(array_diff($keys, [$key]))), $p['id']]);
            }
        }
    } catch (PDOException $e) {
        // popups table not migrated yet: nothing to clean up
    }

    $pdo->prepare('DELETE FROM pages WHERE id = ?')->execute([$id]);
    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: pages.php?msg=error');
    exit;
}

header('Location: pages.php?msg=deleted');
exit;
