<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json');

$dateStr = $_GET['date'] ?? '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr) || strtotime($dateStr) === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date.']);
    exit;
}

$a = get_booking_availability($pdo);
$daysRaw = trim((string) $a['days_of_week']);
$allowedDays = $daysRaw === '' ? [] : array_map('intval', explode(',', $daysRaw));
$dayOfWeek = (int) date('w', strtotime($dateStr));
$today = date('Y-m-d');

if ($dateStr < (string) $a['range_start'] || $dateStr > (string) $a['range_end']) {
    echo json_encode(['slots' => [], 'reason' => 'out_of_range']);
    exit;
}
if (!in_array($dayOfWeek, $allowedDays, true)) {
    echo json_encode(['slots' => [], 'reason' => 'day_unavailable']);
    exit;
}
if ($dateStr < $today) {
    echo json_encode(['slots' => [], 'reason' => 'past_date']);
    exit;
}

$interval = max(5, (int) $a['slot_interval_minutes']);
$startTs = strtotime($dateStr . ' ' . $a['start_time']);
$endTs = strtotime($dateStr . ' ' . $a['end_time']);

// Require at least an hour's notice for same-day bookings.
$minTs = time() + 60 * 60;

$slots = [];
for ($t = $startTs; $t + $interval * 60 <= $endTs; $t += $interval * 60) {
    if ($t < $minTs) {
        continue;
    }
    $slots[] = date('H:i', $t);
}

$stmt = $pdo->prepare("SELECT booking_time FROM bookings WHERE booking_date = ? AND status = 'confirmed'");
$stmt->execute([$dateStr]);
$taken = array_map(fn ($r) => substr($r['booking_time'], 0, 5), $stmt->fetchAll());

$slots = array_values(array_diff($slots, $taken));

echo json_encode(['slots' => $slots]);
