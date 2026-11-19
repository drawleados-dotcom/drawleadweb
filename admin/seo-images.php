<?php
/**
 * Admin → SEO → Image SEO: alt text for every blog image (featured and inside
 * the post), a scan of page templates for images without alt, heavy or badly
 * named files in the Media library, and the site-wide "fill missing alt text"
 * switch (seo_auto_alt_filter, applied by index.php).
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/media-lib.php';
require_admin();

$cfg = seo_image_settings($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'settings') {
        seo_save_json_setting($pdo, 'seo_images', ['auto_alt' => isset($_POST['auto_alt']) ? 1 : 0]);
        seo_flash(isset($_POST['auto_alt']) ? 'Images without alt text now get one from their file name on the live site.' : 'Automatic alt text switched off.');
        seo_back('seo-images.php');
    }

    if ($action === 'alts') {
        $changed = 0;
        // Featured images
        $featured = (array) ($_POST['featured'] ?? []);
        $featuredOrig = (array) ($_POST['featured_orig'] ?? []);
        $upd = $pdo->prepare('UPDATE blogs SET featured_image_alt = ? WHERE id = ?');
        foreach ($featured as $id => $alt) {
            $alt = mb_substr(trim((string) $alt), 0, 190);
            if ($alt !== trim((string) ($featuredOrig[$id] ?? ''))) {
                $upd->execute([$alt, (int) $id]);
                $changed++;
            }
        }
        // Images inside post content: inline[blogId][index]
        $inline = (array) ($_POST['inline'] ?? []);
        $inlineOrig = (array) ($_POST['inline_orig'] ?? []);
        $get = $pdo->prepare('SELECT content FROM blogs WHERE id = ?');
        $save = $pdo->prepare('UPDATE blogs SET content = ? WHERE id = ?');
        foreach ($inline as $blogId => $alts) {
            $todo = [];
            foreach ((array) $alts as $idx => $alt) {
                $alt = mb_substr(trim((string) $alt), 0, 250);
                if ($alt !== trim((string) ($inlineOrig[$blogId][$idx] ?? ''))) {
                    $todo[(int) $idx] = $alt;
                }
            }
            if (!$todo) {
                continue;
            }
            $get->execute([(int) $blogId]);
            $content = $get->fetchColumn();
            if ($content === false) {
                continue;
            }
            foreach ($todo as $idx => $alt) {
                $next = seo_set_inline_alt((string) $content, $idx, $alt);
                if ($next !== null) {
                    $content = $next;
                    $changed++;
                }
            }
            $save->execute([$content, (int) $blogId]);
        }
        seo_flash($changed ? "$changed alt text(s) saved — live now." : 'Nothing changed.');
        seo_back('seo-images.php' . (isset($_GET['all']) ? '?all=1' : ''));
    }
}

$showAll = isset($_GET['all']);

$blogs = $pdo->query("SELECT id, title, slug, status, featured_image, featured_image_alt FROM blogs WHERE featured_image <> '' ORDER BY created_at DESC")->fetchAll();
$featuredMissing = count(array_filter($blogs, fn ($b) => trim((string) $b['featured_image_alt']) === ''));
$inlineAll = seo_blog_inline_images($pdo);
$inlineMissing = array_values(array_filter($inlineAll, fn ($i) => trim($i['alt']) === ''));
$inlineShown = $showAll ? $inlineAll : $inlineMissing;
$featuredShown = $showAll ? $blogs : array_values(array_filter($blogs, fn ($b) => trim((string) $b['featured_image_alt']) === ''));

$media = seo_media_report();
$mediaProblems = array_values(array_filter($media, fn ($m) => $m['problems']));
$mediaBytes = array_sum(array_column($media, 'bytes'));

// Page templates: rendered on request (it takes a moment on a big site).
$scan = null;
if (isset($_GET['scan'])) {
    $scan = [];
    $pagesRows = $pdo->query('SELECT * FROM pages ORDER BY name')->fetchAll();
    foreach ($pagesRows as $p) {
        $html = pc_render_template($pdo, $p);
        if ($html === null) {
            continue;
        }
        try {
            $html = page_content_apply($pdo, (int) $p['id'], $html);
        } catch (Throwable $e) {
            // keep the plain template output
        }
        if (!preg_match_all('/<img\b[^>]*>/i', $html, $m)) {
            continue;
        }
        foreach ($m[0] as $tag) {
            $attrs = pc_parse_attrs($tag);
            $alt = pc_attr($attrs, 'alt');
            if ($alt === null || trim($alt) === '') {
                $src = (string) pc_attr($attrs, 'src');
                $scan[] = ['page' => $p, 'src' => $src, 'auto' => seo_alt_from_filename($src)];
            }
        }
    }
}

$imgSrc = fn (string $s) => preg_match('#^(https?:|/|data:)#i', $s) ? $s : UPLOAD_URL . $s;

$pageTitle = 'Image SEO';
$pageSub = 'Alt text helps Google understand images and makes the site usable with screen readers.';
$activeNav = 'seo-images';
include __DIR__ . '/includes/header.php';
?>

<?= seo_flash_html() ?>

<div class="stat-grid">
  <div class="stat-card"><div class="stat-v"><?= count($inlineMissing) ?><span style="font-size:15px;color:var(--g400)"> / <?= count($inlineAll) ?></span></div><div class="stat-l">Post images without alt</div></div>
  <div class="stat-card"><div class="stat-v"><?= $featuredMissing ?><span style="font-size:15px;color:var(--g400)"> / <?= count($blogs) ?></span></div><div class="stat-l">Featured images using post title</div></div>
  <div class="stat-card"><div class="stat-v"><?= count($mediaProblems) ?><span style="font-size:15px;color:var(--g400)"> / <?= count($media) ?></span></div><div class="stat-l">Library files to improve</div></div>
  <div class="stat-card"><div class="stat-v"><?= h(media_human_size((int) $mediaBytes)) ?></div><div class="stat-l">Library size</div></div>
</div>

<form class="card" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="settings">
  <div class="card-title">Automatic alt text</div>
  <label class="seo-switch-row" style="margin-top:.8rem">
    <input type="checkbox" name="auto_alt" <?= $cfg['auto_alt'] ? 'checked' : '' ?> onchange="this.form.submit()">
    <span><b>Fill in missing alt text from the file name</b>
      <span>On every public page, an image with no alt gets one from its file name — <code>crm-dashboard-a1b2c3.webp</code> becomes "Crm dashboard". Images with meaningless names (IMG_1234) are left alone. Your own alt text always wins, so it's still best to write it.</span></span>
  </label>
</form>

<form method="post" style="margin-top:1.25rem">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="alts">

  <div class="card">
    <div class="toolbar" style="margin-bottom:1rem">
      <div>
        <div class="card-title">Images inside blog posts</div>
        <div class="card-desc" style="margin:0"><?= $showAll ? 'All ' . count($inlineAll) . ' images.' : 'Only the ' . count($inlineMissing) . ' without alt text.' ?> Describe what the image shows in a few words.</div>
      </div>
      <a class="btn btn-ghost btn-sm" href="seo-images.php<?= $showAll ? '' : '?all=1' ?>"><?= $showAll ? 'Show only missing' : 'Show all images' ?></a>
    </div>
    <?php if (!$inlineShown): ?>
      <div class="seo-empty"><?= $inlineAll ? 'Every image inside a blog post has alt text. ✓' : 'No images inside blog posts yet.' ?></div>
    <?php else: ?>
    <div class="table-wrap">
    <table class="seo-bulk">
      <thead><tr><th style="width:80px">Image</th><th>Post</th><th style="width:45%">Alt text</th></tr></thead>
      <tbody>
      <?php foreach ($inlineShown as $img): $auto = seo_alt_from_filename($img['src']); ?>
        <tr>
          <td><img class="seo-thumb" src="<?= h($img['src']) ?>" alt="" loading="lazy"></td>
          <td>
            <a class="row-link" href="blog-edit.php?id=<?= (int) $img['blog_id'] ?>"><?= h($img['post']) ?></a>
            <div class="t-sub seo-path"><?= h(basename((string) parse_url($img['src'], PHP_URL_PATH))) ?></div>
            <?php if ($img['status'] !== 'published'): ?><span class="badge badge-draft"><?= h($img['status']) ?></span><?php endif; ?>
          </td>
          <td>
            <input type="text" name="inline[<?= (int) $img['blog_id'] ?>][<?= (int) $img['index'] ?>]" value="<?= h($img['alt']) ?>" maxlength="250" placeholder="<?= h($auto !== '' ? 'e.g. ' . $auto : 'Describe the image') ?>">
            <input type="hidden" name="inline_orig[<?= (int) $img['blog_id'] ?>][<?= (int) $img['index'] ?>]" value="<?= h($img['alt']) ?>">
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-title">Blog featured images</div>
    <div class="card-desc"><?= $showAll ? 'Every post with a featured image.' : 'Posts whose featured image has no alt text (the post title is used instead).' ?></div>
    <?php if (!$featuredShown): ?>
      <div class="seo-empty"><?= $blogs ? 'Every featured image has its own alt text. ✓' : 'No posts with a featured image yet.' ?></div>
    <?php else: ?>
    <div class="table-wrap">
    <table class="seo-bulk">
      <thead><tr><th style="width:80px">Image</th><th>Post</th><th style="width:45%">Alt text</th></tr></thead>
      <tbody>
      <?php foreach ($featuredShown as $b): ?>
        <tr>
          <td><img class="seo-thumb" src="<?= h($imgSrc((string) $b['featured_image'])) ?>" alt="" loading="lazy"></td>
          <td>
            <a class="row-link" href="blog-edit.php?id=<?= (int) $b['id'] ?>"><?= h($b['title']) ?></a>
            <?php if ($b['status'] !== 'published'): ?><div><span class="badge badge-draft"><?= h($b['status']) ?></span></div><?php endif; ?>
          </td>
          <td>
            <input type="text" name="featured[<?= (int) $b['id'] ?>]" value="<?= h((string) $b['featured_image_alt']) ?>" maxlength="190" placeholder="<?= h('Now uses: ' . $b['title']) ?>">
            <input type="hidden" name="featured_orig[<?= (int) $b['id'] ?>]" value="<?= h((string) $b['featured_image_alt']) ?>">
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php endif; ?>
    <p class="t-sub" style="margin-top:1rem">Case study images take their alt text from the case study title automatically ("Title — desktop view").</p>
  </div>

  <?php if ($inlineShown || $featuredShown): ?>
  <div class="seo-savebar">
    <span class="seo-savebar-note">Only changed fields are saved.</span>
    <button type="submit" class="btn btn-primary">Save alt text</button>
  </div>
  <?php endif; ?>
</form>

<div class="card" style="margin-top:1.25rem" id="scan">
  <div class="toolbar" style="margin-bottom:1rem">
    <div>
      <div class="card-title">Images on pages</div>
      <div class="card-desc" style="margin:0">Renders every page (Home, About, industry pages…) and lists images with no alt text. Fix them in the page's Page Content editor.</div>
    </div>
    <a class="btn btn-ghost btn-sm" href="seo-images.php?scan=1#scan"><?= $scan === null ? 'Scan pages' : 'Scan again' ?></a>
  </div>
  <?php if ($scan === null): ?>
    <div class="seo-empty">Not scanned yet.</div>
  <?php elseif (!$scan): ?>
    <div class="seo-empty">Every image on every page has alt text. ✓</div>
  <?php else: ?>
  <div class="table-wrap">
  <table>
    <thead><tr><th style="width:80px">Image</th><th>Page</th><th>File</th><th>Auto alt<?= $cfg['auto_alt'] ? ' (in use)' : ' (if switched on)' ?></th><th></th></tr></thead>
    <tbody>
    <?php foreach ($scan as $s): ?>
      <tr>
        <td><?php if ($s['src'] !== '' && !str_starts_with($s['src'], 'data:')): ?><img class="seo-thumb" src="<?= h($s['src']) ?>" alt="" loading="lazy"><?php endif; ?></td>
        <td class="t-name"><?= h($s['page']['name']) ?><div class="t-sub seo-path"><?= h($s['page']['slug']) ?></div></td>
        <td class="seo-path"><?= h(str_starts_with($s['src'], 'data:') ? 'Inline image (data:)' : basename((string) parse_url($s['src'], PHP_URL_PATH))) ?></td>
        <td><?= $s['auto'] !== '' ? h($s['auto']) : '<span class="t-sub">— (name too generic)</span>' ?></td>
        <td><a class="row-link" href="page-edit.php?id=<?= (int) $s['page']['id'] ?>&tab=content">Fix →</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</div>

<div class="card" style="margin-top:1.25rem">
  <div class="card-title">Media library files to improve (<?= count($mediaProblems) ?>)</div>
  <div class="card-desc">Big files slow pages down, which hurts rankings. Descriptive file names help Google Images. Rename files in <a class="row-link" href="media.php">Media</a>.</div>
  <?php if (!$mediaProblems): ?>
    <div class="seo-empty"><?= $media ? 'Every library image is light and well named. ✓' : 'The library is empty.' ?></div>
  <?php else: ?>
  <div class="table-wrap">
  <table>
    <thead><tr><th style="width:80px">Image</th><th>File</th><th>Size</th><th>What to fix</th></tr></thead>
    <tbody>
    <?php foreach (array_slice($mediaProblems, 0, 100) as $m): ?>
      <tr>
        <td><img class="seo-thumb" src="<?= h($m['url']) ?>" alt="" loading="lazy"></td>
        <td><a class="row-link seo-path" href="<?= h($m['url']) ?>" target="_blank"><?= h($m['file']) ?></a><div class="t-sub"><?= $m['w'] ? (int) $m['w'] . ' × ' . (int) $m['h'] . ' px' : '' ?></div></td>
        <td><?= h($m['size']) ?></td>
        <td><ul class="seo-issues"><?php foreach ($m['problems'] as $p): ?><li class="warning"><?= h($p) ?></li><?php endforeach; ?></ul></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php if (count($mediaProblems) > 100): ?><p class="t-sub" style="margin-top:.8rem">Showing the first 100 of <?= count($mediaProblems) ?>.</p><?php endif; ?>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
