<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/media-lib.php';
require_login();

/**
 * Media library — upload images once, get a permanent URL to paste anywhere
 * (page content, blog posts, OG image fields, emails).
 *
 * Files uploaded here live in uploads/media/ on the server's disk. uploads/
 * is git-ignored and the deploy workflow only syncs tracked files, so deploys
 * never touch or remove them. The folders themselves ARE the library — there's
 * no DB table, so there's nothing to migrate and nothing that can fall out of
 * sync. uploads/.htaccess also covers this subfolder, so nothing stored here
 * can ever execute as a script.
 *
 * The library also lists every image uploaded earlier through Blogs, Case
 * Studies, Page Content, Popup, and Sidebar (stored loose in uploads/), each
 * labelled with where it's used.
 *
 * Any logged-in user can upload (editors need image URLs for the pages and
 * posts they manage); only admins can delete, since deleting breaks every
 * page still using that URL — the confirm prompt names where it's used.
 */

$u = current_user();
$isAdmin = $u['role'] === 'admin';

$mediaDir = UPLOAD_DIR . 'media/';

// Allowed types, size limits and the upload itself live in
// includes/media-lib.php, shared with the picker (admin/media-api.php).
// Every file this page writes matches MEDIA_NAME_PATTERN, so it doubles as
// the guard that keeps delete requests from reaching outside uploads/media/;
// earlier uploads loose in uploads/ match MEDIA_LOOSE_NAME_PATTERN.
$mediaNamePattern = MEDIA_NAME_PATTERN;
$looseNamePattern = MEDIA_LOOSE_NAME_PATTERN;

/**
 * Normalizes a stored image reference to its path under uploads/ ("x.jpg" or
 * "media/x.jpg"), or '' if it isn't an uploaded file. Blog, case study, popup
 * and sidebar columns hold a bare filename; social image and Page Content
 * fields hold a /uploads/... path or a full URL.
 */
function media_ref_key(string $ref): string
{
    $ref = trim($ref);
    if ($ref === '' || str_starts_with($ref, 'data:')) {
        return '';
    }
    if (preg_match('#^https?://#i', $ref)) {
        $ref = (string) parse_url($ref, PHP_URL_PATH);
    }
    if (str_starts_with($ref, UPLOAD_URL)) {
        return rawurldecode(substr($ref, strlen(UPLOAD_URL)));
    }
    return str_contains($ref, '/') ? '' : $ref;
}

/**
 * Where each uploaded image is used, keyed like media_ref_key(): every column
 * the admin stores an image in. Images pasted into blog body HTML aren't
 * tracked. Each lookup is skipped quietly if its table doesn't exist yet
 * (e.g. a migration that hasn't been run), so the page always loads.
 */
function media_usage_map(PDO $pdo): array
{
    $map = [];
    $add = function ($ref, string $label) use (&$map) {
        $key = media_ref_key((string) $ref);
        if ($key !== '' && !in_array($label, $map[$key] ?? [], true)) {
            $map[$key][] = $label;
        }
    };

    try {
        foreach ($pdo->query('SELECT title, featured_image, og_image FROM blogs') as $r) {
            $add($r['featured_image'], 'Blog: ' . $r['title']);
            $add($r['og_image'], 'Blog: ' . $r['title']);
        }
    } catch (PDOException $e) {
    }
    try {
        foreach ($pdo->query('SELECT title, desktop_image, mobile_image, result_image, og_image FROM case_studies') as $r) {
            foreach (['desktop_image', 'mobile_image', 'result_image', 'og_image'] as $col) {
                $add($r[$col], 'Case study: ' . $r['title']);
            }
        }
    } catch (PDOException $e) {
    }
    try {
        foreach ($pdo->query('SELECT name, og_image FROM pages') as $r) {
            $add($r['og_image'], 'Page: ' . $r['name']);
        }
    } catch (PDOException $e) {
    }
    try {
        foreach ($pdo->query("SELECT p.name, pc.value FROM page_content pc JOIN pages p ON p.id = pc.page_id WHERE pc.kind = 'img_src'") as $r) {
            $add($r['value'], 'Page: ' . $r['name']);
        }
    } catch (PDOException $e) {
    }
    try {
        $add($pdo->query('SELECT image FROM site_popup WHERE id = 1')->fetchColumn(), 'Popup');
    } catch (PDOException $e) {
    }
    try {
        foreach ($pdo->query('SELECT name, title, image FROM popups') as $r) {
            $add($r['image'], 'Popup: ' . ($r['name'] ?: $r['title']));
        }
    } catch (PDOException $e) {
    }
    try {
        $add($pdo->query('SELECT image FROM site_sidebar WHERE id = 1')->fetchColumn(), 'Sidebar');
    } catch (PDOException $e) {
    }

    return $map;
}

/**
 * Data for the library's filter dropdowns. Reads the same columns as
 * media_usage_map(), but records which record uses each image:
 *   'refs'   => [image key => ['blog:12', 'page:3', 'other:popup', ...]]
 *   'groups' => ['blog' => [id => title], 'page' => [...], 'case' => [...]]
 * Groups list every blog, page and case study — even ones without images —
 * so each one can be picked from the dropdown.
 */
function media_filter_index(PDO $pdo): array
{
    $refs = [];
    $groups = ['blog' => [], 'page' => [], 'case' => []];
    $add = function ($ref, string $tag) use (&$refs) {
        $key = media_ref_key((string) $ref);
        if ($key !== '' && !in_array($tag, $refs[$key] ?? [], true)) {
            $refs[$key][] = $tag;
        }
    };

    try {
        foreach ($pdo->query('SELECT id, title, featured_image, og_image FROM blogs ORDER BY id DESC') as $r) {
            $groups['blog'][(int) $r['id']] = (string) $r['title'];
            $add($r['featured_image'], 'blog:' . $r['id']);
            $add($r['og_image'], 'blog:' . $r['id']);
        }
    } catch (PDOException $e) {
    }
    try {
        foreach ($pdo->query('SELECT id, name, og_image FROM pages ORDER BY name') as $r) {
            $groups['page'][(int) $r['id']] = (string) $r['name'];
            $add($r['og_image'], 'page:' . $r['id']);
        }
    } catch (PDOException $e) {
    }
    try {
        foreach ($pdo->query("SELECT page_id, value FROM page_content WHERE kind = 'img_src'") as $r) {
            $add($r['value'], 'page:' . $r['page_id']);
        }
    } catch (PDOException $e) {
    }
    try {
        foreach ($pdo->query('SELECT id, title, desktop_image, mobile_image, result_image, og_image FROM case_studies ORDER BY id DESC') as $r) {
            $groups['case'][(int) $r['id']] = (string) $r['title'];
            foreach (['desktop_image', 'mobile_image', 'result_image', 'og_image'] as $col) {
                $add($r[$col], 'case:' . $r['id']);
            }
        }
    } catch (PDOException $e) {
    }
    try {
        $add($pdo->query('SELECT image FROM site_popup WHERE id = 1')->fetchColumn(), 'other:popup');
    } catch (PDOException $e) {
    }
    try {
        foreach ($pdo->query('SELECT image FROM popups') as $r) {
            $add($r['image'], 'other:popup');
        }
    } catch (PDOException $e) {
    }
    try {
        $add($pdo->query('SELECT image FROM site_sidebar WHERE id = 1')->fetchColumn(), 'other:sidebar');
    } catch (PDOException $e) {
    }

    return ['refs' => $refs, 'groups' => $groups];
}

/**
 * A typed image name as a URL-safe file name base: lowercase letters, numbers
 * and hyphens ("Team Photo 2026.jpg" → "team-photo-2026"). '' if nothing usable.
 */
function media_slug(string $name): string
{
    $name = (string) preg_replace('/\.(jpe?g|png|webp|gif|avif)$/i', '', trim($name));
    $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-');
    return rtrim(substr($slug, 0, 80), '-');
}

/**
 * After a rename, point every stored reference at the new file so nothing on
 * the site shows a broken image. Covers each column media_usage_map() reads,
 * plus blog/case study body text and Page Content history, whether they hold
 * a bare filename, a /uploads/... path, or a full URL (which contains the
 * path). Tables that don't exist yet are skipped. Returns rows updated.
 */
function media_update_references(PDO $pdo, string $sub, string $oldFile, string $newFile): int
{
    $newPath = UPLOAD_URL . $sub . $newFile;
    $oldPaths = array_unique([UPLOAD_URL . $sub . $oldFile, UPLOAD_URL . $sub . rawurlencode($oldFile)]);
    $columns = [
        'blogs' => ['featured_image', 'og_image', 'content'],
        'case_studies' => ['desktop_image', 'mobile_image', 'result_image', 'og_image', 'problem', 'solution', 'process', 'result', 'outcome'],
        'pages' => ['og_image'],
        'page_content' => ['value'],
        'page_content_versions' => ['snapshot'],
        'site_popup' => ['image'],
        'popups' => ['image'],
        'site_sidebar' => ['image'],
    ];
    $n = 0;
    foreach ($columns as $table => $cols) {
        foreach ($cols as $col) {
            foreach ($oldPaths as $old) {
                try {
                    $st = $pdo->prepare("UPDATE `$table` SET `$col` = REPLACE(`$col`, ?, ?) WHERE INSTR(`$col`, ?) > 0");
                    $st->execute([$old, $newPath, $old]);
                    $n += $st->rowCount();
                } catch (PDOException $e) {
                    // table or column not there (migration not run) — nothing to update
                }
            }
        }
    }
    // Blog, case study, popup and sidebar uploads store a bare filename.
    if ($sub === '') {
        $bare = [
            'blogs' => ['featured_image'],
            'case_studies' => ['desktop_image', 'mobile_image', 'result_image'],
            'site_popup' => ['image'],
            'popups' => ['image'],
            'site_sidebar' => ['image'],
        ];
        foreach ($bare as $table => $cols) {
            foreach ($cols as $col) {
                try {
                    $st = $pdo->prepare("UPDATE `$table` SET `$col` = ? WHERE `$col` = ?");
                    $st->execute([$newFile, $oldFile]);
                    $n += $st->rowCount();
                } catch (PDOException $e) {
                }
            }
        }
    }
    return $n;
}

$maxFileBytes = media_max_file_bytes();
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
                $saved = media_save_upload(
                    (string) $files['tmp_name'][$i],
                    (string) $files['name'][$i],
                    (int) $files['size'][$i],
                    (int) $files['error'][$i]
                );
                if (isset($saved['error'])) {
                    $flash['errors'][] = $saved['error'];
                } elseif (isset($saved['file'])) {
                    $flash['uploaded'][] = $saved['file'];
                }
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
        $managed = ($_POST['dir'] ?? 'media') === 'media';
        $dir = $managed ? $mediaDir : UPLOAD_DIR;
        // Name patterns keep the delete inside its folder: no slashes, no
        // dotfiles (.htaccess), image extensions only.
        $validName = $managed
            ? preg_match($mediaNamePattern, $file)
            : preg_match($looseNamePattern, $file);
        if (!$validName || !is_file($dir . $file)) {
            $flash['errors'][] = 'That image no longer exists.';
        } elseif (!@unlink($dir . $file)) {
            $flash['errors'][] = 'Could not delete ' . $file . '. Please try again.';
        } else {
            $flash['success'] = 'Deleted ' . $file . '.';
        }
    } elseif ($action === 'rename') {
        // Renaming changes the image's URL, so — like delete — admins only.
        if (!$isAdmin) {
            http_response_code(403);
            die('Only admins can rename images in the media library.');
        }
        $file = (string) ($_POST['file'] ?? '');
        $managed = ($_POST['dir'] ?? 'media') === 'media';
        $dir = $managed ? $mediaDir : UPLOAD_DIR;
        $sub = $managed ? 'media/' : '';
        $validName = $managed
            ? preg_match($mediaNamePattern, $file)
            : preg_match($looseNamePattern, $file);
        $base = media_slug((string) ($_POST['new_name'] ?? ''));

        if (!$validName || !is_file($dir . $file)) {
            $flash['errors'][] = 'That image no longer exists.';
        } elseif ($base === '') {
            $flash['errors'][] = 'Enter a name using letters or numbers.';
        } else {
            // The extension stays as it is: it reflects the real file type.
            $newFile = $base . '.' . strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($newFile === $file) {
                $flash['success'] = 'Nothing changed — the image is already called ' . $file . '.';
            } elseif (file_exists($dir . $newFile)) {
                $flash['errors'][] = 'Another image is already called ' . $newFile . '. Choose a different name.';
            } elseif (!@rename($dir . $file, $dir . $newFile)) {
                $flash['errors'][] = 'Could not rename ' . $file . '. Please try again.';
            } else {
                $updated = media_update_references($pdo, $sub, $file, $newFile);
                $flash['success'] = 'Renamed to ' . $newFile . '. '
                    . ($updated > 0
                        ? 'Its URL changed, and the ' . $updated . ' place' . ($updated === 1 ? '' : 's') . ' on the site that used it now use the new URL.'
                        : 'Its URL changed — paste the new one wherever you need it.');
                $flash['highlight'] = $sub . $newFile;
            }
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
$highlight = (string) ($flash['highlight'] ?? '');

$usage = media_usage_map($pdo);
$filterIndex = media_filter_index($pdo);
$refCounts = []; // 'blog:12' / 'blog' / 'unused' => number of images

// Images uploaded here (uploads/media/) plus every earlier upload sitting
// loose in uploads/ from Blogs, Case Studies, Page Content, Popup, Sidebar.
$items = [];
$totalBytes = 0;
$earlierCount = 0;
foreach (['' => UPLOAD_DIR, 'media/' => $mediaDir] as $sub => $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    $managed = $sub === 'media/';
    foreach (scandir($dir) ?: [] as $f) {
        $isImage = $managed
            ? preg_match($mediaNamePattern, $f)
            : preg_match($looseNamePattern, $f);
        if (!$isImage || !is_file($dir . $f)) {
            continue;
        }
        $dims = @getimagesize($dir . $f);
        $size = (int) filesize($dir . $f);
        $totalBytes += $size;
        if (!$managed) {
            $earlierCount++;
        }
        // Filter tokens: each record ("blog:12") plus its type ("blog"), or
        // "unused" — what the Show / Blog dropdowns match against.
        $tokens = [];
        foreach ($filterIndex['refs'][$sub . $f] ?? [] as $ref) {
            $tokens[$ref] = true;
            $tokens[strtok($ref, ':')] = true;
        }
        if (!$tokens) {
            $tokens['unused'] = true;
        }
        foreach ($tokens as $t => $_) {
            $refCounts[$t] = ($refCounts[$t] ?? 0) + 1;
        }
        $items[] = [
            'file'    => $f,
            'managed' => $managed,
            'path'    => UPLOAD_URL . $sub . rawurlencode($f),
            'usage'   => $usage[$sub . $f] ?? [],
            'tokens'  => implode(' ', array_keys($tokens)),
            'size'    => $size,
            'mtime'   => (int) filemtime($dir . $f),
            'w'       => $dims ? (int) $dims[0] : 0,
            'h'       => $dims ? (int) $dims[1] : 0,
        ];
    }
}
usort($items, function ($a, $b) {
    return [$b['mtime'], $a['file']] <=> [$a['mtime'], $b['file']];
});

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
      <div class="card-desc">
        <?= count($items) ?> image<?= count($items) === 1 ? '' : 's' ?><?= $items ? ' · ' . h(media_human_size($totalBytes)) . ' total' : '' ?>
        <?php if ($earlierCount): ?>
          · includes <?= $earlierCount ?> uploaded earlier from Blogs, Case Studies, Pages, Popup, and Sidebar
        <?php endif; ?>
      </div>
    </div>
    <?php if ($items):
        $typeNames = ['blog' => ['Blogs', 'All blogs', 'Blog'], 'page' => ['Pages', 'All pages', 'Page'], 'case' => ['Case Studies', 'All case studies', 'Case study']];
    ?>
      <div class="media-filters">
        <select class="media-filter" aria-label="Show images from" data-media-type>
          <option value="">All images (<?= count($items) ?>)</option>
          <?php foreach ($typeNames as $type => $names): ?>
            <option value="<?= $type ?>"><?= h($names[0]) ?> (<?= (int) ($refCounts[$type] ?? 0) ?>)</option>
          <?php endforeach; ?>
          <option value="other">Popup &amp; Sidebar (<?= (int) ($refCounts['other'] ?? 0) ?>)</option>
          <option value="unused">Not used anywhere (<?= (int) ($refCounts['unused'] ?? 0) ?>)</option>
        </select>
        <?php foreach ($typeNames as $type => $names): ?>
          <select class="media-filter media-filter-item" aria-label="Pick one <?= h(strtolower($names[2])) ?>" data-media-record="<?= $type ?>" hidden>
            <option value=""><?= h($names[1]) ?> (<?= (int) ($refCounts[$type] ?? 0) ?>)</option>
            <?php foreach ($filterIndex['groups'][$type] as $id => $title):
                $key = $type . ':' . $id; ?>
              <option value="<?= h($key) ?>"><?= h(mb_strimwidth($title, 0, 70, '…')) ?> (<?= (int) ($refCounts[$key] ?? 0) ?>)</option>
            <?php endforeach; ?>
          </select>
        <?php endforeach; ?>
        <input type="search" class="media-search" placeholder="Search by name, blog, page…" aria-label="Search images by file name or where they're used" data-media-search>
      </div>
    <?php endif; ?>
  </div>
  <?php if ($items): ?><div class="media-count" data-media-count hidden></div><?php endif; ?>

  <?php if (!$items): ?>
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
      <p>No images yet. Upload one above to get its URL.</p>
    </div>
  <?php else: ?>
    <div class="media-grid">
      <?php foreach ($items as $it):
          $path = $it['path'];
          $url = $baseUrl . $path;
          $isNew = $it['managed'] && isset($newFiles[$it['file']]);
          $isRenamed = $highlight !== '' && ($it['managed'] ? 'media/' : '') . $it['file'] === $highlight;
          $usedIn = implode(', ', $it['usage']);
          $meta = ($it['w'] ? (int) $it['w'] . ' × ' . (int) $it['h'] . ' · ' : '') . media_human_size($it['size']) . ' · ' . date('M j, Y', $it['mtime']);
          $searchText = mb_strtolower($it['file'] . ' ' . $usedIn . ($it['managed'] ? '' : ' earlier upload'));
          $deleteMsg = 'Delete this image permanently?'
              . ($usedIn !== '' ? ' It is used in: ' . $usedIn . ' — that will show a broken image.' : ' Any page or post still using its URL will show a broken image.');
      ?>
      <div class="media-item<?= $isNew || $isRenamed ? ' is-new' : '' ?>" data-media-item data-search="<?= h($searchText) ?>" data-refs="<?= h($it['tokens']) ?>"
           data-file="<?= h($it['file']) ?>" data-dir="<?= $it['managed'] ? 'media' : 'root' ?>" data-path="<?= h($path) ?>"
           data-url="<?= h($url) ?>" data-meta="<?= h($meta) ?>" data-used="<?= h($usedIn) ?>"<?= $isRenamed ? ' data-media-renamed' : '' ?>>
        <a class="media-thumb" href="<?= h($path) ?>" target="_blank" rel="noopener" title="Edit name and URL" data-media-open>
          <img src="<?= h($path) ?>" alt="" loading="lazy">
          <span class="media-thumb-hint" aria-hidden="true">Edit name &amp; URL</span>
        </a>
        <div class="media-body">
          <button type="button" class="media-name" title="<?= h($it['file']) ?> — click to rename" data-media-open>
            <?php if ($isNew): ?><span class="badge badge-published">New</span><?php endif; ?>
            <?php if ($isRenamed): ?><span class="badge badge-published">Renamed</span><?php endif; ?>
            <?= h($it['file']) ?>
          </button>
          <div class="media-meta"><?= h($meta) ?></div>
          <?php if ($usedIn !== ''): ?>
            <div class="media-source" title="Used in <?= h($usedIn) ?>">Used in <?= h($usedIn) ?></div>
          <?php elseif (!$it['managed']): ?>
            <div class="media-source is-plain" title="Uploaded before the Media library, from Blogs, Case Studies, Pages, Popup, or Sidebar">Earlier upload</div>
          <?php endif; ?>
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
              <form method="post" onsubmit="return confirm(<?= h(json_encode($deleteMsg, JSON_UNESCAPED_UNICODE)) ?>);">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="dir" value="<?= $it['managed'] ? 'media' : 'root' ?>">
                <input type="hidden" name="file" value="<?= h($it['file']) ?>">
                <button type="submit" class="link-btn link-danger">Delete</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="empty-state" data-media-noresults hidden><p>No images match these filters.</p></div>
  <?php endif; ?>
</div>

<?php if ($items): ?>
<dialog class="media-dialog" id="mediaDialog" aria-labelledby="mediaDialogTitle">
  <form method="post" class="media-dialog-form" data-media-rename data-base-url="<?= h($baseUrl) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="rename">
    <input type="hidden" name="dir" value="" data-d-dir>
    <input type="hidden" name="file" value="" data-d-file>
    <div class="media-dialog-head">
      <h2 id="mediaDialogTitle">Image details</h2>
      <button type="button" class="media-dialog-x" data-media-close aria-label="Close">×</button>
    </div>
    <div class="media-dialog-body">
      <a class="media-dialog-preview" href="#" target="_blank" rel="noopener" title="Open full size" data-d-open>
        <img src="" alt="" data-d-img>
      </a>
      <div class="media-dialog-info">
        <div class="media-meta" data-d-meta></div>
        <div class="media-source" data-d-used></div>

        <label class="media-dialog-label" for="mediaNewName">Image name</label>
        <div class="media-name-field">
          <input type="text" id="mediaNewName" name="new_name" maxlength="80" autocomplete="off" spellcheck="false" required data-d-name<?= $isAdmin ? '' : ' readonly' ?>>
          <span class="media-ext" data-d-ext></span>
        </div>
        <div class="field-hint">Lowercase letters, numbers and hyphens. A descriptive name (e.g. <code>chennai-erp-dashboard</code>) helps the image rank in search.</div>

        <label class="media-dialog-label" for="mediaUrl">Image URL</label>
        <div class="media-url">
          <input type="text" id="mediaUrl" spellcheck="false" data-d-url<?= $isAdmin ? '' : ' readonly' ?>>
          <button type="button" class="btn btn-ghost btn-sm" data-copy="" data-d-copy>Copy URL</button>
        </div>
        <div class="field-hint" data-d-url-hint>Editing the URL changes the image name too — they always match.</div>

        <div class="media-dialog-note" data-d-note hidden></div>
        <?php if (!$isAdmin): ?><p class="media-dialog-note">Only admins can rename images.</p><?php endif; ?>
      </div>
    </div>
    <div class="media-dialog-foot">
      <button type="button" class="btn btn-ghost" data-media-close>Cancel</button>
      <?php if ($isAdmin): ?><button type="submit" class="btn btn-primary" data-d-save disabled>Save new name</button><?php endif; ?>
    </div>
  </form>
</dialog>
<?php endif; ?>

<script src="<?= asset_url('/admin/assets/media.js') ?>" defer></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
