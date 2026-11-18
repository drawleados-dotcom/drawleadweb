<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/media-lib.php';
require_admin();

if (!popups_ready($pdo)) {
    header('Location: popup.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$popup = $id ? get_popup($pdo, $id) : popup_defaults();
if ($id && !$popup) {
    header('Location: popup.php');
    exit;
}

$layouts = popup_layouts();
$targetOptions = popup_target_options($pdo);

$error = '';
$success = isset($_GET['saved']) ? 'Saved.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $savedImage = $popup['image'];
    $layout = (string) ($_POST['layout'] ?? 'split');
    $showOn = ($_POST['show_on'] ?? '') === 'all' ? 'all' : 'selected';
    $targets = array_values(array_intersect((array) ($_POST['targets'] ?? []), array_keys($targetOptions)));

    $popup = array_merge($popup, [
        'name' => trim($_POST['name'] ?? ''),
        'layout' => isset($layouts[$layout]) ? $layout : 'split',
        'enabled' => isset($_POST['enabled']) ? 1 : 0,
        'show_on' => $showOn,
        'targets' => implode(',', $targets),
        'image_alt' => trim($_POST['image_alt'] ?? ''),
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'points' => trim($_POST['points'] ?? ''),
        'cta_text' => trim($_POST['cta_text'] ?? '') ?: 'Book a Free Consultation',
        'cta_use_booking' => isset($_POST['cta_use_booking']) ? 1 : 0,
        'cta_link' => trim($_POST['cta_link'] ?? ''),
        'trigger_delay' => isset($_POST['trigger_delay']) ? 1 : 0,
        'trigger_new_page' => isset($_POST['trigger_new_page']) ? 1 : 0,
        'trigger_refresh' => isset($_POST['trigger_refresh']) ? 1 : 0,
        'trigger_scroll_section' => isset($_POST['trigger_scroll_section']) ? 1 : 0,
    ]);

    if ($popup['enabled'] && $popup['title'] === '') {
        $error = 'Add a title before turning the popup on.';
    } elseif ($popup['enabled'] && $showOn === 'selected' && !$targets) {
        $error = 'Pick at least one page, or choose "All pages", before turning the popup on.';
    }

    // The image comes from the Media picker: empty (no image), the value
    // already saved (which may be a built-in data: graphic), or a path to
    // an image that exists in the library.
    $imageIn = trim((string) ($_POST['image'] ?? ''));
    if ($imageIn === '' || $imageIn === (string) $savedImage) {
        $popup['image'] = $imageIn;
    } elseif (media_is_library_path($imageIn)) {
        $popup['image'] = $imageIn;
    } elseif (!$error) {
        $error = 'That image is no longer in the Media library. Choose another one.';
    }

    if (!$error) {
        $cols = ['name', 'layout', 'enabled', 'show_on', 'targets', 'image', 'image_alt', 'title', 'description', 'points',
            'cta_text', 'cta_use_booking', 'cta_link', 'trigger_delay', 'trigger_new_page', 'trigger_refresh', 'trigger_scroll_section'];
        $values = array_map(fn ($c) => $popup[$c], $cols);

        if ($id) {
            $set = implode(', ', array_map(fn ($c) => "$c = ?", $cols));
            $pdo->prepare("UPDATE popups SET $set WHERE id = ?")->execute([...$values, $id]);
        } else {
            $pdo->prepare('INSERT INTO popups (' . implode(', ', $cols) . ') VALUES (' . implode(', ', array_fill(0, count($cols), '?')) . ')')
                ->execute($values);
            $id = (int) $pdo->lastInsertId();
        }
        header('Location: popup-edit.php?id=' . $id . '&saved=1');
        exit;
    }
}

// Other popups that are on and already cover a page — shown beside each
// page so it's clear which popup a visitor will actually get there.
$otherByTarget = [];
$otherForAll = [];
foreach (get_popups($pdo) as $other) {
    if ((int) $other['id'] === $id || empty($other['enabled'])) {
        continue;
    }
    $label = $other['name'] ?: ($other['title'] ?: 'Untitled popup');
    if ($other['show_on'] === 'all') {
        $otherForAll[] = $label;
        continue;
    }
    foreach (popup_target_keys($other['targets']) as $k) {
        $otherByTarget[$k][] = $label;
    }
}
$selectedTargets = popup_target_keys($popup['targets']);

$pageTitle = $id ? 'Edit Popup' : 'New Popup';
$pageSub = 'Pick a layout, write the content, and choose which pages show it.';
$activeNav = 'popup';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="toolbar">
  <a href="popup.php" class="row-link">← All popups</a>
</div>

<form method="post">
  <?= csrf_field() ?>

  <div class="card">
    <div class="field">
      <label for="name">Popup Name</label>
      <input type="text" id="name" name="name" maxlength="120" value="<?= h($popup['name']) ?>" placeholder="e.g. ERP pages offer">
      <div class="field-hint">Only shown here in the admin, to tell your popups apart.</div>
    </div>
    <label class="checkbox-row" style="max-width:320px">
      <input type="checkbox" name="enabled" <?= $popup['enabled'] ? 'checked' : '' ?>>
      Show this popup on the live site
    </label>
    <div class="field-hint">Turn this off to hide it without losing your content below.</div>
  </div>

  <div class="card">
    <div class="card-title">Layout</div>
    <div class="card-desc">How the popup looks on the page.</div>
    <div class="popup-layouts">
      <?php foreach ($layouts as $key => [$label, $desc]): ?>
      <label class="popup-layout">
        <input type="radio" name="layout" value="<?= h($key) ?>" <?= $popup['layout'] === $key ? 'checked' : '' ?>>
        <span class="popup-layout-card">
          <span class="popup-layout-art popup-layout-art--<?= h($key) ?>" aria-hidden="true">
            <span class="pla-box"><span class="pla-img"></span><span class="pla-body"><i></i><i></i><i></i><b></b></span></span>
          </span>
          <span class="popup-layout-name"><?= h($label) ?></span>
          <span class="popup-layout-desc"><?= h($desc) ?></span>
        </span>
      </label>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Pages</div>
    <div class="card-desc">Where this popup appears. Each page shows at most one popup: one picked for that page beats an "All pages" popup, and if two popups pick the same page the newer one is shown.</div>
    <div class="checkbox-grid" style="margin-bottom:1rem">
      <label class="checkbox-row">
        <input type="radio" name="show_on" value="all" <?= $popup['show_on'] === 'all' ? 'checked' : '' ?> data-show-on>
        All pages
      </label>
      <label class="checkbox-row">
        <input type="radio" name="show_on" value="selected" <?= $popup['show_on'] !== 'all' ? 'checked' : '' ?> data-show-on>
        Only the pages I pick
      </label>
    </div>

    <div data-target-picker<?= $popup['show_on'] === 'all' ? ' hidden' : '' ?>>
      <?php if ($otherForAll): ?>
      <div class="field-hint" style="margin-bottom:.8rem">Pages you leave unticked show "<?= h(implode('", "', $otherForAll)) ?>" (an All pages popup) instead.</div>
      <?php endif; ?>
      <div class="popup-target-tools">
        <input type="search" placeholder="Filter pages…" data-target-filter>
        <button type="button" class="btn btn-ghost" data-target-all>Select all</button>
        <button type="button" class="btn btn-ghost" data-target-none>Clear</button>
        <span class="t-sub" data-target-count></span>
      </div>
      <div class="popup-targets">
        <?php foreach ($targetOptions as $key => $opt): ?>
        <label class="popup-target" data-target-row data-search="<?= h(strtolower($opt['label'] . ' ' . $opt['sub'])) ?>">
          <input type="checkbox" name="targets[]" value="<?= h($key) ?>" <?= in_array($key, $selectedTargets, true) ? 'checked' : '' ?>>
          <span>
            <span class="popup-target-name"><?= h($opt['label']) ?></span>
            <span class="t-sub"><?= h($opt['sub']) ?></span>
            <?php if (!empty($otherByTarget[$key])): ?>
            <span class="popup-target-clash">Also picked by: <?= h(implode(', ', $otherByTarget[$key])) ?></span>
            <?php endif; ?>
          </span>
        </label>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Image</div>
    <div class="card-desc">Split: fills the left half. Centered and Corner: runs across the top.</div>
    <div class="field">
      <label>Image</label>
      <div class="mp-field<?= $popup['image'] ? ' has-image' : '' ?>" data-media-picker data-csrf="<?= h(csrf_token()) ?>">
        <input type="hidden" name="image" value="<?= h((string) $popup['image']) ?>" data-mp-value>
        <button type="button" class="mp-preview" data-mp-open aria-label="Choose image">
          <img data-mp-img alt=""<?= $popup['image'] ? ' src="' . h(popup_image_src((string) $popup['image'])) . '"' : '' ?>>
          <span class="mp-placeholder">No image selected</span>
        </button>
        <div class="mp-field-actions">
          <button type="button" class="btn btn-ghost" data-mp-open>Choose from Media</button>
          <button type="button" class="link-btn link-danger mp-remove" data-mp-clear>Remove image</button>
          <div class="field-hint">Pick an image from the Media library, or upload a new one there.</div>
        </div>
      </div>
    </div>
    <div class="field">
      <label for="image_alt">Image Alt Text</label>
      <input type="text" id="image_alt" name="image_alt" maxlength="190" value="<?= h($popup['image_alt']) ?>">
    </div>
  </div>

  <div class="card">
    <div class="card-title">Content</div>
    <div class="field">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" maxlength="190" value="<?= h($popup['title']) ?>" placeholder="e.g. Let's Build Your Growth Engine">
    </div>
    <div class="field">
      <label for="description">Description</label>
      <textarea id="description" name="description" rows="2" maxlength="400"><?= h($popup['description']) ?></textarea>
    </div>
    <div class="field">
      <label for="points">Points (one per line — first 4 shown with a checkmark; Corner shows 3)</label>
      <textarea id="points" name="points" rows="4" placeholder="Custom ERP built around your process&#10;Live inventory &amp; order sync&#10;SEO and performance marketing that compounds&#10;Dedicated support, not a ticket queue"><?= h($popup['points']) ?></textarea>
    </div>
  </div>

  <div class="card">
    <div class="card-title">When Should It Appear?</div>
    <div class="card-desc">Check the trigger(s) that should show the popup. A trigger left unchecked never fires — leave at least one checked or the popup will never appear even when on.</div>
    <div class="checkbox-grid">
      <label class="checkbox-row">
        <input type="checkbox" name="trigger_delay" <?= $popup['trigger_delay'] ? 'checked' : '' ?>>
        First 3 Seconds
      </label>
      <label class="checkbox-row">
        <input type="checkbox" name="trigger_scroll_section" <?= $popup['trigger_scroll_section'] ? 'checked' : '' ?>>
        4th Section of the Page
      </label>
      <label class="checkbox-row">
        <input type="checkbox" name="trigger_new_page" <?= $popup['trigger_new_page'] ? 'checked' : '' ?>>
        Every New Page
      </label>
      <label class="checkbox-row">
        <input type="checkbox" name="trigger_refresh" <?= $popup['trigger_refresh'] ? 'checked' : '' ?>>
        Every Refresh
      </label>
    </div>
    <div class="field-hint" style="margin-top:.9rem">
      "First 3 Seconds" and "4th Section" control <b>when</b> on the page it appears. "Every New Page" and "Every Refresh" control <b>how often</b> — leave both off to show it just once per visitor session (the default).
    </div>
  </div>

  <div class="card">
    <div class="card-title">Call to Action</div>
    <div class="field">
      <label for="cta_text">Button Text</label>
      <input type="text" id="cta_text" name="cta_text" maxlength="100" value="<?= h($popup['cta_text']) ?>">
    </div>
    <label class="checkbox-row" style="max-width:340px;margin-bottom:1.1rem">
      <input type="checkbox" name="cta_use_booking" <?= $popup['cta_use_booking'] ? 'checked' : '' ?>>
      Open the booking popup (recommended)
    </label>
    <div class="field">
      <label for="cta_link">Or a Custom Link</label>
      <input type="text" id="cta_link" name="cta_link" maxlength="255" value="<?= h($popup['cta_link']) ?>" placeholder="https://... or /case-studies">
      <div class="field-hint">Only used when "Open the booking popup" above is unchecked.</div>
    </div>
  </div>

  <div class="card">
    <button type="submit" class="btn btn-primary">Save Popup</button>
    <a href="popup.php" class="btn btn-ghost">Cancel</a>
  </div>
</form>

<script>
(function () {
  var picker = document.querySelector('[data-target-picker]');
  var rows = Array.prototype.slice.call(document.querySelectorAll('[data-target-row]'));
  var count = document.querySelector('[data-target-count]');

  function updateCount() {
    var n = rows.filter(function (r) { return r.querySelector('input').checked; }).length;
    count.textContent = n + ' selected';
  }
  function visibleRows() {
    return rows.filter(function (r) { return !r.hidden; });
  }

  document.querySelectorAll('[data-show-on]').forEach(function (radio) {
    radio.addEventListener('change', function () {
      picker.hidden = document.querySelector('[data-show-on]:checked').value === 'all';
    });
  });
  document.querySelector('[data-target-filter]').addEventListener('input', function () {
    var q = this.value.trim().toLowerCase();
    rows.forEach(function (r) { r.hidden = q !== '' && r.dataset.search.indexOf(q) === -1; });
  });
  document.querySelector('[data-target-all]').addEventListener('click', function () {
    visibleRows().forEach(function (r) { r.querySelector('input').checked = true; });
    updateCount();
  });
  document.querySelector('[data-target-none]').addEventListener('click', function () {
    visibleRows().forEach(function (r) { r.querySelector('input').checked = false; });
    updateCount();
  });
  rows.forEach(function (r) { r.querySelector('input').addEventListener('change', updateCount); });
  updateCount();
})();
</script>
<script src="<?= asset_url('/admin/assets/media-picker.js') ?>"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
