<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_case_studies_access($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: case-studies.php');
    exit;
}

csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare('SELECT desktop_image, mobile_image, result_image FROM case_studies WHERE id = ?');
    $stmt->execute([$id]);
    $images = $stmt->fetch();

    $pdo->prepare('DELETE FROM case_studies WHERE id = ?')->execute([$id]);

    if ($images) {
        foreach ([$images['desktop_image'], $images['mobile_image'], $images['result_image']] as $img) {
            if ($img && is_file(UPLOAD_DIR . $img)) {
                @unlink(UPLOAD_DIR . $img);
            }
        }
    }
}

header('Location: case-studies.php');
exit;
