<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$sidebar = get_site_sidebar($pdo);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $enabled = isset($_POST['enabled']) ? 1 : 0;
    $imageAlt = trim($_POST['image_alt'] ?? '');
    $title = trim($_POST['title'] ?? '') ?: 'Book a Consultation';
    $text = trim($_POST['text'] ?? '');
    $ctaText = trim($_POST['cta_text'] ?? '') ?: 'Book a Free Consultation';
    $ctaUseBooking = isset($_POST['cta_use_booking']) ? 1 : 0;
    $ctaLink = trim($_POST['cta_link'] ?? '');

    $image = $sidebar['image'];
    if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
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
            $filename = 'site-sidebar-' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (!move_uploaded_file($tmp, UPLOAD_DIR . $filename)) {
                $error = 'Could not save the uploaded image. Please try again.';
            } else {
                $image = $filename;
            }
        }
    }

    if (!$error) {
        $pdo->prepare(
            'UPDATE site_sidebar SET enabled=?, image=?, image_alt=?, title=?, text=?,
             cta_text=?, cta_use_booking=?, cta_link=? WHERE id=1'
        )->execute([
            $enabled, $image, $imageAlt, $title, $text,
            $ctaText, $ctaUseBooking, $ctaLink,
        ]);

        $success = 'Saved.';
        $sidebar = [
            'enabled' => $enabled, 'image' => $image, 'image_alt' => $imageAlt, 'title' => $title, 'text' => $text,
            'cta_text' => $ctaText, 'cta_use_booking' => $ctaUseBooking, 'cta_link' => $ctaLink,
        ];
    }
}

$pageTitle = 'Sidebar';
$pageSub = 'The CTA block shown below Recent Posts on blog and case study pages.';
$activeNav = 'sidebar';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="card">
    <label class="checkbox-row" style="max-width:320px">
      <input type="checkbox" name="enabled" <?= $sidebar['enabled'] ? 'checked' : '' ?>>
      Show this block in the sidebar
    </label>
    <div class="field-hint">Turn this off to show only Recent Posts in the sidebar, without a CTA block.</div>
  </div>

  <div class="card">
    <div class="card-title">Image</div>
    <div class="card-desc">Optional — shown above the title.</div>
    <div class="field">
      <label>Image File</label>
      <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" data-image-input>
      <div class="upload-preview">
        <img data-image-preview src="<?= h($sidebar['image'] ? UPLOAD_URL . $sidebar['image'] : '') ?>" style="<?= $sidebar['image'] ? '' : 'display:none' ?>">
      </div>
    </div>
    <div class="field">
      <label for="image_alt">Image Alt Text</label>
      <input type="text" id="image_alt" name="image_alt" maxlength="190" value="<?= h($sidebar['image_alt']) ?>">
    </div>
  </div>

  <div class="card">
    <div class="card-title">Text</div>
    <div class="field">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" maxlength="190" value="<?= h($sidebar['title']) ?>">
    </div>
    <div class="field">
      <label for="text">Description</label>
      <textarea id="text" name="text" rows="3"><?= h($sidebar['text']) ?></textarea>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Call to Action</div>
    <div class="field">
      <label for="cta_text">Button Text</label>
      <input type="text" id="cta_text" name="cta_text" maxlength="100" value="<?= h($sidebar['cta_text']) ?>">
    </div>
    <label class="checkbox-row" style="max-width:340px;margin-bottom:1.1rem">
      <input type="checkbox" name="cta_use_booking" <?= $sidebar['cta_use_booking'] ? 'checked' : '' ?>>
      Open the booking popup (recommended)
    </label>
    <div class="field">
      <label for="cta_link">Or a Custom Link</label>
      <input type="text" id="cta_link" name="cta_link" maxlength="255" value="<?= h($sidebar['cta_link']) ?>" placeholder="https://... or /case-studies">
      <div class="field-hint">Only used when "Open the booking popup" above is unchecked.</div>
    </div>
  </div>

  <div class="card">
    <button type="submit" class="btn btn-primary">Save Sidebar</button>
    <a href="/blog" target="_blank" class="btn btn-ghost">View a Blog Post ↗</a>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
