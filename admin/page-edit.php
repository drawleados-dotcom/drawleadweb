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
$contentReady = migration_table_exists($pdo, 'page_content');
$rendered = null;

$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

// A request bigger than post_max_size arrives with $_POST and $_FILES empty —
// say so instead of failing the CSRF check with a confusing message.
if ($isPost && empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $tab = 'content';
    $contentError = 'That upload is larger than the server accepts. Use images under 5MB and publish again.';
    $isPost = false;
}

// ── Page Content tab: publish ──
if ($isPost && ($_POST['form'] ?? '') === 'content') {
    csrf_verify();
    $tab = 'content';

    if (!$contentReady) {
        $contentError = 'Page content editing needs migration 027. Run it from Migrations first.';
    } else {
        $rendered = pc_render_template($pdo, $page);
        if ($rendered === null) {
            $contentError = 'This page\'s template couldn\'t be loaded, so nothing was published.';
        } else {
            $result = pc_save_page($pdo, $id, pc_sections(pc_scan($rendered)), $_POST, $_FILES);
            if ($result['error'] !== '') {
                $contentError = $result['error'] . ' Nothing was published.';
            } else {
                if ($result['changed'] > 0) {
                    $pdo->prepare('UPDATE pages SET updated_at = NOW() WHERE id = ?')->execute([$id]);
                }
                header('Location: page-edit.php?id=' . $id . '&tab=content&published=' . (int) $result['changed']);
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

// ── Page Content tab: build the editor ──
$contentStatus = 'ok';
$contentSections = [];
$contentOverrides = [];
$sectionRows = [];
$looseRows = [];
$hiddenSections = [];
$editorData = ['blocks' => []];
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
            $sectionKeys = array_keys(array_filter($contentSections, fn ($s) => $s['type'] === 'section'));
            $layout = pc_layout($contentOverrides, $sectionKeys);
            $hiddenSections = $layout['hidden'];
            foreach ($layout['order'] as $k) {
                $sectionRows[] = $contentSections[$k];
            }
            $looseRows = array_values(array_filter($contentSections, fn ($s) => $s['type'] !== 'section'));
            foreach ($contentSections as $s) {
                foreach ($s['texts'] as $key => $t) {
                    if ($t['type'] === 'block' && !isset($editorData['blocks'][$key])) {
                        $editorData['blocks'][(string) $key] = [
                            'c' => array_map(fn ($c) => [$c['open'], $c['close']], $t['children']),
                            'br' => $t['br'],
                        ];
                    }
                }
            }
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

/** One section row in the editor's list, with its fields. */
function pc_admin_row(array $section, array $overrides, bool $sortable, bool $hidden, string $pageSlug): void
{
    $edited = pc_admin_edited_count($section, $overrides);
    $textCount = count($section['texts']);
    $imageCount = count($section['images']);
    $sub = $section['title'] !== ''
        ? $section['title']
        : ($textCount || $imageCount
            ? $textCount . ' text' . ($textCount === 1 ? '' : 's') . ($imageCount ? ' · ' . $imageCount . ' image' . ($imageCount === 1 ? '' : 's') : '')
            : 'Nothing to edit here');
    ?>
    <li class="pc-row<?= $hidden ? ' is-off' : '' ?>" data-key="<?= h($section['key']) ?>"<?= $sortable ? '' : ' data-fixed' ?>>
      <div class="pc-row-head">
        <?php if ($sortable): ?>
          <button type="button" class="pc-drag" aria-label="Move “<?= h($section['label']) ?>”: drag, or use the up and down arrow keys" title="Drag to reorder">
            <svg viewBox="0 0 12 18" aria-hidden="true"><circle cx="3" cy="3" r="1.6"/><circle cx="9" cy="3" r="1.6"/><circle cx="3" cy="9" r="1.6"/><circle cx="9" cy="9" r="1.6"/><circle cx="3" cy="15" r="1.6"/><circle cx="9" cy="15" r="1.6"/></svg>
          </button>
        <?php else: ?>
          <span class="pc-lock" title="Sits between sections, so it can't be moved or hidden">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
          </span>
        <?php endif; ?>
        <button type="button" class="pc-row-toggle" aria-expanded="false">
          <span class="pc-row-name"><?= h($section['label']) ?><?php if ($edited): ?> <span class="pc-edited"><?= $edited ?> edited</span><?php endif; ?><span class="pc-changed-dot" title="Unpublished changes"></span></span>
          <span class="pc-row-sub"><?= h(mb_strimwidth($sub, 0, 80, '…', 'UTF-8')) ?></span>
        </button>
        <?php if ($sortable): ?>
          <label class="pc-switch" title="Show this section on the live page">
            <input type="checkbox" data-pc-visible<?= $hidden ? '' : ' checked' ?> aria-label="Show “<?= h($section['label']) ?>” on the live page">
            <span aria-hidden="true"></span>
          </label>
        <?php endif; ?>
      </div>

      <div class="pc-row-body" hidden>
        <?php if (!$textCount && !$imageCount): ?>
          <p class="pc-empty">This section has no text or images to edit here<?= $sortable ? ', but you can still move or hide it' : '' ?>.</p>
        <?php endif; ?>

        <?php $imgNo = 0; foreach ($section['images'] as $hash => $img):
          $imgNo++;
          $hash = (string) $hash;
          $srcEdited = array_key_exists('i' . $hash, $overrides);
          $altEdited = array_key_exists('a' . $hash, $overrides);
          $currentSrc = $srcEdited ? $overrides['i' . $hash] : $img['src'];
          $currentAlt = $altEdited ? $overrides['a' . $hash] : $img['alt'];
          $fileName = str_starts_with($img['src'], 'data:') ? 'Embedded image' : basename(pc_image_path($img['src']));
        ?>
        <div class="pc-image" data-pc-item="img:<?= h($hash) ?>" data-pc-search="<?= h(mb_strtolower($img['alt'] . ' ' . $currentAlt . ' ' . $fileName, 'UTF-8')) ?>">
          <div class="pc-image-preview">
            <img src="<?= h($currentSrc) ?>" alt="" data-pc-thumb data-pc-src-saved="<?= h($currentSrc) ?>" data-pc-src-original="<?= h($img['src']) ?>">
          </div>
          <div class="pc-image-meta">
            <b>Image <?= $imgNo ?></b>
            <?php if ($srcEdited): ?><span class="pc-edited">Replaced</span><?php endif; ?>
            <?php if ($img['count'] > 1): ?><span class="pc-count">used <?= (int) $img['count'] ?>×</span><?php endif; ?>
            <span class="pc-file-name" data-pc-file-name><?= h($fileName) ?></span>
          </div>
          <label class="btn btn-ghost btn-sm pc-upload">
            Replace image
            <input type="file" name="pc_img[<?= h($hash) ?>]" accept="image/jpeg,image/png,image/webp,image/gif" data-pc-input data-pc-type="img-file" data-pc-key="<?= h($hash) ?>">
          </label>
          <input type="text" class="pc-input" name="pc_img_url[<?= h($hash) ?>]" value="" placeholder="…or paste an image URL from Admin → Media" aria-label="Image URL" data-pc-input data-pc-type="img-url" data-pc-key="<?= h($hash) ?>">
          <label class="pc-label" for="pca-<?= h($hash) ?>">Alt text <?php if ($altEdited): ?><span class="pc-edited">Edited</span><?php endif; ?></label>
          <input type="text" class="pc-input" id="pca-<?= h($hash) ?>" name="pc_alt[<?= h($hash) ?>]" maxlength="300" value="<?= h($currentAlt) ?>" placeholder="Describe the image" data-pc-input data-pc-type="img-alt" data-pc-key="<?= h($hash) ?>" data-pc-original="<?= h($img['alt']) ?>">
          <?php if ($srcEdited): ?>
            <label class="pc-check">
              <input type="checkbox" name="pc_img_reset[<?= h($hash) ?>]" value="1" data-pc-input data-pc-type="img-reset" data-pc-key="<?= h($hash) ?>">
              Restore the original image
            </label>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <?php foreach ($section['texts'] as $key => $item):
          $key = (string) $key;
          $isBlock = $item['type'] === 'block';
          $isEdited = array_key_exists($key, $overrides);
          $value = $isEdited ? $overrides[$key] : $item['text'];
          $long = $isBlock && $item['br'] !== null
              || mb_strlen($value, 'UTF-8') > 60 || mb_strlen($item['text'], 'UTF-8') > 60;
          $rows = max(2, min(8, substr_count($value, "\n") + (int) ceil(mb_strlen($value, 'UTF-8') / 42)));
        ?>
        <div class="pc-field<?= $isEdited ? ' is-edited' : '' ?>" data-pc-item="text:<?= h($key) ?>" data-pc-search="<?= h(mb_strtolower($item['text'] . ' ' . $value, 'UTF-8')) ?>">
          <label class="pc-label" for="pcf-<?= h($key) ?>">
            <?= h($item['kind']) ?><?php if (!$isBlock && $item['parts'] > 1): ?> · part <?= (int) $item['part'] ?> of <?= (int) $item['parts'] ?><?php endif; ?>
            <?php if ($item['hint'] !== ''): ?><code class="pc-hint"><?= h($item['hint']) ?></code><?php endif; ?>
            <?php if ($isEdited): ?><span class="pc-edited">Edited</span><?php endif; ?>
            <?php if ($item['count'] > 1): ?><span class="pc-count">appears <?= (int) $item['count'] ?>×</span><?php endif; ?>
          </label>
          <?php if ($long): ?>
            <textarea class="pc-input" id="pcf-<?= h($key) ?>" name="pc_text[<?= h($key) ?>]" rows="<?= $rows ?>" placeholder="<?= h($item['text']) ?>" data-pc-input data-pc-type="<?= $isBlock ? 'block' : 'text' ?>" data-pc-key="<?= h($key) ?>" data-pc-original="<?= h($item['text']) ?>"><?= h($value) ?></textarea>
          <?php else: ?>
            <input type="text" class="pc-input" id="pcf-<?= h($key) ?>" name="pc_text[<?= h($key) ?>]" value="<?= h($value) ?>" placeholder="<?= h($item['text']) ?>" data-pc-input data-pc-type="<?= $isBlock ? 'block' : 'text' ?>" data-pc-key="<?= h($key) ?>" data-pc-original="<?= h($item['text']) ?>">
          <?php endif; ?>
          <?php if ($isBlock && ($item['children'] || $item['br'] !== null)): ?>
            <div class="pc-help">
              <?php if ($item['children']): ?>Words in <b>[brackets]</b> keep the highlight style.<?php endif; ?>
              <?php if ($item['br'] !== null): ?>A new line is a line break.<?php endif; ?>
            </div>
          <?php endif; ?>
          <?php if ($isEdited): ?>
            <div class="pc-help">Original: “<?= h($item['text']) ?>”. Clear the field to restore it.</div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <?php if ($section['anchor'] !== ''): ?>
          <a class="pc-row-link" href="<?= h(($pageSlug === '/' ? '/' : $pageSlug) . '#' . $section['anchor']) ?>" target="_blank" rel="noopener">View this section on the live site ↗</a>
        <?php endif; ?>
      </div>
    </li>
    <?php
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
</div>

<?php if ($tab === 'content'): ?>

  <?php if ($contentError): ?><div class="alert alert-error"><?= h($contentError) ?></div><?php endif; ?>
  <?php if (isset($_GET['published'])): $publishedN = (int) $_GET['published']; ?>
    <div class="alert alert-success" data-pc-flash><?= $publishedN > 0 ? 'Published — the live page now shows your changes.' : 'Nothing had changed, so there was nothing to publish.' ?></div>
  <?php endif; ?>

  <?php if ($contentStatus === 'no-template'): ?>
    <div class="card">
      <div class="card-title">Content isn't editable here</div>
      <div class="card-desc" style="margin-bottom:0">This page is generated by code rather than a page template, so it has no section content to edit. Its URL, status and SEO are on the Settings &amp; SEO tab.</div>
    </div>
  <?php elseif ($contentStatus === 'migration'): ?>
    <div class="card">
      <div class="card-title">One-time setup needed</div>
      <div class="card-desc">Page content editing needs migration 027, which adds the table that stores your edits.</div>
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
      <div class="card-desc" style="margin-bottom:0">This page's template has no sections, text or images to edit.</div>
    </div>
  <?php else:
    $previewUrl = ($page['slug'] === '/' ? '/' : $page['slug']) . '?pc_preview=1';
    $liveLabel = (($page['status'] ?? 'published') === 'draft' ? 'Draft' : 'Live')
        . ' · last updated ' . date('M j, g:i A', strtotime((string) $page['updated_at']));
    $layoutJson = json_encode([
        'order' => array_column($sectionRows, 'key'),
        'hidden' => array_keys($hiddenSections),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  ?>
  <form id="pcEditor" class="pc-editor" method="post" enctype="multipart/form-data" action="page-edit.php?id=<?= (int) $id ?>&amp;tab=content" autocomplete="off" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="form" value="content">
    <input type="hidden" name="pc_layout" id="pcLayout" value="<?= h($layoutJson) ?>">

    <div class="pc-bar">
      <div class="pc-devices" role="group" aria-label="Preview size">
        <button type="button" class="pc-device is-active" data-pc-device="desktop" aria-pressed="true">Desktop</button>
        <button type="button" class="pc-device" data-pc-device="tablet" aria-pressed="false">Tablet</button>
        <button type="button" class="pc-device" data-pc-device="phone" aria-pressed="false">Phone</button>
      </div>
      <div class="pc-status<?= ($page['status'] ?? 'published') === 'draft' ? ' is-draft' : '' ?>" id="pcStatus">
        <span class="pc-dot" aria-hidden="true"></span><span data-pc-status-text><?= h($liveLabel) ?></span>
      </div>
      <div class="pc-bar-actions">
        <a class="btn btn-ghost btn-sm" href="<?= h($page['slug']) ?>" target="_blank" rel="noopener">View live ↗</a>
        <button type="button" class="btn btn-ghost btn-sm" id="pcDiscard" disabled>Discard changes</button>
        <button type="submit" class="btn btn-primary btn-sm" id="pcPublish" disabled>Publish</button>
      </div>
    </div>

    <div class="pc-split">
      <aside class="pc-panel" id="pcPanel" aria-label="Page sections">
        <p class="pc-panel-help">Click any text or image in the preview to edit it. Drag <b>⠿</b> to change the order (or focus it and use the arrow keys). Switch a section off to hide it on the live page. Nothing goes live until you <b>Publish</b>.</p>
        <input type="search" class="pc-search" id="pcSearch" placeholder="Find text on this page…" aria-label="Find text on this page">
        <p class="pc-search-empty" id="pcSearchEmpty" hidden>No text on this page matches that search.</p>

        <ol class="pc-list" id="pcList">
          <?php foreach ($sectionRows as $s) {
              pc_admin_row($s, $contentOverrides, true, isset($hiddenSections[$s['key']]), $page['slug']);
          } ?>
        </ol>

        <?php if ($looseRows): ?>
          <div class="pc-list-title">Between sections <span>fixed in place</span></div>
          <ol class="pc-list pc-list-fixed">
            <?php foreach ($looseRows as $s) {
                pc_admin_row($s, $contentOverrides, false, false, $page['slug']);
            } ?>
          </ol>
        <?php endif; ?>
      </aside>

      <div class="pc-stage" id="pcStage">
        <div class="pc-frame-wrap" id="pcFrameWrap">
          <iframe id="pcFrame" src="<?= h($previewUrl) ?>" title="Live preview of <?= h($page['name']) ?>"></iframe>
          <div class="pc-frame-loading" id="pcLoading">Loading preview…</div>
        </div>
      </div>
    </div>
  </form>
  <script type="application/json" id="pcData"><?= json_encode($editorData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
  <script src="<?= asset_url('/admin/assets/page-editor.js') ?>"></script>
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
