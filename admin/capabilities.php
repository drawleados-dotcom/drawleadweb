<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$cards = [
    'systems' => ['Business Systems', 'Custom software & operating systems'],
    'commerce' => ['Digital Commerce', 'High-performing ecommerce platforms and connected stores'],
    'automation' => ['AI & Automation', 'Intelligent workflows that reduce manual work'],
    'growth' => ['Marketing & Growth', 'Data-driven digital marketing strategies'],
];
$order = array_keys($cards);
try {
    $raw = $pdo->query("SELECT `value` FROM settings WHERE `key`='home10_capabilities_order'")->fetchColumn();
    $saved = is_string($raw) ? json_decode($raw, true) : null;
    if (is_array($saved) && isset($saved['order']) && is_array($saved['order'])) {
        $order = array_values(array_unique(array_filter($saved['order'], static fn ($id) => is_string($id) && isset($cards[$id]))));
        $order = array_merge($order, array_values(array_diff(array_keys($cards), $order)));
    }
} catch (PDOException $e) {
    // Keep the default card order when settings are unavailable.
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $posted = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : [];
    $order = array_values(array_unique(array_filter($posted, static fn ($id) => is_string($id) && isset($cards[$id]))));
    $order = array_merge($order, array_values(array_diff(array_keys($cards), $order)));
    try {
        $stmt = $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
        $stmt->execute(['home10_capabilities_order', json_encode(['order' => $order])]);
        $success = 'Core Capabilities order saved.';
    } catch (PDOException $e) {
        $error = 'Could not save the card order. Please try again.';
    }
}

$pageTitle = 'Core Capabilities';
$pageSub = 'Reorder the cards and matching panels in the homepage Core Capabilities section.';
$activeNav = 'capabilities';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="post">
  <?= csrf_field() ?>
  <div class="card">
    <div class="card-title">Core Capabilities card order</div>
    <div class="card-desc">Move cards with the arrows. Each card stays paired with its expanded panel, and numbers update to match the new order.</div>
    <div id="capabilityList" style="display:grid;gap:10px">
      <?php foreach ($order as $index => $id): ?>
      <div class="capability-row" style="display:grid;grid-template-columns:52px minmax(220px,1fr) auto;gap:14px;align-items:center;padding:14px;border:1px solid var(--border);border-radius:12px;background:var(--white)">
        <strong data-number style="font-size:18px;color:var(--blue)"><?= sprintf('%02d', $index + 1) ?></strong>
        <input type="hidden" name="order[]" value="<?= h($id) ?>">
        <div><strong><?= h($cards[$id][0]) ?></strong><div class="field-hint"><?= h($cards[$id][1]) ?></div></div>
        <span style="display:flex;gap:6px">
          <button type="button" class="btn btn-ghost btn-sm" data-move="-1" aria-label="Move <?= h($cards[$id][0]) ?> up">↑</button>
          <button type="button" class="btn btn-ghost btn-sm" data-move="1" aria-label="Move <?= h($cards[$id][0]) ?> down">↓</button>
        </span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Save Card Order</button>
</form>
<script>
(function(){
  var list = document.getElementById('capabilityList');
  if (!list) return;
  function sync(){
    var rows = Array.from(list.querySelectorAll('.capability-row'));
    rows.forEach(function(row, index){
      row.querySelector('[data-number]').textContent = String(index + 1).padStart(2, '0');
      row.querySelector('[data-move="-1"]').disabled = index === 0;
      row.querySelector('[data-move="1"]').disabled = index === rows.length - 1;
    });
  }
  list.addEventListener('click', function(event){
    var button = event.target.closest('[data-move]');
    if (!button) return;
    var row = button.closest('.capability-row');
    var sibling = Number(button.dataset.move) < 0 ? row.previousElementSibling : row.nextElementSibling;
    if (!sibling) return;
    if (Number(button.dataset.move) < 0) list.insertBefore(row, sibling);
    else list.insertBefore(sibling, row);
    sync();
  });
  sync();
})();
</script>
