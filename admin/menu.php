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
$defaults = ['mega_enabled' => true, 'visible' => array_fill_keys(array_keys($items), true)];
$config = $defaults;
try {
    $raw = $pdo->query("SELECT `value` FROM settings WHERE `key`='site_navigation'")->fetchColumn();
    $saved = is_string($raw) ? json_decode($raw, true) : null;
    if (is_array($saved)) {
        $config['mega_enabled'] = !array_key_exists('mega_enabled', $saved) || (bool) $saved['mega_enabled'];
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
    $config['mega_enabled'] = isset($_POST['mega_enabled']);
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
    <div class="card-title">Mega menu</div>
    <label class="checkbox-row" style="max-width:440px">
      <input type="checkbox" name="mega_enabled" <?= $config['mega_enabled'] ? 'checked' : '' ?>>
      Enable mega menus on the public site
    </label>
    <div class="field-hint">When off, Solutions, Case Studies, and Industries remain as regular navigation links.</div>
  </div>

  <div class="card">
    <div class="card-title">Navigation links</div>
    <div class="card-desc">Turn links on or off in the desktop navigation and mobile menu.</div>
    <?php foreach ($items as $key => [$label, $destination]): ?>
    <label class="checkbox-row" style="max-width:600px; padding:13px 0; border-bottom:1px solid var(--border)">
      <input type="checkbox" name="visible[<?= h($key) ?>]" <?= $config['visible'][$key] ? 'checked' : '' ?>>
      <span style="flex:1"><?= h($label) ?></span>
      <span style="color:var(--g500);font-size:12px"><?= h($destination) ?></span>
    </label>
    <?php endforeach; ?>
  </div>
  <button type="submit" class="btn btn-primary">Save Menu</button>
</form>
