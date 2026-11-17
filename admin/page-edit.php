<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$u = current_user();
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM pages WHERE id = ?');
$stmt->execute([$id]);
$page = $stmt->fetch();

if (!$page) {
    http_response_code(404);
    die('Page not found.');
}

if (!user_has_page_access($pdo, $u['id'], $u['role'], $id)) {
    http_response_code(403);
    die('You do not have access to edit this page. Ask an admin for access.');
}

$tab = ($_GET['tab'] ?? 'content') === 'settings' ? 'settings' : 'content';
$error = '';
$success = '';
$contentError = '';
$contentErrorSection = '';
$contentReady = migration_table_exists($pdo, 'page_content');
$rendered = null;

$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

// A request bigger than post_max_size arrives with $_POST and $_FILES empty —
// say so instead of failing the CSRF check with a confusing message.
if ($isPost && empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $tab = 'content';
    $contentError = 'That upload is larger than the server accepts. Use an image under 5MB.';
    $isPost = false;
}

// ── Page Content tab: save one section ──
if ($isPost && ($_POST['form'] ?? '') === 'content') {
    csrf_verify();
    $tab = 'content';
    $sectionKey = (string) ($_POST['section'] ?? '');
    $contentErrorSection = $sectionKey;

    if (!$contentReady) {
        $contentError = 'Page content editing needs migration 027. Run it from Migrations first.';
    } else {
        $rendered = pc_render_template($pdo, $page);
        $sectionsForSave = $rendered === null ? [] : pc_sections(pc_scan($rendered));
        if (!isset($sectionsForSave[$sectionKey])) {
            $contentError = 'That section is no longer on this page. Reload and try again.';
        } else {
            $result = pc_save_section($pdo, $id, $sectionsForSave[$sectionKey], $_POST, $_FILES);
            if ($result['error'] !== '') {
                $contentError = $result['error'];
            } else {
                if ($result['changed'] > 0) {
                    $pdo->prepare('UPDATE pages SET updated_at = NOW() WHERE id = ?')->execute([$id]);
                }
                header('Location: page-edit.php?id=' . $id . '&tab=content&saved=' . rawurlencode($sectionKey)
                    . '&n=' . (int) $result['changed'] . '#' . pc_dom_id($sectionKey));
                exit;
            }
        }
    }
} elseif ($isPost) {
    // ── Settings & SEO tab ──
    csrf_verify();
    $tab = 'settings';
    $name = trim($_POST['name'] ?? '');
    $rawSlug = trim($_POST['slug'] ?? '');
    $status = ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
    $showInMenu = isset($_POST['show_in_menu']) ? 1 : 0;
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $focusKeyword = trim($_POST['focus_keyword'] ?? '');
    $canonicalUrl = trim($_POST['canonical_url'] ?? '');
    $robotsIndex = ($_POST['robots_index'] ?? 'index') === 'noindex' ? 'noindex' : 'index';
    $robotsFollow = ($_POST['robots_follow'] ?? 'follow') === 'nofollow' ? 'nofollow' : 'follow';
    $ogTitle = trim($_POST['og_title'] ?? '');
    $ogDescription = trim($_POST['og_description'] ?? '');
    $ogImage = trim($_POST['og_image'] ?? '');

    // Normalize the URL: always starts with "/", lowercase, safe characters only.
    if ($rawSlug === '' || $rawSlug === '/') {
        $slug = '/';
    } else {
        $clean = strtolower(preg_replace('/[^a-z0-9\/-]+/i', '-', $rawSlug));
        $clean = preg_replace('/-+/', '-', $clean);
        $clean = trim($clean, '/-');
        $slug = '/' . $clean;
    }

    if ($name === '') {
        $error = 'Page name is required.';
    } elseif ($metaTitle === '') {
        $error = 'Meta title is required.';
    } elseif ($slug === '/' || strlen($slug) > 1) {
        $dup = $pdo->prepare('SELECT 1 FROM pages WHERE slug = ? AND id <> ?');
        $dup->execute([$slug, $id]);
        if ($dup->fetchColumn()) {
            $error = 'That URL is already used by another page.';
        }
        $dupBlog = $pdo->prepare('SELECT 1 FROM blogs WHERE CONCAT("/blog/", slug) = ?');
        $dupBlog->execute([$slug]);
        if (!$error && $dupBlog->fetchColumn()) {
            $error = 'That URL collides with a blog post URL.';
        }
    }

    if (!$error) {
        $pdo->prepare(
            'UPDATE pages SET name = ?, slug = ?, status = ?, show_in_menu = ?, meta_title = ?, meta_description = ?,
             focus_keyword = ?, canonical_url = ?, robots_index = ?, robots_follow = ?,
             og_title = ?, og_description = ?, og_image = ? WHERE id = ?'
        )->execute([
            $name, $slug, $status, $showInMenu, $metaTitle, $metaDescription,
            $focusKeyword, $canonicalUrl, $robotsIndex, $robotsFollow,
            $ogTitle, $ogDescription, $ogImage, $id,
        ]);

        $success = 'Saved — the live site now reflects these changes.';
        $page['name'] = $name;
        $page['slug'] = $slug;
        $page['status'] = $status;
        $page['show_in_menu'] = $showInMenu;
        $page['meta_title'] = $metaTitle;
        $page['meta_description'] = $metaDescription;
        $page['focus_keyword'] = $focusKeyword;
        $page['canonical_url'] = $canonicalUrl;
        $page['robots_index'] = $robotsIndex;
        $page['robots_follow'] = $robotsFollow;
        $page['og_title'] = $ogTitle;
        $page['og_description'] = $ogDescription;
        $page['og_image'] = $ogImage;
    }
}

// ── Page Content tab: build the section list ──
$contentStatus = 'ok';
$contentSections = [];
$contentOverrides = [];
$savedSection = (string) ($_GET['saved'] ?? '');
if ($tab === 'content') {
    if (pc_template_file($page) === null) {
        $contentStatus = 'no-template';
    } elseif (!$contentReady) {
        $contentStatus = 'migration';
    } else {
        $rendered = $rendered ?? pc_render_template($pdo, $page);
        if ($rendered === null) {
            $contentStatus = 'render-failed';
        } else {
            $contentSections = pc_sections(pc_scan($rendered));
            $contentOverrides = page_content_overrides($pdo, $id);
            if (!$contentSections) {
                $contentStatus = 'empty';
            }
        }
    }
}

/** How many items in a section currently differ from the template. */
function pc_admin_edited_count(array $section, array $overrides): int
{
    $n = 0;
    foreach (array_keys($section['texts']) as $key) {
        $n += array_key_exists((string) $key, $overrides) ? 1 : 0;
    }
    foreach (array_keys($section['images']) as $hash) {
        $n += array_key_exists('i' . $hash, $overrides) ? 1 : 0;
        $n += array_key_exists('a' . $hash, $overrides) ? 1 : 0;
    }
    return $n;
}

$seoRow = [
    'focus_keyword' => $page['focus_keyword'] ?? '',
    'meta_title' => $page['meta_title'] ?? '',
    'meta_description' => $page['meta_description'] ?? '',
    'canonical_url' => $page['canonical_url'] ?? '',
    'robots_index' => $page['robots_index'] ?? 'index',
    'robots_follow' => $page['robots_follow'] ?? 'follow',
    'og_title' => $page['og_title'] ?? '',
    'og_description' => $page['og_description'] ?? '',
    'og_image' => $page['og_image'] ?? '',
];
$seoPathPrefix = '';

$pageTitle = 'Edit Page';
$pageSub = $page['name'];
$activeNav = 'pages';
include __DIR__ . '/includes/header.php';
?>

<div class="page-tabs">
  <a class="page-tab<?= $tab === 'content' ? ' active' : '' ?>" href="page-edit.php?id=<?= (int) $id ?>&amp;tab=content">Page Content</a>
  <a class="page-tab<?= $tab === 'settings' ? ' active' : '' ?>" href="page-edit.php?id=<?= (int) $id ?>&amp;tab=settings">Settings &amp; SEO</a>
  <a class="page-tab-view" href="<?= h($page['slug']) ?>" target="_blank" rel="noopener">View this page ↗</a>
</div>

<?php if ($tab === 'content'): ?>

  <?php if ($contentError): ?><div class="alert alert-error"><?= h($contentError) ?></div><?php endif; ?>
  <?php if ($savedSection !== '' && isset($contentSections[$savedSection])):
    $savedN = (int) ($_GET['n'] ?? 0); ?>
    <div class="alert alert-success">
      <?= $savedN > 0
        ? 'Saved “' . h($contentSections[$savedSection]['label']) . '” — the live page now shows your changes.'
        : 'No changes to save in “' . h($contentSections[$savedSection]['label']) . '”.' ?>
    </div>
  <?php endif; ?>

  <?php if ($contentStatus === 'no-template'): ?>
    <div class="card">
      <div class="card-title">Content isn't editable here</div>
      <div class="card-desc" style="margin-bottom:0">This page is generated by code rather than a page template, so it has no section content to edit. Its URL, status and SEO are on the Settings &amp; SEO tab.</div>
    </div>
  <?php elseif ($contentStatus === 'migration'): ?>
    <div class="card">
      <div class="card-title">One-time setup needed</div>
      <div class="card-desc">Section-wise content editing needs migration 027, which adds the table that stores your edits.</div>
      <?php if ($u['role'] === 'admin'): ?>
        <a class="btn btn-primary" href="run-migrations.php">Go to Migrations</a>
      <?php else: ?>
        <div class="access-note" style="margin-top:0">Ask an admin to run migration 027 from Admin → Migrations.</div>
      <?php endif; ?>
    </div>
  <?php elseif ($contentStatus === 'render-failed'): ?>
    <div class="alert alert-error">This page's template couldn't be loaded for editing. The live page is unaffected.</div>
  <?php elseif ($contentStatus === 'empty'): ?>
    <div class="card">
      <div class="card-title">No editable content found</div>
      <div class="card-desc" style="margin-bottom:0">This page's template has no section text or images to edit.</div>
    </div>
  <?php else: ?>

    <div class="card pc-intro">
      <div class="pc-intro-row">
        <div>
          <div class="card-title">Edit content section by section</div>
          <div class="card-desc" style="margin-bottom:0">Open a section, change its text or images, then save that section. Clear a field to restore its original text. The site's menu and footer are shared by every page and aren't edited here.</div>
        </div>
        <div class="pc-intro-tools">
          <input type="search" id="pcSearch" class="pc-search" placeholder="Find text on this page…" aria-label="Find text on this page">
          <button type="button" class="btn btn-ghost btn-sm" data-pc-toggle-all>Expand all</button>
        </div>
      </div>
      <div class="pc-search-empty" id="pcSearchEmpty" hidden>No text on this page matches that search.</div>
    </div>

    <?php $sectionNo = 0; foreach ($contentSections as $sectionKey => $section):
      $sectionNo++;
      $domId = pc_dom_id($sectionKey);
      $edited = pc_admin_edited_count($section, $contentOverrides);
      $isOpen = $savedSection === $sectionKey || ($contentError && $contentErrorSection === $sectionKey) || count($contentSections) === 1;
      $textCount = count($section['texts']);
      $imageCount = count($section['images']);
    ?>
    <details class="pc-section" id="<?= h($domId) ?>"<?= $isOpen ? ' open' : '' ?>>
      <summary>
        <span class="pc-sec-num"><?= $sectionNo ?></span>
        <span class="pc-sec-head">
          <span class="pc-sec-name"><?= h($section['label']) ?></span>
          <?php if ($section['title'] !== ''): ?><span class="pc-sec-title"><?= h(mb_strimwidth($section['title'], 0, 90, '…', 'UTF-8')) ?></span><?php endif; ?>
        </span>
        <span class="pc-sec-meta">
          <?= $textCount ?> text<?= $textCount === 1 ? '' : 's' ?><?php if ($imageCount): ?> · <?= $imageCount ?> image<?= $imageCount === 1 ? '' : 's' ?><?php endif; ?>
          <?php if ($edited): ?><span class="badge badge-published"><?= $edited ?> edited</span><?php endif; ?>
        </span>
        <span class="pc-sec-chevron" aria-hidden="true"></span>
      </summary>

      <form method="post" enctype="multipart/form-data" action="page-edit.php?id=<?= (int) $id ?>&amp;tab=content" class="pc-form">
        <?= csrf_field() ?>
        <input type="hidden" name="form" value="content">
        <input type="hidden" name="section" value="<?= h($sectionKey) ?>">

        <?php if ($section['images']): ?>
        <div class="pc-group-label">Images</div>
        <div class="pc-images">
          <?php $imgNo = 0; foreach ($section['images'] as $hash => $img):
            $imgNo++;
            $srcEdited = array_key_exists('i' . $hash, $contentOverrides);
            $altEdited = array_key_exists('a' . $hash, $contentOverrides);
            $currentSrc = $srcEdited ? $contentOverrides['i' . $hash] : $img['src'];
            $currentAlt = $altEdited ? $contentOverrides['a' . $hash] : $img['alt'];
            $fileName = basename(pc_image_path($img['src']));
            $imgId = 'pci-' . $hash;
          ?>
          <div class="field pc-image pc-item" data-pc-text="<?= h($img['alt'] . ' ' . $currentAlt . ' ' . $fileName) ?>">
            <div class="pc-image-preview">
              <img data-image-preview src="<?= h($currentSrc) ?>" alt="">
            </div>
            <label for="<?= h($imgId) ?>">
              Image <?= $imgNo ?>
              <?php if ($srcEdited): ?><span class="pc-edited">Replaced</span><?php endif; ?>
              <?php if ($img['count'] > 1): ?><span class="pc-count">used <?= (int) $img['count'] ?>×</span><?php endif; ?>
            </label>
            <div class="pc-file-name"><?= h(str_starts_with($img['src'], 'data:') ? 'Embedded image' : $fileName) ?></div>
            <input type="file" id="<?= h($imgId) ?>" name="pc_img[<?= h($hash) ?>]" accept="image/jpeg,image/png,image/webp,image/gif" data-image-input>
            <div class="field-hint">JPG, PNG, WEBP or GIF, under 5MB. Use the same shape as the current image so the layout holds.</div>
            <label for="<?= h($imgId) ?>-url" class="pc-sub-label">Or image URL</label>
            <input type="text" id="<?= h($imgId) ?>-url" name="pc_img_url[<?= h($hash) ?>]" value="" placeholder="Paste a URL from Admin → Media" data-pc-img-url>
            <div class="field-hint">Leave the URL empty to keep the current image.</div>
            <label for="<?= h($imgId) ?>-alt" class="pc-sub-label">Alt text <?php if ($altEdited): ?><span class="pc-edited">Edited</span><?php endif; ?></label>
            <input type="text" id="<?= h($imgId) ?>-alt" name="pc_alt[<?= h($hash) ?>]" maxlength="300" value="<?= h($currentAlt) ?>" placeholder="Describe the image for screen readers and search engines">
            <?php if ($srcEdited): ?>
              <label class="checkbox-row pc-reset">
                <input type="checkbox" name="pc_img_reset[<?= h($hash) ?>]" value="1">
                Restore the original image
              </label>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($section['texts']): ?>
        <div class="pc-group-label">Text</div>
        <?php foreach ($section['texts'] as $key => $item):
          $key = (string) $key;
          $isEdited = array_key_exists($key, $contentOverrides);
          $value = $isEdited ? $contentOverrides[$key] : $item['text'];
          $fieldId = 'pcf-' . $key;
          $long = mb_strlen($value, 'UTF-8') > 90 || mb_strlen($item['text'], 'UTF-8') > 90;
        ?>
        <div class="field pc-field pc-item<?= $isEdited ? ' is-edited' : '' ?>" data-pc-text="<?= h($item['text'] . ' ' . $value) ?>">
          <label for="<?= h($fieldId) ?>">
            <?= h($item['kind']) ?><?php if ($item['parts'] > 1): ?> · part <?= (int) $item['part'] ?> of <?= (int) $item['parts'] ?><?php endif; ?>
            <?php if ($item['hint'] !== ''): ?><code class="pc-hint"><?= h($item['hint']) ?></code><?php endif; ?>
            <?php if ($isEdited): ?><span class="pc-edited">Edited</span><?php endif; ?>
            <?php if ($item['count'] > 1): ?><span class="pc-count">appears <?= (int) $item['count'] ?>×</span><?php endif; ?>
          </label>
          <?php if ($long): ?>
            <textarea id="<?= h($fieldId) ?>" name="pc_text[<?= h($key) ?>]" rows="<?= min(8, (int) ceil(mb_strlen($value, 'UTF-8') / 95) + 1) ?>" placeholder="<?= h($item['text']) ?>"><?= h($value) ?></textarea>
          <?php else: ?>
            <input type="text" id="<?= h($fieldId) ?>" name="pc_text[<?= h($key) ?>]" value="<?= h($value) ?>" placeholder="<?= h($item['text']) ?>">
          <?php endif; ?>
          <?php if ($isEdited): ?>
            <div class="field-hint">Original: “<?= h($item['text']) ?>”. Clear the field and save to restore it.</div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <div class="pc-actions">
          <button type="submit" class="btn btn-primary">Save “<?= h($section['label']) ?>”</button>
          <?php if ($section['anchor'] !== ''): ?>
            <a class="btn btn-ghost btn-sm" href="<?= h($page['slug'] . '#' . $section['anchor']) ?>" target="_blank" rel="noopener">View section on site ↗</a>
          <?php endif; ?>
        </div>
      </form>
    </details>
    <?php endforeach; ?>

  <?php endif; ?>

<?php else: ?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="post" action="page-edit.php?id=<?= (int) $id ?>&amp;tab=settings" novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="form" value="settings">

  <div class="card">
    <div class="card-title">Basics</div>
    <?php if ($page['slug'] === '/'): ?>
      <div class="access-note" style="margin-top:0;margin-bottom:1.4rem">This is your homepage. If you change its URL away from <code>/</code>, your domain root will no longer show this page unless the new URL is set to <code>/</code>.</div>
    <?php endif; ?>
    <?php if ($page['slug'] === '/analyze'): ?>
      <div class="access-note" style="margin-top:0;margin-bottom:1.4rem">This page powers the Drawlead Analyze tool. Its functionality lives in code at the fixed URL <code>/analyze</code> — if you change the URL here, the tool will stop working there (this row will just control an unused page). Status and meta title/description are safe to edit.</div>
    <?php endif; ?>
    <div class="field">
      <label for="name">Page Name</label>
      <input type="text" id="name" name="name" required value="<?= h($page['name']) ?>" data-slug-source>
      <div class="field-hint">Internal label shown in the admin — not shown to site visitors.</div>
    </div>
    <div class="field">
      <label for="slug">Page URL</label>
      <input type="text" id="slug" name="slug" required value="<?= h($page['slug']) ?>" data-slug-target>
      <div class="field-hint">e.g. <code>/about-us</code>. Visitors reach this page at yoursite.com<?= h($page['slug']) ?></div>
    </div>
    <div class="field">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="published" <?= ($page['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
        <option value="draft" <?= ($page['status'] ?? 'published') === 'draft' ? 'selected' : '' ?>>Draft</option>
      </select>
      <div class="field-hint">Draft pages are unpublished — visitors get a 404, and they drop out of the sitemap.</div>
    </div>
    <label class="checkbox-row" style="max-width:340px">
      <input type="checkbox" name="show_in_menu" <?= !empty($page['show_in_menu']) ? 'checked' : '' ?>>
      Show in main nav
    </label>
    <div class="field-hint">Only applies to Home, Home 2.0, Home 5, Final Home, Home 7, About Us, and Analyze — Platform, Industry, and Solution pages always use their own mega menus regardless of this setting.</div>
  </div>

  <?php include __DIR__ . '/includes/seo-panel.php'; ?>

  <div class="card">
    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="pages.php" class="btn btn-ghost">Cancel</a>
  </div>
</form>

<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
