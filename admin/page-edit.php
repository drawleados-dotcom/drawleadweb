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
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');

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
            'UPDATE pages SET name = ?, slug = ?, meta_title = ?, meta_description = ? WHERE id = ?'
        )->execute([$name, $slug, $metaTitle, $metaDescription, $id]);

        $success = 'Saved — the live site now reflects these changes.';
        $page['name'] = $name;
        $page['slug'] = $slug;
        $page['meta_title'] = $metaTitle;
        $page['meta_description'] = $metaDescription;
    }
}

$pageTitle = 'Edit Page';
$pageSub = $page['name'];
$activeNav = 'pages';
include __DIR__ . '/includes/header.php';
?>

<div class="card">
  <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

  <?php if ($page['slug'] === '/'): ?>
    <div class="access-note" style="margin-top:0;margin-bottom:1.4rem">This is your homepage. If you change its URL away from <code>/</code>, your domain root will no longer show this page unless the new URL is set to <code>/</code>.</div>
  <?php endif; ?>

  <form method="post" novalidate>
    <?= csrf_field() ?>
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
      <label for="meta_title">Meta Title</label>
      <input type="text" id="meta_title" name="meta_title" required maxlength="190" value="<?= h($page['meta_title']) ?>">
      <div class="field-hint">Shown as the browser tab title and the Google search result headline.</div>
    </div>
    <div class="field">
      <label for="meta_description">Meta Description</label>
      <textarea id="meta_description" name="meta_description" rows="3" maxlength="320"><?= h($page['meta_description']) ?></textarea>
      <div class="field-hint">Shown under the title in Google search results. Aim for 150–160 characters.</div>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="pages.php" class="btn btn-ghost">Cancel</a>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
