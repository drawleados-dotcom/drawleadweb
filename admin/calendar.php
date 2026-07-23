<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$tab = in_array($_GET['tab'] ?? '', ['availability', 'form'], true) ? $_GET['tab'] : 'bookings';

$error = '';
$success = '';

// ── Save availability settings ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_availability') {
    csrf_verify();
    $tab = 'availability';

    $days = array_values(array_intersect(array_map('intval', $_POST['days'] ?? []), range(0, 6)));
    $startTime = $_POST['start_time'] ?? '';
    $endTime = $_POST['end_time'] ?? '';
    $interval = (int) ($_POST['slot_interval_minutes'] ?? 30);
    $rangeStart = $_POST['range_start'] ?? '';
    $rangeEnd = $_POST['range_end'] ?? '';

    if (empty($days)) {
        $error = 'Pick at least one day of the week.';
    } elseif (!in_array($interval, [15, 30, 45, 60], true)) {
        $error = 'Pick a valid slot interval.';
    } elseif (strtotime($startTime) === false || strtotime($endTime) === false || strtotime($startTime) >= strtotime($endTime)) {
        $error = 'Start time must be before end time.';
    } elseif (strtotime($rangeStart) === false || strtotime($rangeEnd) === false || strtotime($rangeStart) > strtotime($rangeEnd)) {
        $error = 'Pick a valid booking date range (start on or before end).';
    }

    if (!$error) {
        $pdo->prepare(
            'UPDATE booking_availability SET days_of_week=?, start_time=?, end_time=?, slot_interval_minutes=?, range_start=?, range_end=? WHERE id=1'
        )->execute([
            implode(',', $days), date('H:i:s', strtotime($startTime)), date('H:i:s', strtotime($endTime)),
            $interval, $rangeStart, $rangeEnd,
        ]);

        $emails = array_map('trim', $_POST['notification_emails'] ?? []);
        $emails = array_values(array_unique(array_filter($emails, fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))));
        $pdo->exec('DELETE FROM booking_notification_emails');
        $ins = $pdo->prepare('INSERT INTO booking_notification_emails (email) VALUES (?)');
        foreach ($emails as $e) {
            $ins->execute([$e]);
        }

        $success = 'Availability settings saved.';
    }
}

// ── Create / update a form field ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_field') {
    csrf_verify();
    $tab = 'form';

    $fieldId = (int) ($_POST['field_id'] ?? 0);
    $label = trim($_POST['label'] ?? '');
    $allowedTypes = ['text', 'email', 'phone', 'textarea', 'select', 'radio', 'checkbox', 'date'];
    $type = in_array($_POST['field_type'] ?? '', $allowedTypes, true) ? $_POST['field_type'] : 'text';
    $role = in_array($_POST['field_role'] ?? '', ['none', 'name', 'email'], true) ? $_POST['field_role'] : 'none';
    $placeholder = trim($_POST['placeholder'] ?? '');
    $isRequired = isset($_POST['is_required']) ? 1 : 0;

    $options = [];
    if (in_array($type, ['select', 'radio', 'checkbox'], true)) {
        $options = array_values(array_filter(array_map('trim', explode("\n", $_POST['options'] ?? ''))));
    }

    $condFieldId = (int) ($_POST['conditional_field_id'] ?? 0);
    $condFieldId = $condFieldId ?: null;
    $condValue = $condFieldId ? trim($_POST['conditional_value'] ?? '') : null;

    if ($label === '') {
        $error = 'Field label is required.';
    } elseif (in_array($type, ['select', 'radio', 'checkbox'], true) && empty($options)) {
        $error = 'Add at least one option (one per line) for this field type.';
    } elseif ($condFieldId && $condValue === '') {
        $error = 'Enter the value that triggers this conditional field to show.';
    }

    if (!$error) {
        $optionsJson = $options ? json_encode($options) : null;

        if ($fieldId) {
            $pdo->prepare(
                'UPDATE booking_form_fields SET label=?, field_type=?, field_role=?, options=?, placeholder=?, is_required=?, conditional_field_id=?, conditional_value=? WHERE id=?'
            )->execute([$label, $type, $role, $optionsJson, $placeholder, $isRequired, $condFieldId, $condValue, $fieldId]);
            $success = 'Field updated.';
        } else {
            $fieldKey = slugify($label) ?: 'field';
            $base = $fieldKey;
            $n = 1;
            $chk = $pdo->prepare('SELECT 1 FROM booking_form_fields WHERE field_key = ?');
            while (true) {
                $chk->execute([$fieldKey]);
                if (!$chk->fetchColumn()) {
                    break;
                }
                $n++;
                $fieldKey = $base . '-' . $n;
            }
            $maxOrder = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), 0) FROM booking_form_fields')->fetchColumn();
            $pdo->prepare(
                'INSERT INTO booking_form_fields (field_key, label, field_type, field_role, options, placeholder, is_required, sort_order, conditional_field_id, conditional_value)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            )->execute([$fieldKey, $label, $type, $role, $optionsJson, $placeholder, $isRequired, $maxOrder + 1, $condFieldId, $condValue]);
            $success = 'Field added.';
        }
    }
}

// ── Reorder fields (drag and drop) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reorder_fields') {
    csrf_verify();
    $tab = 'form';
    $order = array_filter(array_map('intval', explode(',', $_POST['order'] ?? '')));
    $stmt = $pdo->prepare('UPDATE booking_form_fields SET sort_order = ? WHERE id = ?');
    $i = 1;
    foreach ($order as $fieldId) {
        $stmt->execute([$i, $fieldId]);
        $i++;
    }
    $success = 'Field order saved.';
}

// ── Cancel a booking (frees the slot back up) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancel_booking') {
    csrf_verify();
    $tab = 'bookings';
    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?")->execute([$bookingId]);
    $success = 'Booking cancelled — that time slot is available again.';
}

$availability = get_booking_availability($pdo);
$notificationEmails = get_booking_notification_emails($pdo);
$fields = get_booking_form_fields($pdo);
$activeDays = array_map('intval', array_filter(explode(',', $availability['days_of_week'])));

$editField = null;
if ($tab === 'form' && !empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM booking_form_fields WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editField = $stmt->fetch();
    if ($editField) {
        $editField['options'] = $editField['options'] ? json_decode($editField['options'], true) : [];
    }
}

$dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

$bookings = [];
$fieldLabelByKey = [];
if ($tab === 'bookings') {
    $bookings = $pdo->query('SELECT * FROM bookings ORDER BY booking_date DESC, booking_time DESC')->fetchAll();
    foreach ($fields as $f) {
        $fieldLabelByKey[$f['field_key']] = $f['label'];
    }
}

$pageTitle = 'Calendar & Booking';
$pageSub = 'Configure consultation availability and the booking form.';
$activeNav = 'calendar';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="tabs">
  <a href="calendar.php?tab=bookings" class="tab-link <?= $tab === 'bookings' ? 'active' : '' ?>">Bookings</a>
  <a href="calendar.php?tab=availability" class="tab-link <?= $tab === 'availability' ? 'active' : '' ?>">Availability</a>
  <a href="calendar.php?tab=form" class="tab-link <?= $tab === 'form' ? 'active' : '' ?>">Booking Form</a>
</div>

<?php if ($tab === 'bookings'): ?>

<div class="card">
  <div class="card-title">All Bookings</div>
  <div class="card-desc"><?= count($bookings) ?> total. Newest first.</div>
  <?php if (empty($bookings)): ?>
    <div class="empty-state">
      <p>No consultations booked yet.</p>
    </div>
  <?php else: ?>
  <table>
    <thead><tr><th>Date &amp; Time</th><th>Name</th><th>Email</th><th>Details</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($bookings as $b):
        $data = json_decode($b['form_data'], true) ?: [];
      ?>
      <tr>
        <td class="t-name"><?= h(date('M j, Y', strtotime($b['booking_date']))) ?><div class="t-sub"><?= h(date('g:i A', strtotime($b['booking_time']))) ?></div></td>
        <td><?= h($b['name'] ?: '—') ?></td>
        <td><?= h($b['email'] ?: '—') ?></td>
        <td>
          <?php foreach ($data as $key => $val):
            if ($val === '' || $val === [] || in_array($key, ['name', 'email'], true)) continue;
            $val = is_array($val) ? implode(', ', $val) : $val;
          ?>
            <div class="t-sub"><strong><?= h($fieldLabelByKey[$key] ?? $key) ?>:</strong> <?= h($val) ?></div>
          <?php endforeach; ?>
        </td>
        <td><span class="badge <?= $b['status'] === 'confirmed' ? 'badge-published' : 'badge-draft' ?>"><?= h($b['status']) ?></span></td>
        <td>
          <?php if ($b['status'] === 'confirmed'): ?>
          <form method="post" onsubmit="return confirm('Cancel this booking? The time slot will open back up.');">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="cancel_booking">
            <input type="hidden" name="booking_id" value="<?= (int) $b['id'] ?>">
            <button type="submit" class="row-link" style="background:none;border:none;color:#e11d48;cursor:pointer;font:inherit;padding:0">Cancel</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php elseif ($tab === 'availability'): ?>

<form method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_availability">

  <div class="card">
    <div class="card-title">Days Available</div>
    <div class="checkbox-grid" style="grid-template-columns:repeat(4,1fr)">
      <?php foreach ($dayLabels as $i => $label): ?>
      <label class="checkbox-row">
        <input type="checkbox" name="days[]" value="<?= $i ?>" <?= in_array($i, $activeDays, true) ? 'checked' : '' ?>>
        <?= h($label) ?>
      </label>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Daily Hours &amp; Slot Length</div>
    <div class="checkbox-grid">
      <div class="field">
        <label for="start_time">Start Time</label>
        <input type="time" id="start_time" name="start_time" value="<?= h(substr($availability['start_time'], 0, 5)) ?>">
      </div>
      <div class="field">
        <label for="end_time">End Time</label>
        <input type="time" id="end_time" name="end_time" value="<?= h(substr($availability['end_time'], 0, 5)) ?>">
      </div>
    </div>
    <div class="field">
      <label for="slot_interval_minutes">Time Between Meetings</label>
      <select id="slot_interval_minutes" name="slot_interval_minutes">
        <?php foreach ([15, 30, 45, 60] as $m): ?>
        <option value="<?= $m ?>" <?= (int) $availability['slot_interval_minutes'] === $m ? 'selected' : '' ?>><?= $m ?> minutes</option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Booking Window</div>
    <div class="card-desc">The date range visitors are allowed to book within. Update this periodically as time passes.</div>
    <div class="checkbox-grid">
      <div class="field">
        <label for="range_start">From</label>
        <input type="date" id="range_start" name="range_start" value="<?= h($availability['range_start']) ?>">
      </div>
      <div class="field">
        <label for="range_end">Until</label>
        <input type="date" id="range_end" name="range_end" value="<?= h($availability['range_end']) ?>">
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Notification Emails</div>
    <div class="card-desc">Everyone on this list gets an email the moment someone books a consultation. Add 2–3.</div>
    <div id="email-rows">
      <?php
      $emailList = $notificationEmails ?: [['email' => '']];
      foreach ($emailList as $e): ?>
      <div class="field" style="display:flex;gap:8px;align-items:center">
        <input type="email" name="notification_emails[]" value="<?= h($e['email']) ?>" placeholder="name@drawlead.com" style="flex:1;font-family:var(--font);font-size:14px;padding:11px 13px;border:1.5px solid var(--border);border-radius:8px">
        <button type="button" class="btn btn-ghost btn-sm" onclick="this.parentElement.remove()">Remove</button>
      </div>
      <?php endforeach; ?>
    </div>
    <button type="button" class="btn btn-ghost btn-sm" id="add-email-row">+ Add Email</button>
  </div>

  <div class="card">
    <button type="submit" class="btn btn-primary">Save Availability</button>
  </div>
</form>

<?php else: ?>

<div class="card">
  <div class="card-title"><?= $editField ? 'Edit Field' : 'Add a Field' ?></div>
  <div class="card-desc">Build the form visitors fill in to book a consultation — like Name, Email, Phone, or anything custom.</div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_field">
    <?php if ($editField): ?><input type="hidden" name="field_id" value="<?= (int) $editField['id'] ?>"><?php endif; ?>

    <div class="checkbox-grid">
      <div class="field">
        <label for="label">Field Label</label>
        <input type="text" id="label" name="label" required value="<?= h($editField['label'] ?? '') ?>" placeholder="e.g. Company Size">
      </div>
      <div class="field">
        <label for="field_type">Field Type</label>
        <select id="field_type" name="field_type">
          <?php foreach (['text' => 'Short Text', 'textarea' => 'Long Text', 'email' => 'Email', 'phone' => 'Phone', 'date' => 'Date', 'select' => 'Dropdown', 'radio' => 'Radio Buttons', 'checkbox' => 'Checkboxes'] as $val => $label): ?>
          <option value="<?= $val ?>" <?= ($editField['field_type'] ?? 'text') === $val ? 'selected' : '' ?>><?= h($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="field">
      <label for="options">Options (one per line — only for Dropdown / Radio / Checkboxes)</label>
      <textarea id="options" name="options" rows="3" placeholder="Option One&#10;Option Two&#10;Option Three"><?= h(implode("\n", $editField['options'] ?? [])) ?></textarea>
    </div>

    <div class="checkbox-grid">
      <div class="field">
        <label for="placeholder">Placeholder Text</label>
        <input type="text" id="placeholder" name="placeholder" value="<?= h($editField['placeholder'] ?? '') ?>">
      </div>
      <div class="field">
        <label for="field_role">Special Role</label>
        <select id="field_role" name="field_role">
          <option value="none" <?= ($editField['field_role'] ?? 'none') === 'none' ? 'selected' : '' ?>>None</option>
          <option value="name" <?= ($editField['field_role'] ?? '') === 'name' ? 'selected' : '' ?>>This is the Name field</option>
          <option value="email" <?= ($editField['field_role'] ?? '') === 'email' ? 'selected' : '' ?>>This is the Email field</option>
        </select>
      </div>
    </div>

    <label class="checkbox-row" style="max-width:220px;margin-bottom:1.1rem">
      <input type="checkbox" name="is_required" <?= ($editField['is_required'] ?? 1) ? 'checked' : '' ?>>
      Required field
    </label>

    <div class="card-title" style="font-size:12.5px">Show Only If (optional)</div>
    <div class="checkbox-grid">
      <div class="field">
        <label for="conditional_field_id">Another field...</label>
        <select id="conditional_field_id" name="conditional_field_id">
          <option value="">— Always show —</option>
          <?php foreach ($fields as $f): if ($editField && (int) $f['id'] === (int) $editField['id']) continue; ?>
          <option value="<?= (int) $f['id'] ?>" <?= (int) ($editField['conditional_field_id'] ?? 0) === (int) $f['id'] ? 'selected' : '' ?>><?= h($f['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="conditional_value">...equals this value</label>
        <input type="text" id="conditional_value" name="conditional_value" value="<?= h($editField['conditional_value'] ?? '') ?>" placeholder="e.g. Yes">
      </div>
    </div>

    <button type="submit" class="btn btn-primary"><?= $editField ? 'Save Field' : 'Add Field' ?></button>
    <?php if ($editField): ?><a href="calendar.php?tab=form" class="btn btn-ghost">Cancel</a><?php endif; ?>
  </form>
</div>

<div class="card">
  <div class="card-title">Form Fields</div>
  <div class="card-desc">Drag the ⠿ handle to reorder, then click Save Order.</div>

  <form method="post" id="reorder-form">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="reorder_fields">
    <input type="hidden" name="order" id="reorder-input">

    <div id="field-list">
      <?php foreach ($fields as $f): ?>
      <div class="field-row" draggable="true" data-id="<?= (int) $f['id'] ?>">
        <span class="drag-handle">⠿</span>
        <div class="field-row-main">
          <div class="t-name"><?= h($f['label']) ?> <?php if (!$f['is_required']): ?><span class="t-sub">(optional)</span><?php endif; ?></div>
          <div class="t-sub">
            <?= h(ucfirst($f['field_type'])) ?>
            <?php if ($f['field_role'] !== 'none'): ?> · role: <?= h($f['field_role']) ?><?php endif; ?>
            <?php if ($f['conditional_field_id']): ?> · conditional<?php endif; ?>
          </div>
        </div>
        <a class="row-link" href="calendar.php?tab=form&edit=<?= (int) $f['id'] ?>">Edit</a>
        <form method="post" action="calendar-field-delete.php" style="display:inline" onsubmit="return confirm('Delete this field?');">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
          <button type="submit" class="row-link" style="background:none;border:none;color:#e11d48;cursor:pointer;font:inherit;padding:0;margin-left:10px">Delete</button>
        </form>
      </div>
      <?php endforeach; ?>
    </div>

    <button type="submit" class="btn btn-ghost btn-sm" style="margin-top:1rem">Save Order</button>
  </form>
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

<script>
(function () {
  var container = document.getElementById('field-list');
  if (!container) return;
  var dragged = null;

  container.querySelectorAll('.field-row').forEach(function (row) {
    row.addEventListener('dragstart', function () {
      dragged = row;
      row.classList.add('dragging');
    });
    row.addEventListener('dragend', function () {
      row.classList.remove('dragging');
    });
    row.addEventListener('dragover', function (e) {
      e.preventDefault();
      var after = getRowAfter(container, e.clientY);
      if (!dragged) return;
      if (after == null) {
        container.appendChild(dragged);
      } else {
        container.insertBefore(dragged, after);
      }
    });
  });

  function getRowAfter(container, y) {
    var rows = Array.prototype.slice.call(container.querySelectorAll('.field-row:not(.dragging)'));
    return rows.reduce(function (closest, row) {
      var box = row.getBoundingClientRect();
      var offset = y - box.top - box.height / 2;
      if (offset < 0 && offset > closest.offset) {
        return { offset: offset, element: row };
      }
      return closest;
    }, { offset: -Infinity, element: null }).element;
  }

  document.getElementById('reorder-form').addEventListener('submit', function () {
    var ids = Array.prototype.map.call(container.querySelectorAll('.field-row'), function (r) { return r.dataset.id; });
    document.getElementById('reorder-input').value = ids.join(',');
  });

  var addEmailBtn = document.getElementById('add-email-row');
  if (addEmailBtn) {
    addEmailBtn.addEventListener('click', function () {
      var wrap = document.getElementById('email-rows');
      var row = document.createElement('div');
      row.className = 'field';
      row.style.cssText = 'display:flex;gap:8px;align-items:center';
      row.innerHTML = '<input type="email" name="notification_emails[]" placeholder="name@drawlead.com" style="flex:1;font-family:var(--font);font-size:14px;padding:11px 13px;border:1.5px solid var(--border);border-radius:8px">' +
        '<button type="button" class="btn btn-ghost btn-sm" onclick="this.parentElement.remove()">Remove</button>';
      wrap.appendChild(row);
    });
  }
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
