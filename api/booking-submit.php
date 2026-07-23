<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/mailer.php';
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

// Honeypot: a hidden field real visitors never fill in, but bots often do.
if (!empty($input['website'])) {
    echo json_encode(['success' => true, 'meeting' => ['date' => '', 'time' => '', 'name' => '']]);
    exit;
}

$date = trim((string) ($input['date'] ?? ''));
$time = trim((string) ($input['time'] ?? ''));
$submitted = is_array($input['fields'] ?? null) ? $input['fields'] : [];

if (!is_booking_slot_valid($pdo, $date, $time)) {
    echo json_encode(['success' => false, 'error' => 'That time slot is no longer available. Please pick another.']);
    exit;
}

$fields = get_booking_form_fields($pdo);
$fieldsById = [];
foreach ($fields as $f) {
    $fieldsById[(int) $f['id']] = $f;
}

$isVisible = function (array $f, array $seen = []) use (&$isVisible, $fieldsById, $submitted): bool {
    if (!$f['conditional_field_id'] || in_array((int) $f['id'], $seen, true)) {
        return true; // no condition, or a circular reference — fail open rather than recurse forever
    }
    $parent = $fieldsById[(int) $f['conditional_field_id']] ?? null;
    if (!$parent) {
        return true;
    }
    if (!$isVisible($parent, array_merge($seen, [(int) $f['id']]))) {
        return false;
    }
    $val = $submitted[$parent['field_key']] ?? null;
    if (is_array($val)) {
        return in_array((string) $f['conditional_value'], $val, true);
    }
    return (string) $val === (string) $f['conditional_value'];
};

$errors = [];
$cleanData = [];
$name = '';
$email = '';

foreach ($fields as $f) {
    $key = $f['field_key'];
    if (!$isVisible($f)) {
        continue;
    }

    if ($f['field_type'] === 'checkbox') {
        $val = $submitted[$key] ?? [];
        $val = is_array($val) ? array_values(array_filter(array_map('strval', $val))) : [];
        if ($f['is_required'] && empty($val)) {
            $errors[] = $f['label'] . ' is required.';
        }
        $cleanData[$key] = $val;
        continue;
    }

    $val = $submitted[$key] ?? '';
    $val = is_string($val) ? trim($val) : '';

    if ($f['is_required'] && $val === '') {
        $errors[] = $f['label'] . ' is required.';
    }
    if ($val !== '' && $f['field_type'] === 'email' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
        $errors[] = $f['label'] . ' must be a valid email address.';
    }
    if ($val !== '' && in_array($f['field_type'], ['select', 'radio'], true) && !empty($f['options']) && !in_array($val, $f['options'], true)) {
        $val = '';
    }

    $cleanData[$key] = $val;
    if ($f['field_role'] === 'name' && $val !== '') {
        $name = $val;
    }
    if ($f['field_role'] === 'email' && $val !== '') {
        $email = $val;
    }
}

if (!$errors && $email === '') {
    $errors[] = 'An email address is required to confirm your booking.';
}

if ($errors) {
    echo json_encode(['success' => false, 'error' => implode(' ', array_slice($errors, 0, 3))]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO bookings (booking_date, booking_time, form_data, name, email) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$date, $time . ':00', json_encode($cleanData), $name, $email]);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        echo json_encode(['success' => false, 'error' => 'That time slot was just booked by someone else. Please pick another.']);
        exit;
    }
    error_log('Booking insert failed: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Something went wrong saving your booking. Please try again.']);
    exit;
}

$prettyDate = date('l, F j, Y', strtotime($date));
$prettyTime = date('g:i A', strtotime($time));

// Best-effort emails — the booking is already saved regardless of these succeeding.
if ($email) {
    $bookerBody = '<div style="font-family:Arial,sans-serif;max-width:480px">'
        . '<h2 style="color:#111112">Your consultation is confirmed</h2>'
        . '<p>Hi ' . h($name ?: 'there') . ',</p>'
        . '<p>Thanks for booking a free consultation call with Drawlead. Here are your details:</p>'
        . '<p><strong>Date:</strong> ' . h($prettyDate) . '<br><strong>Time:</strong> ' . h($prettyTime) . ' (IST)</p>'
        . '<p>We will be in touch shortly with a meeting link. In the meantime, take a look at how we have helped other businesses grow:</p>'
        . '<p><a href="https://drawlead.com/#cases" style="color:#14855a;font-weight:bold">View Our Case Studies →</a></p>'
        . '<p style="color:#888;font-size:12px;margin-top:24px">— Team Drawlead</p>'
        . '</div>';
    send_email($email, $name, 'Your Drawlead consultation is confirmed', $bookerBody);
}

$notifyRows = get_booking_notification_emails($pdo);
if ($notifyRows) {
    $detailsHtml = '';
    foreach ($fields as $f) {
        if (!array_key_exists($f['field_key'], $cleanData)) {
            continue;
        }
        $v = $cleanData[$f['field_key']];
        $v = is_array($v) ? implode(', ', $v) : $v;
        if ($v === '') {
            continue;
        }
        $detailsHtml .= '<tr><td style="padding:4px 12px 4px 0;color:#888">' . h($f['label']) . '</td><td style="padding:4px 0"><strong>' . h($v) . '</strong></td></tr>';
    }
    $adminBody = '<div style="font-family:Arial,sans-serif;max-width:520px">'
        . '<h2 style="color:#111112">New Consultation Booking</h2>'
        . '<p><strong>' . h($prettyDate) . ' at ' . h($prettyTime) . ' (IST)</strong></p>'
        . '<table cellpadding="0" cellspacing="0">' . $detailsHtml . '</table>'
        . '</div>';
    foreach ($notifyRows as $row) {
        send_email($row['email'], '', 'New consultation booking — ' . $prettyDate, $adminBody);
    }
}

echo json_encode([
    'success' => true,
    'meeting' => ['date' => $prettyDate, 'time' => $prettyTime, 'name' => $name],
]);
