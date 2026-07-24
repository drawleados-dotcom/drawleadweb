<?php
/**
 * AJAX endpoint — removes a Departments/Services tag. Any case study
 * already tagged with it keeps that text in its own `services` column
 * (a plain comma-separated string, not a foreign key), so nothing else
 * breaks — the tag just stops being offered as a checkbox going forward.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_case_studies_access($pdo);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', is_string($token) ? $token : '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Your session expired — please reload the page and try again.']);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id) {
    $pdo->prepare('DELETE FROM case_study_services WHERE id = ?')->execute([$id]);
}

echo json_encode(['success' => true]);
