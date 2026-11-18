<?php
/**
 * JSON endpoint behind the "Choose image" picker (admin/assets/media-picker.js).
 *   GET               → every library image: {images: [{file, url, w, h, size}]}
 *   POST (file=...)   → uploads one image into the library: {image: {...}} or {error}
 * Same rules as admin/media.php (includes/media-lib.php). Any logged-in user
 * may use it, as with the library page itself.
 */
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/media-lib.php';

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

function media_api_reply(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if (!current_user()) {
    media_api_reply(['error' => 'Your session has ended. Log in again and retry.'], 401);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bigger than post_max_size: PHP drops the whole body, CSRF token included.
    if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        media_api_reply(['error' => 'That image is larger than the server accepts (' . media_human_size(media_ini_bytes((string) ini_get('post_max_size'))) . ').'], 413);
    }
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', is_string($token) ? $token : '')) {
        media_api_reply(['error' => 'Your session expired. Reload the page and try again.'], 403);
    }
    $f = $_FILES['file'] ?? null;
    if (!is_array($f) || is_array($f['name'] ?? null)) {
        media_api_reply(['error' => 'Choose an image to upload.'], 400);
    }
    $saved = media_save_upload((string) $f['tmp_name'], (string) $f['name'], (int) $f['size'], (int) $f['error']);
    if (isset($saved['error'])) {
        media_api_reply(['error' => $saved['error']], 422);
    }
    if (!isset($saved['file'])) {
        media_api_reply(['error' => 'Choose an image to upload.'], 400);
    }
    $path = UPLOAD_DIR . 'media/' . $saved['file'];
    $dims = @getimagesize($path);
    media_api_reply(['image' => [
        'file' => $saved['file'],
        'url' => UPLOAD_URL . 'media/' . rawurlencode($saved['file']),
        'w' => $dims ? (int) $dims[0] : 0,
        'h' => $dims ? (int) $dims[1] : 0,
        'size' => media_human_size((int) filesize($path)),
    ]]);
}

$images = array_map(function ($i) {
    unset($i['mtime']);
    return $i;
}, media_library_images());
media_api_reply(['images' => $images, 'max_bytes' => media_max_file_bytes()]);
