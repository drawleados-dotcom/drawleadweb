<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$projects = [
    'construction' => ['Construction & Real Estate', 'Project management, site operations, procurement and finance.'],
    'ecommerce' => ['Ecommerce Solutions', 'Custom Shopify storefront and conversion experience.'],
    'healthcare' => ['Healthcare', 'Digital solutions for multi-branch healthcare operations.'],
    'automation' => ['AI & Business Automation', 'WhatsApp communication and workflow automation.'],
];
$order = array_keys($projects);
$expanded = $order[0];
try {
    $raw = $pdo->query("SELECT `value` FROM settings WHERE `key`='home10_selected_work'")->fetchColumn();
    $saved = is_string($raw) ? json_decode($raw, true) : null;
    if (is_array($saved)) {
        if (isset($saved['order']) && is_array($saved['order'])) {
            $order = array_values(array_unique(array_filter($saved['order'], static fn ($id) => is_string($id) && isset($projects[$id]))));
            $order = array_merge($order, array_values(array_diff(array_keys($projects), $order)));
        }
        if (isset($saved['expanded']) && is_string($saved['expanded']) && isset($projects[$saved['expanded']])) $expanded = $saved['expanded'];
    }
} catch (PDOException $e) {
    // Use the homepage defaults if settings are unavailable.
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $postedOrder = isset($_POST['order']) && is_array($_POST['order']) ? $_POST['order'] : [];
    $order = array_values(array_unique(array_filter($postedOrder, static fn ($id) => is_string($id) && isset($projects[$id]))));
    $order = array_merge($order, array_values(array_diff(array_keys($projects), $order)));
    $postedExpanded = (string) ($_POST['expanded'] ?? '');
    if (isset($projects[$postedExpanded])) $expanded = $postedExpanded;
    try {
        $stmt = $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
        $stmt->execute(['home10_selected_work', json_encode(['order' => $order, 'expanded' => $expanded])]);
        $success = 'Selected Work settings saved.';
    } catch (PDOException $e) {
        $error = 'Could not save Selected Work settings. Please try again.';
    }
}

$pageTitle = 'Selected Work';
$pageSub = 'Choose the card that opens first and reorder the cards on the homepage.';
$activeNav = 'selected-work';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="post" id="selectedWorkForm">
  <?= csrf_field() ?>
  <div class="card">
    <div class="card-title">Homepage card order</div>
    <div class="card-desc">Use the arrows to change the order. Card numbers update automatically to match the public page.</div>
    <div id="selectedWorkList" style="display:grid;gap:10px">
      <?php foreach ($order as $index => $id): ?>
      <div class="selected-work-row" data-project="<?= h($id) ?>" style="display:grid;grid-template-columns:52px minmax(220px,1fr) auto auto;gap:14px;align-items:center;padding:14px;border:1px solid var(--border);border-radius:12px;background:var(--white)">
        <strong data-number style="font-size:18px;color:var(--blue)"><?= sprintf('%02d', $index + 1) ?></strong>
        <input type="hidden" name="order[]" value="<?= h($id) ?>">
        <div><strong><?= h($projects[$id][0]) ?></strong><div class="field-hint"><?= h($projects[$id][1]) ?></div></div>
        <label class="checkbox-row"><input type="radio" name="expanded" value="<?= h($id) ?>" <?= $expanded === $id ? 'checked' : '' ?>> Open first</label>
        <span style="display:flex;gap:6px">
          <button type="button" class="btn btn-ghost btn-sm" data-move="-1" aria-label="Move <?= h($projects[$id][0]) ?> up">↑</button>
          <button type="button" class="btn btn-ghost btn-sm" data-move="1" aria-label="Move <?= h($projects[$id][0]) ?> down">↓</button>
        </span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Save Selected Work</button>
</form>
<script>
(function(){
  var list = document.getElementById('selectedWorkList');
  if (!list) return;
  function sync(){
    var rows = Array.from(list.querySelectorAll('.selected-work-row'));
    rows.forEach(function(row, index){
      row.querySelector('[data-number]').textContent = String(index + 1).padStart(2, '0');
      row.querySelector('[data-move="-1"]').disabled = index === 0;
      row.querySelector('[data-move="1"]').disabled = index === rows.length - 1;
    });
  }
  list.addEventListener('click', function(event){
    var button = event.target.closest('[data-move]');
    if (!button) return;
    var row = button.closest('.selected-work-row');
    var sibling = Number(button.dataset.move) < 0 ? row.previousElementSibling : row.nextElementSibling;
    if (!sibling) return;
    if (Number(button.dataset.move) < 0) list.insertBefore(row, sibling);
    else list.insertBefore(sibling, row);
    sync();
  });
  sync();
})();
</script>
