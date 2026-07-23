<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$tab = ($_GET['tab'] ?? '') === 'leads' ? 'leads' : 'flow';

$error = '';
$success = '';

// ── Create / update a flow step ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_step') {
    csrf_verify();
    $tab = 'flow';

    $stepId = (int) ($_POST['step_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    $stepType = ($_POST['step_type'] ?? '') === 'text' ? 'text' : 'choice';

    $options = [];
    if ($stepType === 'choice') {
        $options = array_values(array_filter(array_map('trim', explode("\n", $_POST['options'] ?? ''))));
    }

    if ($message === '') {
        $error = 'Message text is required.';
    } elseif ($stepType === 'choice' && empty($options)) {
        $error = 'Add at least one option (one per line) for a multiple-choice question.';
    }

    if (!$error) {
        $optionsJson = $options ? json_encode($options) : null;
        if ($stepId) {
            $pdo->prepare('UPDATE whatsapp_flow_steps SET message=?, step_type=?, options=? WHERE id=?')
                ->execute([$message, $stepType, $optionsJson, $stepId]);
            $success = 'Question updated.';
        } else {
            $maxOrder = (int) $pdo->query('SELECT COALESCE(MAX(step_order), 0) FROM whatsapp_flow_steps')->fetchColumn();
            $pdo->prepare('INSERT INTO whatsapp_flow_steps (step_order, message, step_type, options) VALUES (?, ?, ?, ?)')
                ->execute([$maxOrder + 1, $message, $stepType, $optionsJson]);
            $success = 'Question added.';
        }
    }
}

// ── Reorder steps (drag and drop) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reorder_steps') {
    csrf_verify();
    $tab = 'flow';
    $order = array_filter(array_map('intval', explode(',', $_POST['order'] ?? '')));
    $stmt = $pdo->prepare('UPDATE whatsapp_flow_steps SET step_order = ? WHERE id = ?');
    $i = 1;
    foreach ($order as $stepId) {
        $stmt->execute([$i, $stepId]);
        $i++;
    }
    $success = 'Question order saved.';
}

$steps = $pdo->query('SELECT * FROM whatsapp_flow_steps ORDER BY step_order, id')->fetchAll();
foreach ($steps as &$s) {
    $s['options'] = $s['options'] ? json_decode($s['options'], true) : [];
}
unset($s);

$editStep = null;
if ($tab === 'flow' && !empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM whatsapp_flow_steps WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editStep = $stmt->fetch();
    if ($editStep) {
        $editStep['options'] = $editStep['options'] ? json_decode($editStep['options'], true) : [];
    }
}

$leads = [];
if ($tab === 'leads') {
    $leads = $pdo->query('SELECT * FROM whatsapp_leads ORDER BY created_at DESC')->fetchAll();
    foreach ($leads as &$l) {
        $l['answers'] = $l['answers'] ? json_decode($l['answers'], true) : [];
    }
    unset($l);
}

$pageTitle = 'WhatsApp Bot';
$pageSub = 'Build the chat widget flow and view leads it captures.';
$activeNav = 'whatsapp';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="tabs">
  <a href="whatsapp.php?tab=flow" class="tab-link <?= $tab === 'flow' ? 'active' : '' ?>">Flow</a>
  <a href="whatsapp.php?tab=leads" class="tab-link <?= $tab === 'leads' ? 'active' : '' ?>">Leads<?= $leads ? ' (' . count($leads) . ')' : '' ?></a>
</div>

<?php if ($tab === 'flow'): ?>

<div class="access-note" style="margin-bottom:1.25rem">Every visitor answers these questions in order, then is always asked for their phone number as the final step — that part is built in and doesn't need configuring.</div>

<div class="card">
  <div class="card-title"><?= $editStep ? 'Edit Question' : 'Add a Question' ?></div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_step">
    <?php if ($editStep): ?><input type="hidden" name="step_id" value="<?= (int) $editStep['id'] ?>"><?php endif; ?>

    <div class="field">
      <label for="message">Bot Message</label>
      <textarea id="message" name="message" rows="2" required placeholder="e.g. Which industry is your business in?"><?= h($editStep['message'] ?? '') ?></textarea>
    </div>

    <div class="field">
      <label for="step_type">Answer Type</label>
      <select id="step_type" name="step_type">
        <option value="choice" <?= ($editStep['step_type'] ?? 'choice') === 'choice' ? 'selected' : '' ?>>Multiple Choice (tappable options)</option>
        <option value="text" <?= ($editStep['step_type'] ?? '') === 'text' ? 'selected' : '' ?>>Free Text (visitor types an answer)</option>
      </select>
    </div>

    <div class="field">
      <label for="options">Options (one per line — only for Multiple Choice)</label>
      <textarea id="options" name="options" rows="3" placeholder="Custom ERP Solution / Software&#10;Ecommerce Solutions&#10;Marketing Solutions"><?= h(implode("\n", $editStep['options'] ?? [])) ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary"><?= $editStep ? 'Save Question' : 'Add Question' ?></button>
    <?php if ($editStep): ?><a href="whatsapp.php?tab=flow" class="btn btn-ghost">Cancel</a><?php endif; ?>
  </form>
</div>

<div class="card">
  <div class="card-title">Conversation Order</div>
  <div class="card-desc">Drag the ⠿ handle to reorder, then click Save Order.</div>

  <form method="post" id="reorder-form">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="reorder_steps">
    <input type="hidden" name="order" id="reorder-input">

    <div id="step-list">
      <?php foreach ($steps as $s): ?>
      <div class="field-row" draggable="true" data-id="<?= (int) $s['id'] ?>">
        <span class="drag-handle">⠿</span>
        <div class="field-row-main">
          <div class="t-name"><?= h($s['message']) ?></div>
          <div class="t-sub"><?= $s['step_type'] === 'choice' ? count($s['options']) . ' options' : 'Free text answer' ?></div>
        </div>
        <a class="row-link" href="whatsapp.php?tab=flow&edit=<?= (int) $s['id'] ?>">Edit</a>
        <form method="post" action="whatsapp-step-delete.php" style="display:inline" onsubmit="return confirm('Delete this question?');">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
          <button type="submit" class="row-link" style="background:none;border:none;color:#e11d48;cursor:pointer;font:inherit;padding:0;margin-left:10px">Delete</button>
        </form>
      </div>
      <?php endforeach; ?>
      <?php if (empty($steps)): ?>
        <div class="empty-state"><p>No questions yet — add one above.</p></div>
      <?php endif; ?>
    </div>

    <?php if (!empty($steps)): ?>
    <button type="submit" class="btn btn-ghost btn-sm" style="margin-top:1rem">Save Order</button>
    <?php endif; ?>
  </form>
</div>

<script>
(function () {
  var container = document.getElementById('step-list');
  if (!container) return;
  var dragged = null;

  container.querySelectorAll('.field-row').forEach(function (row) {
    row.addEventListener('dragstart', function () { dragged = row; row.classList.add('dragging'); });
    row.addEventListener('dragend', function () { row.classList.remove('dragging'); });
    row.addEventListener('dragover', function (e) {
      e.preventDefault();
      var after = getRowAfter(container, e.clientY);
      if (!dragged) return;
      if (after == null) container.appendChild(dragged);
      else container.insertBefore(dragged, after);
    });
  });

  function getRowAfter(container, y) {
    var rows = Array.prototype.slice.call(container.querySelectorAll('.field-row:not(.dragging)'));
    return rows.reduce(function (closest, row) {
      var box = row.getBoundingClientRect();
      var offset = y - box.top - box.height / 2;
      if (offset < 0 && offset > closest.offset) return { offset: offset, element: row };
      return closest;
    }, { offset: -Infinity, element: null }).element;
  }

  var reorderForm = document.getElementById('reorder-form');
  if (reorderForm) {
    reorderForm.addEventListener('submit', function () {
      var ids = Array.prototype.map.call(container.querySelectorAll('.field-row'), function (r) { return r.dataset.id; });
      document.getElementById('reorder-input').value = ids.join(',');
    });
  }
})();
</script>

<?php else: ?>

<div class="card">
  <?php if (empty($leads)): ?>
    <div class="empty-state">
      <p>No leads yet. They'll show up here once visitors complete the WhatsApp chat widget.</p>
    </div>
  <?php else: ?>
  <div style="overflow-x:auto">
  <table>
    <thead>
      <tr>
        <th>Phone</th>
        <?php foreach ($steps as $s): ?><th><?= h(mb_strimwidth($s['message'], 0, 28, '…')) ?></th><?php endforeach; ?>
        <th>Submitted</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($leads as $lead):
        $byQuestion = [];
        foreach ($lead['answers'] as $a) {
            $byQuestion[$a['question'] ?? ''] = $a['answer'] ?? '';
        }
      ?>
      <tr>
        <td class="t-name"><a href="https://wa.me/<?= h(preg_replace('/[^0-9]/', '', $lead['phone'])) ?>" target="_blank" class="row-link"><?= h($lead['phone']) ?></a></td>
        <?php foreach ($steps as $s): ?>
          <td><?= h($byQuestion[$s['message']] ?? '—') ?></td>
        <?php endforeach; ?>
        <td><?= h(date('M j, Y g:i A', strtotime($lead['created_at']))) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</div>

<?php endif; ?>

<style>
.tabs{display:flex;gap:4px;margin-bottom:1.25rem;border-bottom:1.5px solid var(--border)}
.tab-link{padding:10px 16px;font-size:13px;font-weight:700;color:var(--g500);text-decoration:none;border-bottom:2px solid transparent;margin-bottom:-1.5px}
.tab-link.active{color:var(--black);border-bottom-color:var(--blue)}
.field-row{display:flex;align-items:center;gap:12px;padding:12px 10px;border-bottom:1px solid var(--border);cursor:grab}
.field-row:last-child{border-bottom:none}
.field-row.dragging{opacity:.4}
.field-row-main{flex:1}
.drag-handle{color:var(--g400);font-size:16px;flex-shrink:0}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
