<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$popup = get_site_popup($pdo);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $enabled = isset($_POST['enabled']) ? 1 : 0;
    $imageAlt = trim($_POST['image_alt'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $points = trim($_POST['points'] ?? '');
    $ctaText = trim($_POST['cta_text'] ?? '') ?: 'Book a Free Consultation';
    $ctaUseBooking = isset($_POST['cta_use_booking']) ? 1 : 0;
    $ctaLink = trim($_POST['cta_link'] ?? '');
    $triggerDelay = isset($_POST['trigger_delay']) ? 1 : 0;
    $triggerNewPage = isset($_POST['trigger_new_page']) ? 1 : 0;
    $triggerRefresh = isset($_POST['trigger_refresh']) ? 1 : 0;
    $triggerScrollSection = isset($_POST['trigger_scroll_section']) ? 1 : 0;

    if ($enabled && $title === '') {
        $error = 'Add a title before enabling the popup.';
    }

    $image = $popup['image'];
    if (!$error && !empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $tmp = $_FILES['image']['tmp_name'];
        $mime = @mime_content_type($tmp);
        $size = (int) $_FILES['image']['size'];

        if (!isset($allowed[$mime])) {
            $error = 'Image must be a JPG, PNG, WEBP, or GIF file.';
        } elseif ($size > 5 * 1024 * 1024) {
            $error = 'Image must be smaller than 5MB.';
        } else {
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }
            $ext = $allowed[$mime];
            $filename = 'site-popup-' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (!move_uploaded_file($tmp, UPLOAD_DIR . $filename)) {
                $error = 'Could not save the uploaded image. Please try again.';
            } else {
                $image = $filename;
            }
        }
    }

    if (!$error) {
        $pdo->prepare(
            'UPDATE site_popup SET enabled=?, image=?, image_alt=?, title=?, description=?, points=?,
             cta_text=?, cta_use_booking=?, cta_link=?,
             trigger_delay=?, trigger_new_page=?, trigger_refresh=?, trigger_scroll_section=? WHERE id=1'
        )->execute([
            $enabled, $image, $imageAlt, $title, $description, $points,
            $ctaText, $ctaUseBooking, $ctaLink,
            $triggerDelay, $triggerNewPage, $triggerRefresh, $triggerScrollSection,
        ]);

        $success = 'Saved.';
        $popup = [
            'enabled' => $enabled, 'image' => $image, 'image_alt' => $imageAlt, 'title' => $title,
            'description' => $description, 'points' => $points, 'cta_text' => $ctaText,
            'cta_use_booking' => $ctaUseBooking, 'cta_link' => $ctaLink,
            'trigger_delay' => $triggerDelay, 'trigger_new_page' => $triggerNewPage,
            'trigger_refresh' => $triggerRefresh, 'trigger_scroll_section' => $triggerScrollSection,
        ];
    }
}

$pageTitle = 'Consultation Popup';
$pageSub = 'The popup shown to visitors when they open the site.';
$activeNav = 'popup';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="card">
    <label class="checkbox-row" style="max-width:320px">
      <input type="checkbox" name="enabled" <?= $popup['enabled'] ? 'checked' : '' ?>>
      Show this popup on the live site
    </label>
    <div class="field-hint">Shows once per visitor session, a moment after the page loads. Turn this off to hide it without losing your content below.</div>
  </div>

  <div class="card">
    <div class="card-title">Image</div>
    <div class="card-desc">Fills the left half of the popup.</div>
    <div class="field">
      <label>Image File</label>
      <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" data-image-input>
      <div class="upload-preview">
        <img data-image-preview src="<?= h($popup['image'] ? UPLOAD_URL . $popup['image'] : '') ?>" style="<?= $popup['image'] ? '' : 'display:none' ?>">
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
      <label for="points">Points (one per line — first 4 shown with a checkmark)</label>
      <textarea id="points" name="points" rows="4" placeholder="Custom ERP built around your process&#10;Live inventory &amp; order sync&#10;SEO and performance marketing that compounds&#10;Dedicated support, not a ticket queue"><?= h($popup['points']) ?></textarea>
    </div>
  </div>

  <div class="card">
    <div class="card-title">When Should It Appear?</div>
    <div class="card-desc">Check the trigger(s) that should show the popup. A trigger left unchecked never fires — leave at least one checked or the popup will never appear even when enabled.</div>
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
      <input type="checkbox" name="cta_use_booking" id="cta_use_booking" <?= $popup['cta_use_booking'] ? 'checked' : '' ?>>
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
    <a href="/" target="_blank" class="btn btn-ghost">View Site ↗</a>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
