<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$items = [
    'home' => ['Home', 'Homepage'],
    'about-us' => ['About Us', '/about-us'],
    'solutions' => ['Solutions', 'Homepage #solutions'],
    'case-studies' => ['Case Studies', '/case-studies'],
    'industries' => ['Industries', 'Homepage #industries'],
    'blog' => ['Blog', '/blog'],
];
$megaItems = ['solutions', 'case-studies', 'industries'];
$defaults = [
    'mega' => array_fill_keys($megaItems, true),
    'visible' => array_fill_keys(array_keys($items), true),
];
$config = $defaults;
try {
    $raw = $pdo->query("SELECT `value` FROM settings WHERE `key`='site_navigation'")->fetchColumn();
    $saved = is_string($raw) ? json_decode($raw, true) : null;
    if (is_array($saved)) {
        foreach ($megaItems as $key) {
            $config['mega'][$key] = isset($saved['mega']) && is_array($saved['mega']) && array_key_exists($key, $saved['mega'])
                ? (bool) $saved['mega'][$key]
                : (!array_key_exists('mega_enabled', $saved) || (bool) $saved['mega_enabled']);
        }
        foreach ($items as $key => $_) {
            if (isset($saved['visible']) && is_array($saved['visible']) && array_key_exists($key, $saved['visible'])) {
                $config['visible'][$key] = (bool) $saved['visible'][$key];
            }
        }
    }
} catch (PDOException $e) {
    // The settings table is part of the base schema; keep defaults if unavailable.
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    foreach ($megaItems as $key) {
        $config['mega'][$key] = isset($_POST['mega'][$key]);
    }
    foreach ($items as $key => $_) {
        $config['visible'][$key] = isset($_POST['visible'][$key]);
    }
    try {
        $stmt = $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
        $stmt->execute(['site_navigation', json_encode($config, JSON_UNESCAPED_SLASHES)]);
        $success = 'Navigation settings saved.';
    } catch (PDOException $e) {
        $error = 'Could not save navigation settings. Please try again.';
    }
}

$pageTitle = 'Menu';
$pageSub = 'Choose which links appear in the site navigation and control its mega menu.';
$activeNav = 'menu';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="post">
  <?= csrf_field() ?>
  <div class="card">
    <div class="card-title">Navigation links</div>
    <div class="card-desc">Control link visibility and each dropdown mega menu. Mega menus are available for Solutions, Case Studies, and Industries.</div>
    <div style="display:grid;grid-template-columns:minmax(220px,1fr) 150px 150px;gap:12px;padding:10px 0;color:var(--g500);font-size:12px;font-weight:700">
      <span>Navigation item</span><span>Show link</span><span>Mega menu</span>
    </div>
    <?php foreach ($items as $key => [$label, $destination]): ?>
    <div style="display:grid;grid-template-columns:minmax(220px,1fr) 150px 150px;gap:12px;align-items:center;padding:13px 0;border-top:1px solid var(--border)">
      <span><strong><?= h($label) ?></strong><small style="display:block;color:var(--g500);margin-top:4px"><?= h($destination) ?></small></span>
      <label class="checkbox-row"><input type="checkbox" name="visible[<?= h($key) ?>]" <?= $config['visible'][$key] ? 'checked' : '' ?>> Show link</label>
      <?php if (in_array($key, $megaItems, true)): ?>
      <label class="checkbox-row"><input type="checkbox" name="mega[<?= h($key) ?>]" <?= $config['mega'][$key] ? 'checked' : '' ?>> Enabled</label>
      <?php else: ?>
      <span style="color:var(--g500);font-size:13px">Not configured</span>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <button type="submit" class="btn btn-primary">Save Menu</button>
</form>
