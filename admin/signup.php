<?php
require_once __DIR__ . '/../includes/bootstrap.php';

// This page only ever creates the FIRST admin account, to bootstrap the
// system. Once at least one user exists, all further accounts must be
// created by a logged-in admin from the Users page — no open public signup.
$userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
if ($userCount > 0) {
    header('Location: login.php');
    exit;
}

$error = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm'] ?? '');

    if (mb_strlen($name) < 2) {
        $error = 'Please enter your name.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (mb_strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    }

    if (!$error) {
        // Re-check under the (unlikely) race of two simultaneous first-run signups.
        $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ($userCount > 0) {
            header('Location: login.php');
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'admin')"
        );
        $stmt->execute([$name, $email, $hash]);
        $uid = (int) $pdo->lastInsertId();

        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $uid, 'name' => $name, 'email' => $email, 'role' => 'admin'];
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Admin Account — Drawlead Admin</title>
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">Drawlead</div>
    <div class="auth-sub">Welcome — create the first admin account to get started</div>

    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>

    <form method="post" novalidate>
      <?= csrf_field() ?>
      <div class="field">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required autofocus value="<?= h($name) ?>">
      </div>
      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= h($email) ?>">
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="8">
        <div class="field-hint">At least 8 characters.</div>
      </div>
      <div class="field">
        <label for="confirm">Confirm Password</label>
        <input type="password" id="confirm" name="confirm" required minlength="8">
      </div>
      <button type="submit" class="btn btn-primary btn-block">Create Admin Account →</button>
    </form>
    <div class="auth-foot">This screen only appears once, to create the first account.<br>After this, new users are added from inside the admin panel.</div>
  </div>
</div>
</body>
</html>
