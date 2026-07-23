<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_blogs_access($pdo);

$u = current_user();
$id = (int) ($_GET['id'] ?? 0);
$post = [
    'id' => 0, 'title' => '', 'slug' => '', 'meta_title' => '', 'meta_description' => '',
    'excerpt' => '', 'content' => '', 'featured_image' => '', 'status' => 'draft',
];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM blogs WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        http_response_code(404);
        die('Post not found.');
    }
    $post = $found;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $title = trim($_POST['title'] ?? '');
    $rawSlug = trim($_POST['slug'] ?? '');
    $slug = $rawSlug !== '' ? slugify($rawSlug) : slugify($title);
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = sanitize_blog_html($_POST['content'] ?? '');
    $status = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';

    if ($title === '') {
        $error = 'Title is required.';
    } elseif ($slug === '') {
        $error = 'URL slug is required.';
    } else {
        $dup = $pdo->prepare('SELECT 1 FROM blogs WHERE slug = ? AND id <> ?');
        $dup->execute([$slug, $id]);
        if ($dup->fetchColumn()) {
            $error = 'That URL is already used by another post.';
        }
        $dupPage = $pdo->prepare('SELECT 1 FROM pages WHERE slug = ?');
        $dupPage->execute(['/blog/' . $slug]);
        if (!$error && $dupPage->fetchColumn()) {
            $error = 'That URL collides with an existing page.';
        }
    }

    // Featured image upload (optional).
    $featuredImage = $post['featured_image'];
    if (!$error && !empty($_FILES['featured_image']['tmp_name']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $tmp = $_FILES['featured_image']['tmp_name'];
        $mime = @mime_content_type($tmp);
        $size = (int) $_FILES['featured_image']['size'];

        if (!isset($allowed[$mime])) {
            $error = 'Featured image must be a JPG, PNG, WEBP, or GIF file.';
        } elseif ($size > 5 * 1024 * 1024) {
            $error = 'Featured image must be smaller than 5MB.';
        } else {
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }
            $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
            if (!move_uploaded_file($tmp, UPLOAD_DIR . $filename)) {
                $error = 'Could not save the uploaded image. Please try again.';
            } else {
                $featuredImage = $filename;
            }
        }
    }

    if (!$error) {
        if ($id) {
            $pdo->prepare(
                'UPDATE blogs SET title=?, slug=?, meta_title=?, meta_description=?, excerpt=?, content=?, featured_image=?, status=? WHERE id=?'
            )->execute([$title, $slug, $metaTitle, $metaDescription, $excerpt, $content, $featuredImage, $status, $id]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO blogs (title, slug, meta_title, meta_description, excerpt, content, featured_image, status, author_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$title, $slug, $metaTitle, $metaDescription, $excerpt, $content, $featuredImage, $status, $u['id']]);
            $id = (int) $pdo->lastInsertId();
        }

        $success = $status === 'published' ? 'Published — this post is now live.' : 'Draft saved.';
        $post = [
            'id' => $id, 'title' => $title, 'slug' => $slug,
            'meta_title' => $metaTitle, 'meta_description' => $metaDescription,
            'excerpt' => $excerpt, 'content' => $content,
            'featured_image' => $featuredImage, 'status' => $status,
        ];
    }
}

$pageTitle = $id ? 'Edit Post' : 'New Post';
$pageSub = $post['title'] ?: 'Untitled';
$activeNav = 'blogs';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="card">
    <div class="field">
      <label for="title">Post Title</label>
      <input type="text" id="title" name="title" required value="<?= h($post['title']) ?>" data-slug-source>
    </div>
    <div class="field">
      <label for="slug">URL Slug</label>
      <input type="text" id="slug" name="slug" required value="<?= h($post['slug']) ?>" data-slug-target>
      <div class="field-hint">Published at yoursite.com/blog/<?= h($post['slug'] ?: 'your-slug') ?></div>
    </div>
    <div class="field">
      <label for="excerpt">Excerpt</label>
      <textarea id="excerpt" name="excerpt" rows="2" maxlength="400"><?= h($post['excerpt']) ?></textarea>
      <div class="field-hint">Short summary shown on the blog listing page.</div>
    </div>
    <div class="field">
      <label>Content</label>
      <div class="rte">
        <div class="rte-toolbar">
          <button type="button" data-cmd="bold"><b>B</b></button>
          <button type="button" data-cmd="italic"><i>I</i></button>
          <button type="button" data-cmd="underline"><u>U</u></button>
          <button type="button" data-cmd="formatBlock" data-value="h2">H2</button>
          <button type="button" data-cmd="formatBlock" data-value="h3">H3</button>
          <button type="button" data-cmd="formatBlock" data-value="p">¶</button>
          <button type="button" data-cmd="insertUnorderedList">• List</button>
          <button type="button" data-cmd="insertOrderedList">1. List</button>
          <button type="button" data-cmd="formatBlock" data-value="blockquote">" Quote</button>
          <button type="button" data-cmd="createLink">🔗 Link</button>
          <button type="button" data-cmd="insertImage">🖼 Image</button>
          <button type="button" data-cmd="removeFormat">Clear</button>
        </div>
        <div class="rte-body" contenteditable="true"></div>
        <textarea name="content" style="display:none"><?= h($post['content']) ?></textarea>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Featured Image</div>
    <div class="field">
      <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif" data-image-input>
      <div class="upload-preview">
        <img data-image-preview src="<?= h($post['featured_image'] ? UPLOAD_URL . $post['featured_image'] : '') ?>" style="<?= $post['featured_image'] ? '' : 'display:none' ?>">
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">SEO</div>
    <div class="field">
      <label for="meta_title">Meta Title</label>
      <input type="text" id="meta_title" name="meta_title" maxlength="190" value="<?= h($post['meta_title']) ?>" placeholder="Defaults to the post title if left blank">
    </div>
    <div class="field">
      <label for="meta_description">Meta Description</label>
      <textarea id="meta_description" name="meta_description" rows="2" maxlength="320" placeholder="Defaults to the excerpt if left blank"><?= h($post['meta_description']) ?></textarea>
    </div>
  </div>

  <div class="card">
    <div class="field">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Save Post</button>
    <a href="blogs.php" class="btn btn-ghost">Back to Blogs</a>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
