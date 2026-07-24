<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);
$editUser = $stmt->fetch();

if (!$editUser) {
    http_response_code(404);
    die('User not found.');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $role = ($_POST['role'] ?? 'editor') === 'admin' ? 'admin' : 'editor';
    $pageIds = array_map('intval', $_POST['pages'] ?? []);
    $blogsAccess = isset($_POST['blogs_access']);
    $caseStudiesAccess = isset($_POST['case_studies_access']);

    // Don't allow demoting the very last admin — that would lock everyone out of Users.
    if ($editUser['role'] === 'admin' && $role === 'editor') {
        $adminCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
        if ($adminCount <= 1) {
            $error = 'You cannot demote the last remaining admin.';
        }
    }

    if (!$error) {
        $pdo->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$role, $id]);

        $pdo->prepare('DELETE FROM user_access WHERE user_id = ?')->execute([$id]);
        $insert = $pdo->prepare('INSERT INTO user_access (user_id, item_type, item_id) VALUES (?, ?, ?)');
        foreach ($pageIds as $pid) {
            $insert->execute([$id, 'page', $pid]);
        }
        if ($blogsAccess) {
            $insert->execute([$id, 'blogs', 0]);
        }
        if ($caseStudiesAccess) {
            $insert->execute([$id, 'case_studies', 0]);
        }

        $success = 'Access updated.';
        $editUser['role'] = $role;
    }
}

$pages = $pdo->query('SELECT id, name FROM pages ORDER BY id')->fetchAll();
$accessStmt = $pdo->prepare('SELECT item_type, item_id FROM user_access WHERE user_id = ?');
$accessStmt->execute([$id]);
$access = $accessStmt->fetchAll();
$grantedPages = array_column(array_filter($access, fn ($a) => $a['item_type'] === 'page'), 'item_id');
$hasBlogs = (bool) array_filter($access, fn ($a) => $a['item_type'] === 'blogs');
$hasCaseStudies = (bool) array_filter($access, fn ($a) => $a['item_type'] === 'case_studies');

$pageTitle = 'Manage Access';
$pageSub = $editUser['name'] . ' — ' . $editUser['email'];
$activeNav = 'users';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="post">
  <?= csrf_field() ?>

  <div class="card">
    <div class="card-title">Role</div>
    <div class="field">
      <select name="role">
        <option value="editor" <?= $editUser['role'] === 'editor' ? 'selected' : '' ?>>Editor (limited, access granted below)</option>
        <option value="admin" <?= $editUser['role'] === 'admin' ? 'selected' : '' ?>>Admin (full access to everything)</option>
      </select>
      <div class="field-hint">Admins bypass the checkboxes below and always have full access.</div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Page Access</div>
    <div class="card-desc">Which pages can this user edit the meta title, description, name, and URL for?</div>
    <div class="checkbox-grid">
      <?php foreach ($pages as $p): ?>
      <label class="checkbox-row">
        <input type="checkbox" name="pages[]" value="<?= (int) $p['id'] ?>" <?= in_array((int) $p['id'], $grantedPages, true) ? 'checked' : '' ?>>
        <?= h($p['name']) ?>
      </label>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Blogs Access</div>
    <div class="card-desc">Can this user write, edit, and delete blog posts?</div>
    <label class="checkbox-row" style="max-width:280px">
      <input type="checkbox" name="blogs_access" <?= $hasBlogs ? 'checked' : '' ?>>
      Access to Blogs module
    </label>
  </div>

  <div class="card">
    <div class="card-title">Case Studies Access</div>
    <div class="card-desc">Can this user write, edit, and delete case studies?</div>
    <label class="checkbox-row" style="max-width:280px">
      <input type="checkbox" name="case_studies_access" <?= $hasCaseStudies ? 'checked' : '' ?>>
      Access to Case Studies module
    </label>
  </div>

  <div class="card">
    <button type="submit" class="btn btn-primary">Save Access</button>
    <a href="users.php" class="btn btn-ghost">Back to Users</a>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
