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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
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

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="post" novalidate>
  <?= csrf_field() ?>

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

<?php include __DIR__ . '/includes/footer.php'; ?>
