<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$items = [
    'home' => ['Home', '/'],
    'about-us' => ['About Us', '/about-us'],
    'solutions' => ['Solutions', '/#solutions'],
    'case-studies' => ['Case Studies', '/#case-studies'],
    'industries' => ['Industries', '/#industries'],
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
            if (isset($saved['items'][$key]) && is_array($saved['items'][$key])) {
                if (isset($saved['items'][$key]['label']) && is_string($saved['items'][$key]['label'])) $items[$key][0] = $saved['items'][$key]['label'];
                if (isset($saved['items'][$key]['url']) && is_string($saved['items'][$key]['url'])) $items[$key][1] = $saved['items'][$key]['url'];
            }
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
        $label = trim((string) ($_POST['label'][$key] ?? ''));
        $url = trim((string) ($_POST['url'][$key] ?? ''));
        if ($label === '' || mb_strlen($label) > 80) {
            $error = 'Each navigation label must contain 1 to 80 characters.';
        } elseif ($url === '' || mb_strlen($url) > 255 || preg_match('/\s/', $url) || !preg_match('~^(?:/(?!/)|#[A-Za-z][A-Za-z0-9_-]*|https?://)~i', $url)) {
            $error = 'Use a local path, a #section anchor, or a full http/https URL for each destination.';
        }
        $items[$key] = [$label, $url];
    }
    if (!$error) {
        $config['items'] = [];
        foreach ($items as $key => [$label, $url]) $config['items'][$key] = ['label' => $label, 'url' => $url];
        try {
            $stmt = $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
            $stmt->execute(['site_navigation', json_encode($config, JSON_UNESCAPED_SLASHES)]);
            $success = 'Navigation settings saved.';
        } catch (PDOException $e) {
            $error = 'Could not save navigation settings. Please try again.';
        }
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
    <div style="display:grid;grid-template-columns:minmax(200px,1fr) minmax(200px,1.3fr) 120px 140px;gap:12px;padding:10px 0;color:var(--g500);font-size:12px;font-weight:700">
      <span>Label</span><span>Destination</span><span>Show link</span><span>Mega menu</span>
    </div>
    <?php foreach ($items as $key => [$label, $destination]): ?>
    <div style="display:grid;grid-template-columns:minmax(200px,1fr) minmax(200px,1.3fr) 120px 140px;gap:12px;align-items:center;padding:13px 0;border-top:1px solid var(--border)">
      <input type="text" name="label[<?= h($key) ?>]" maxlength="80" value="<?= h($label) ?>" aria-label="<?= h($label) ?> label">
      <input type="text" name="url[<?= h($key) ?>]" maxlength="255" value="<?= h($destination) ?>" aria-label="<?= h($label) ?> destination">
      <label class="checkbox-row"><input type="checkbox" name="visible[<?= h($key) ?>]" <?= $config['visible'][$key] ? 'checked' : '' ?>> Show</label>
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
