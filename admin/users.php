<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $role = ($_POST['role'] ?? 'editor') === 'admin' ? 'admin' : 'editor';

    if (mb_strlen($name) < 2) {
        $error = 'Please enter a name.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (mb_strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        $dup = $pdo->prepare('SELECT 1 FROM users WHERE email = ?');
        $dup->execute([$email]);
        if ($dup->fetchColumn()) {
            $error = 'A user with that email already exists.';
        }
    }

    if (!$error) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $hash, $role]);
        $success = 'User created. Set their page/blog access from the list below.';
    }
}

$users = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Users';
$pageSub = 'Create admin users and control which pages and blogs each one can manage.';
$activeNav = 'users';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<div class="card">
  <div class="card-title">Add New User</div>
  <div class="card-desc">Admins have full access to everything automatically. Editors only see what you grant them.</div>
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="field">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" required>
    </div>
    <div class="field">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div class="field">
      <label for="password">Temporary Password</label>
      <input type="password" id="password" name="password" required minlength="8">
      <div class="field-hint">At least 8 characters. Share this with them directly — have them change it later.</div>
    </div>
    <div class="field">
      <label for="role">Role</label>
      <select id="role" name="role">
        <option value="editor">Editor (limited, access granted per page/blog)</option>
        <option value="admin">Admin (full access to everything)</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Create User</button>
  </form>
</div>

<div class="card">
  <div class="card-title">All Users</div>
  <table>
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Added</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($users as $usr): ?>
      <tr>
        <td class="t-name"><?= h($usr['name']) ?></td>
        <td><?= h($usr['email']) ?></td>
        <td><span class="badge badge-<?= $usr['role'] === 'admin' ? 'admin' : 'editor' ?>"><?= h($usr['role']) ?></span></td>
        <td><?= h(date('M j, Y', strtotime($usr['created_at']))) ?></td>
        <td><a class="row-link" href="user-edit.php?id=<?= (int) $usr['id'] ?>">Manage Access →</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
