<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

/**
 * Media library — upload images once, get a permanent URL to paste anywhere
 * (page content, blog posts, OG image fields, emails).
 *
 * Files live in uploads/media/ on the server's disk. uploads/ is git-ignored
 * and the deploy workflow only syncs tracked files, so deploys never touch
 * or remove them. The folder itself IS the library — there's no DB table,
 * so there's nothing to migrate and nothing that can fall out of sync.
 * uploads/.htaccess also covers this subfolder, so nothing stored here can
 * ever execute as a script.
 *
 * Any logged-in user can upload (editors need image URLs for the pages and
 * posts they manage); only admins can delete, since deleting breaks every
 * page still using that URL.
 */

$u = current_user();
$isAdmin = $u['role'] === 'admin';

$mediaDir = UPLOAD_DIR . 'media/';
$mediaPath = UPLOAD_URL . 'media/';

// SVG is deliberately not accepted: it can carry script, and these files are
// served from the same origin as the admin panel.
$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
    'image/avif' => 'avif',
];
// Every file this page writes matches this, so it doubles as the guard that
// keeps delete requests from reaching outside uploads/media/.
$mediaNamePattern = '/^[a-z0-9][a-z0-9-]*\.(jpg|png|webp|gif|avif)$/';

/** Converts a php.ini size like "64M" to bytes (0 = no limit). */
function media_ini_bytes(string $value): int
{
    $value = trim($value);
    if ($value === '') {
        return 0;
    }
    $bytes = (int) $value;
    switch (strtolower(substr($value, -1))) {
        case 'g':
            $bytes *= 1024;
            // no break
        case 'm':
            $bytes *= 1024;
            // no break
        case 'k':
            $bytes *= 1024;
    }
    return $bytes;
}

function media_human_size(int $bytes): string
{
    if ($bytes >= 1048576) {
        return rtrim(rtrim(number_format($bytes / 1048576, 1), '0'), '.') . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024) . ' KB';
    }
    return $bytes . ' B';
}

// Per-image cap is 10MB, or lower if the server's own upload limit is lower.
$maxFileBytes = 10 * 1024 * 1024;
$serverFileLimit = media_ini_bytes((string) ini_get('upload_max_filesize'));
if ($serverFileLimit > 0) {
    $maxFileBytes = min($maxFileBytes, $serverFileLimit);
}
$postLimit = media_ini_bytes((string) ini_get('post_max_size'));
$maxFiles = (int) ini_get('max_file_uploads') ?: 20;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A request bigger than post_max_size arrives with $_POST and $_FILES both
    // empty — CSRF token included. Catch that first so the message says "too
    // large" rather than "your session expired".
    if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        $_SESSION['media_flash'] = [
            'errors' => ['That upload was larger than the server accepts in one go (' . media_human_size($postLimit) . ' in total). Upload fewer or smaller images at a time.'],
            'uploaded' => [],
            'success' => '',
        ];
        header('Location: media.php');
        exit;
    }

    csrf_verify();

    $flash = ['errors' => [], 'uploaded' => [], 'success' => ''];
    $action = $_POST['action'] ?? '';

    if ($action === 'upload') {
        $files = $_FILES['files'] ?? null;
        $count = (is_array($files) && is_array($files['name'] ?? null)) ? count($files['name']) : 0;

        if ($count === 0) {
            $flash['errors'][] = 'Choose at least one image to upload.';
        } elseif (!is_dir($mediaDir) && !@mkdir($mediaDir, 0755, true) && !is_dir($mediaDir)) {
            $flash['errors'][] = 'Could not create the uploads/media folder on the server. Check that uploads/ is writable.';
        } else {
            for ($i = 0; $i < $count; $i++) {
                $originalName = (string) $files['name'][$i];
                $err = (int) $files['error'][$i];

                if ($err === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                    $flash['errors'][] = $originalName . ': larger than the ' . media_human_size($maxFileBytes) . ' limit.';
                    continue;
                }
                if ($err !== UPLOAD_ERR_OK) {
                    $flash['errors'][] = $originalName . ': upload failed (error ' . $err . '). Please try again.';
                    continue;
                }

                $tmp = $files['tmp_name'][$i];
                $size = (int) $files['size'][$i];
                $mime = @mime_content_type($tmp);

                if (!is_string($mime) || !isset($allowedTypes[$mime])) {
                    $flash['errors'][] = $originalName . ': not a supported image. Use JPG, PNG, WEBP, GIF, or AVIF.';
                    continue;
                }
                if ($size > $maxFileBytes) {
                    $flash['errors'][] = $originalName . ': larger than the ' . media_human_size($maxFileBytes) . ' limit.';
                    continue;
                }

                // Keep the original name (good for SEO), cleaned to a URL-safe
                // slug. The extension always comes from the detected file type,
                // never from what the file claimed to be, and a short random
                // suffix means an upload never overwrites an existing image.
                $base = strtolower(pathinfo($originalName, PATHINFO_FILENAME));
                $base = trim((string) preg_replace('/[^a-z0-9]+/', '-', $base), '-');
                $base = rtrim(substr($base, 0, 60), '-');
                if ($base === '') {
                    $base = 'image';
                }
                do {
                    $filename = $base . '-' . bin2hex(random_bytes(3)) . '.' . $allowedTypes[$mime];
                } while (is_file($mediaDir . $filename));

                if (!move_uploaded_file($tmp, $mediaDir . $filename)) {
                    $flash['errors'][] = $originalName . ': could not be saved on the server. Please try again.';
                    continue;
                }
                @chmod($mediaDir . $filename, 0644);
                $flash['uploaded'][] = $filename;
            }

            $n = count($flash['uploaded']);
            if ($n > 0) {
                $flash['success'] = $n === 1
                    ? 'Image uploaded — its URL is ready to copy below.'
                    : $n . ' images uploaded — their URLs are ready to copy below.';
            } elseif (empty($flash['errors'])) {
                $flash['errors'][] = 'Choose at least one image to upload.';
            }
        }
    } elseif ($action === 'delete') {
        if (!$isAdmin) {
            http_response_code(403);
            die('Only admins can delete images from the media library.');
        }
        $file = (string) ($_POST['file'] ?? '');
        if (!preg_match($mediaNamePattern, $file) || !is_file($mediaDir . $file)) {
            $flash['errors'][] = 'That image no longer exists.';
        } elseif (!@unlink($mediaDir . $file)) {
            $flash['errors'][] = 'Could not delete ' . $file . '. Please try again.';
        } else {
            $flash['success'] = 'Deleted ' . $file . '.';
        }
    }

    // Post/Redirect/Get, so refreshing the page never re-uploads anything.
    $_SESSION['media_flash'] = $flash;
    header('Location: media.php');
    exit;
}

$flash = $_SESSION['media_flash'] ?? ['errors' => [], 'uploaded' => [], 'success' => ''];
unset($_SESSION['media_flash']);
$newFiles = array_flip($flash['uploaded'] ?? []);

$items = [];
$totalBytes = 0;
if (is_dir($mediaDir)) {
    foreach (scandir($mediaDir) ?: [] as $f) {
        if (!preg_match($mediaNamePattern, $f) || !is_file($mediaDir . $f)) {
            continue;
        }
        $dims = @getimagesize($mediaDir . $f);
        $size = (int) filesize($mediaDir . $f);
        $totalBytes += $size;
        $items[] = [
            'file'  => $f,
            'size'  => $size,
            'mtime' => (int) filemtime($mediaDir . $f),
            'w'     => $dims ? (int) $dims[0] : 0,
            'h'     => $dims ? (int) $dims[1] : 0,
        ];
    }
    usort($items, function ($a, $b) {
        return [$b['mtime'], $a['file']] <=> [$a['mtime'], $b['file']];
    });
}

$baseUrl = site_base_url();

$pageTitle = 'Media';
$pageSub = 'Upload images and copy their URLs to use anywhere on the site.';
$activeNav = 'media';
include __DIR__ . '/includes/header.php';
?>

<?php if (!empty($flash['errors'])): ?>
<div class="alert alert-error">
  <?php if (count($flash['errors']) === 1): ?>
    <?= h($flash['errors'][0]) ?>
  <?php else: ?>
    Some images weren't uploaded:
    <ul class="alert-list">
      <?php foreach ($flash['errors'] as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
<?php endif; ?>
<?php if (!empty($flash['success'])): ?><div class="alert alert-success"><?= h($flash['success']) ?></div><?php endif; ?>

<div class="card">
  <div class="card-title">Upload Images</div>
  <div class="card-desc">
    JPG, PNG, WEBP, GIF, or AVIF · up to <?= h(media_human_size($maxFileBytes)) ?> each · up to <?= (int) $maxFiles ?> at a time.
    The file name is kept (cleaned up for SEO) with a short code added, so an upload never replaces an existing image.
  </div>
  <form method="post" enctype="multipart/form-data" data-media-upload
        data-max-bytes="<?= (int) $maxFileBytes ?>" data-max-files="<?= (int) $maxFiles ?>" data-post-limit="<?= (int) $postLimit ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="upload">
    <label class="dropzone" data-dropzone>
      <input type="file" name="files[]" multiple accept="image/jpeg,image/png,image/webp,image/gif,image/avif" data-media-input>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/></svg>
      <span class="dropzone-title">Drag images here, or <u>browse your computer</u></span>
      <span class="dropzone-sub" data-dropzone-status>You can pick several at once — they upload as soon as you choose them.</span>
    </label>
    <noscript><div style="margin-top:1rem"><button type="submit" class="btn btn-primary">Upload</button></div></noscript>
  </form>
</div>

<div class="card">
  <div class="media-head">
    <div>
      <div class="card-title">Library</div>
      <div class="card-desc"><?= count($items) ?> image<?= count($items) === 1 ? '' : 's' ?><?= $items ? ' · ' . h(media_human_size($totalBytes)) . ' total' : '' ?></div>
    </div>
    <?php if ($items): ?>
      <input type="search" class="media-search" placeholder="Search by file name" aria-label="Search images by file name" data-media-search>
    <?php endif; ?>
  </div>

  <?php if (!$items): ?>
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
      <p>No images yet. Upload one above to get its URL.</p>
    </div>
  <?php else: ?>
    <div class="media-grid">
      <?php foreach ($items as $it):
          $path = $mediaPath . $it['file'];
          $url = $baseUrl . $path;
          $isNew = isset($newFiles[$it['file']]);
      ?>
      <div class="media-item<?= $isNew ? ' is-new' : '' ?>" data-media-item data-name="<?= h($it['file']) ?>">
        <a class="media-thumb" href="<?= h($path) ?>" target="_blank" rel="noopener" title="Open full size">
          <img src="<?= h($path) ?>" alt="" loading="lazy">
        </a>
        <div class="media-body">
          <div class="media-name" title="<?= h($it['file']) ?>">
            <?php if ($isNew): ?><span class="badge badge-published">New</span><?php endif; ?>
            <?= h($it['file']) ?>
          </div>
          <div class="media-meta">
            <?= $it['w'] ? (int) $it['w'] . ' × ' . (int) $it['h'] . ' · ' : '' ?><?= h(media_human_size($it['size'])) ?> · <?= h(date('M j, Y', $it['mtime'])) ?>
          </div>
          <div class="media-url">
            <input type="text" readonly value="<?= h($url) ?>" aria-label="Image URL" data-copy-source>
            <button type="button" class="btn btn-ghost btn-sm" data-copy="<?= h($url) ?>">Copy URL</button>
          </div>
          <div class="media-actions">
            <button type="button" class="link-btn" data-copy="<?= h($path) ?>" title="Copies <?= h($path) ?> — keeps working if the site's domain changes">Copy path</button>
            <span aria-hidden="true">·</span>
            <a class="link-btn" href="<?= h($path) ?>" target="_blank" rel="noopener">Open ↗</a>
            <?php if ($isAdmin): ?>
              <span aria-hidden="true">·</span>
              <form method="post" onsubmit="return confirm('Delete this image permanently? Any page or post still using its URL will show a broken image.');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="file" value="<?= h($it['file']) ?>">
                <button type="submit" class="link-btn link-danger">Delete</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="empty-state" data-media-noresults hidden><p>No images match that search.</p></div>
  <?php endif; ?>
</div>

<script src="<?= asset_url('/admin/assets/media.js') ?>" defer></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
