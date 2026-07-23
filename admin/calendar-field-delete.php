<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: calendar.php?tab=form');
    exit;
}

csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
if ($id) {
    // Any field that used this one as its condition just goes back to "always show".
    $pdo->prepare('UPDATE booking_form_fields SET conditional_field_id = NULL, conditional_value = NULL WHERE conditional_field_id = ?')
        ->execute([$id]);
    $pdo->prepare('DELETE FROM booking_form_fields WHERE id = ?')->execute([$id]);
}

header('Location: calendar.php?tab=form');
exit;
