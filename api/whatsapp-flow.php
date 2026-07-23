<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json');

$steps = $pdo->query('SELECT id, message, step_type, options FROM whatsapp_flow_steps ORDER BY step_order, id')->fetchAll();

$out = array_map(function ($s) {
    return [
        'id'      => (int) $s['id'],
        'message' => $s['message'],
        'type'    => $s['step_type'],
        'options' => $s['options'] ? json_decode($s['options'], true) : [],
    ];
}, $steps);

echo json_encode(['steps' => $out, 'csrf_token' => csrf_token()]);
