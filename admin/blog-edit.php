<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_blogs_access($pdo);

$u = current_user();
$id = (int) ($_GET['id'] ?? 0);
$post = [
    'id' => 0, 'title' => '', 'slug' => '', 'meta_title' => '', 'meta_description' => '',
    'excerpt' => '', 'content' => '', 'featured_image' => '', 'featured_image_alt' => '',
    'status' => 'draft', 'scheduled_at' => null,
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
    $featuredImageAlt = trim($_POST['featured_image_alt'] ?? '');

    $statusInput = $_POST['status'] ?? 'draft';
    $status = in_array($statusInput, ['draft', 'published', 'scheduled'], true) ? $statusInput : 'draft';

    $scheduledAt = null;
    if ($status === 'scheduled') {
        $scheduledRaw = trim($_POST['scheduled_at'] ?? '');
        $ts = $scheduledRaw !== '' ? strtotime($scheduledRaw) : false;
        if ($ts === false) {
            $error = 'Pick a valid publish date and time for a scheduled post.';
        } else {
            $scheduledAt = date('Y-m-d H:i:s', $ts);
        }
    }

    if (!$error) {
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
            // Optional SEO-friendly filename — falls back to the post slug.
            // The real detected extension is always used, regardless of
            // what (if anything) the admin typed, and a short random
            // suffix guarantees uniqueness without ever overwriting an
            // unrelated file.
            $ext = $allowed[$mime];
            $customName = trim($_POST['image_filename'] ?? '');
            $base = $customName !== '' ? $customName : $slug;
            $base = strtolower(preg_replace('/[^a-z0-9-]+/i', '-', $base));
            $base = trim($base, '-') ?: 'image';
            $filename = $base . '-' . bin2hex(random_bytes(3)) . '.' . $ext;

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
                'UPDATE blogs SET title=?, slug=?, meta_title=?, meta_description=?, excerpt=?, content=?,
                 featured_image=?, featured_image_alt=?, status=?, scheduled_at=? WHERE id=?'
            )->execute([
                $title, $slug, $metaTitle, $metaDescription, $excerpt, $content,
                $featuredImage, $featuredImageAlt, $status, $scheduledAt, $id,
            ]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO blogs (title, slug, meta_title, meta_description, excerpt, content,
                 featured_image, featured_image_alt, status, scheduled_at, author_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $title, $slug, $metaTitle, $metaDescription, $excerpt, $content,
                $featuredImage, $featuredImageAlt, $status, $scheduledAt, $u['id'],
            ]);
            $id = (int) $pdo->lastInsertId();
        }

        if ($status === 'scheduled') {
            $success = 'Scheduled — this post will go live automatically at ' . date('M j, Y g:i A', strtotime($scheduledAt)) . '.';
        } elseif ($status === 'published') {
            $success = 'Published — this post is now live.';
        } else {
            $success = 'Draft saved.';
        }

        $post = [
            'id' => $id, 'title' => $title, 'slug' => $slug,
            'meta_title' => $metaTitle, 'meta_description' => $metaDescription,
            'excerpt' => $excerpt, 'content' => $content,
            'featured_image' => $featuredImage, 'featured_image_alt' => $featuredImageAlt,
            'status' => $status, 'scheduled_at' => $scheduledAt,
        ];
    }
}

$scheduledAtLocal = !empty($post['scheduled_at']) ? date('Y-m-d\TH:i', strtotime($post['scheduled_at'])) : '';

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
    <div class="card-desc">Used as the thumbnail on the blog listing page and the header image on the post itself.</div>
    <div class="field">
      <label>Image File</label>
      <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif" data-image-input>
      <div class="upload-preview">
        <img data-image-preview src="<?= h($post['featured_image'] ? UPLOAD_URL . $post['featured_image'] : '') ?>" style="<?= $post['featured_image'] ? '' : 'display:none' ?>">
      </div>
    </div>
    <div class="field">
      <label for="image_filename">Image Filename (optional)</label>
      <input type="text" id="image_filename" name="image_filename" placeholder="Auto-generated from the URL slug if left blank">
      <div class="field-hint">Only applies when uploading a new image above. Good for SEO — e.g. "drawlead-website-launch".</div>
    </div>
    <div class="field">
      <label for="featured_image_alt">Image Alt Text</label>
      <input type="text" id="featured_image_alt" name="featured_image_alt" maxlength="190" value="<?= h($post['featured_image_alt']) ?>" placeholder="Describe the image — helps accessibility and image search">
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
        <option value="scheduled" <?= $post['status'] === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
      </select>
    </div>
    <div class="field">
      <label for="scheduled_at">Publish Date &amp; Time</label>
      <input type="datetime-local" id="scheduled_at" name="scheduled_at" value="<?= h($scheduledAtLocal) ?>">
      <div class="field-hint">Only used when Status is Scheduled — the post goes live automatically at this date and time.</div>
    </div>
    <button type="submit" class="btn btn-primary">Save Post</button>
    <a href="blogs.php" class="btn btn-ghost">Back to Blogs</a>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
