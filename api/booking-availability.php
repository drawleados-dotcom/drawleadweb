<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json');

$a = get_booking_availability($pdo);

$daysRaw = trim((string) $a['days_of_week']);
$days = $daysRaw === '' ? [] : array_values(array_unique(array_map('intval', explode(',', $daysRaw))));

echo json_encode([
    'days_of_week' => $days,
    'start_time'   => substr($a['start_time'], 0, 5),
    'end_time'     => substr($a['end_time'], 0, 5),
    'interval'     => (int) $a['slot_interval_minutes'],
    'range_start'  => $a['range_start'],
    'range_end'    => $a['range_end'],
    'csrf_token'   => csrf_token(),
]);
