<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json');

$steps = $pdo->query('SELECT id, message, step_type, options FROM whatsapp_flow_steps ORDER BY step_order, id')->fetchAll();

// The original seeded service choices. While a step still has exactly this
// list, "AI & Automation" is offered as a fourth choice; once the options are
// edited in Admin → WhatsApp, the saved list is used as-is.
$defaultServiceOptions = ['Custom ERP Solution / Software', 'Ecommerce Solutions', 'Marketing Solutions'];

$out = array_map(function ($s) use ($defaultServiceOptions) {
    $options = $s['options'] ? json_decode($s['options'], true) : [];
    if (is_array($options) && $options === $defaultServiceOptions) {
        array_splice($options, 2, 0, ['AI & Automation']);
    }
    return [
        'id'      => (int) $s['id'],
        'message' => $s['message'],
        'type'    => $s['step_type'],
        'options' => $options,
    ];
}, $steps);

echo json_encode(['steps' => $out, 'csrf_token' => csrf_token()]);
