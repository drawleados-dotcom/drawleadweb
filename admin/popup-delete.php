<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !popups_ready($pdo)) {
    header('Location: popup.php');
    exit;
}

csrf_verify();

// The image file stays in uploads/ — it's in the Media library, where it
// can be reused or deleted separately.
$id = (int) ($_POST['id'] ?? 0);
if ($id) {
    $pdo->prepare('DELETE FROM popups WHERE id = ?')->execute([$id]);
}

header('Location: popup.php?deleted=1');
exit;
