<?php
/**
 * AJAX endpoint — adds a new Departments/Services tag from the Case Study
 * edit screen. Returns JSON so the calling page can insert the new
 * checkbox into the DOM without a full reload, which would otherwise
 * discard whatever the admin is mid-typing in the rest of the form.
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

$name = trim((string) ($_POST['name'] ?? ''));
if ($name === '') {
    echo json_encode(['success' => false, 'error' => 'Enter a service name.']);
    exit;
}
if (mb_strlen($name) > 190) {
    $name = mb_substr($name, 0, 190);
}

$dup = $pdo->prepare('SELECT id, name FROM case_study_services WHERE LOWER(name) = LOWER(?)');
$dup->execute([$name]);
$existing = $dup->fetch();
if ($existing) {
    echo json_encode(['success' => true, 'service' => $existing]);
    exit;
}

$maxOrder = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), 0) FROM case_study_services')->fetchColumn();
$pdo->prepare('INSERT INTO case_study_services (name, sort_order) VALUES (?, ?)')->execute([$name, $maxOrder + 1]);

echo json_encode(['success' => true, 'service' => ['id' => (int) $pdo->lastInsertId(), 'name' => $name]]);
