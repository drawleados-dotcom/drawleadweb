<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: whatsapp.php?tab=flow');
    exit;
}

csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
if ($id) {
    $pdo->prepare('DELETE FROM whatsapp_flow_steps WHERE id = ?')->execute([$id]);
}

header('Location: whatsapp.php?tab=flow');
exit;
