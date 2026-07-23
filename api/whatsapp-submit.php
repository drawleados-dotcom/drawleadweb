<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

$input = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}

$token = $input['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', is_string($token) ? $token : '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Your session expired — please refresh the page and try again.']);
    exit;
}

// Honeypot.
if (!empty($input['website'])) {
    echo json_encode(['success' => true]);
    exit;
}

$phone = trim((string) ($input['phone'] ?? ''));
$phoneDigits = preg_replace('/[^0-9+]/', '', $phone);

if (mb_strlen($phoneDigits) < 7) {
    echo json_encode(['success' => false, 'error' => 'Please enter a valid phone number.']);
    exit;
}

$rawAnswers = is_array($input['answers'] ?? null) ? $input['answers'] : [];
$knownSteps = $pdo->query('SELECT id, message FROM whatsapp_flow_steps')->fetchAll();
$knownMessages = array_column($knownSteps, 'message');

$answers = [];
foreach ($rawAnswers as $a) {
    if (!is_array($a)) {
        continue;
    }
    $q = is_string($a['question'] ?? null) ? trim($a['question']) : '';
    $ans = is_string($a['answer'] ?? null) ? trim($a['answer']) : '';
    if ($q === '' || $ans === '' || !in_array($q, $knownMessages, true)) {
        continue;
    }
    $answers[] = ['question' => $q, 'answer' => $ans];
}

$stmt = $pdo->prepare('INSERT INTO whatsapp_leads (answers, phone) VALUES (?, ?)');
$stmt->execute([json_encode($answers), $phone]);

echo json_encode(['success' => true]);
