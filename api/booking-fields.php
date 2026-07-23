<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json');

$fields = get_booking_form_fields($pdo);

$out = array_map(function ($f) {
    return [
        'id'                   => (int) $f['id'],
        'key'                  => $f['field_key'],
        'label'                => $f['label'],
        'type'                 => $f['field_type'],
        'options'              => $f['options'],
        'placeholder'          => $f['placeholder'],
        'required'             => (bool) $f['is_required'],
        'conditional_field_id' => $f['conditional_field_id'] ? (int) $f['conditional_field_id'] : null,
        'conditional_value'    => $f['conditional_value'],
    ];
}, $fields);

echo json_encode(['fields' => $out]);
