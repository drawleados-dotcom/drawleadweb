<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_blogs_access($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: blogs.php');
    exit;
}

csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare('SELECT featured_image FROM blogs WHERE id = ?');
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();

    $pdo->prepare('DELETE FROM blogs WHERE id = ?')->execute([$id]);

    if ($img && is_file(UPLOAD_DIR . $img)) {
        @unlink(UPLOAD_DIR . $img);
    }
}

header('Location: blogs.php');
exit;
