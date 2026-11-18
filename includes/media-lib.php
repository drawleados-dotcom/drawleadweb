<?php
/**
 * Media library helpers shared by admin/media.php (the library page) and
 * admin/media-api.php (the "Choose image" picker used in other admin forms),
 * so both accept uploads under exactly the same rules.
 *
 * Library uploads live in uploads/media/; earlier uploads from Blogs, Case
 * Studies, Page Content, Popup and Sidebar sit loose in uploads/.
 */

// SVG is deliberately not accepted: it can carry script, and these files are
// served from the same origin as the admin panel.
function media_allowed_types(): array
{
    return [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'image/avif' => 'avif',
    ];
}

/** Every file the library writes matches this (uploads/media/). */
const MEDIA_NAME_PATTERN = '/^[a-z0-9][a-z0-9-]*\.(jpg|png|webp|gif|avif)$/';
/** Earlier uploads loose in uploads/: an image file name, no path, no leading dot. */
const MEDIA_LOOSE_NAME_PATTERN = '/^[^.\/\\\\][^\/\\\\]*\.(jpe?g|png|webp|gif|avif)$/i';

/** Converts a php.ini size like "64M" to bytes (0 = no limit). */
function media_ini_bytes(string $value): int
{
    $value = trim($value);
    if ($value === '') {
        return 0;
    }
    $bytes = (int) $value;
    switch (strtolower(substr($value, -1))) {
        case 'g':
            $bytes *= 1024;
            // no break
        case 'm':
            $bytes *= 1024;
            // no break
        case 'k':
            $bytes *= 1024;
    }
    return $bytes;
}

function media_human_size(int $bytes): string
{
    if ($bytes >= 1048576) {
        return rtrim(rtrim(number_format($bytes / 1048576, 1), '0'), '.') . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024) . ' KB';
    }
    return $bytes . ' B';
}

/** Per-image cap: 10MB, or lower if the server's own upload limit is lower. */
function media_max_file_bytes(): int
{
    $max = 10 * 1024 * 1024;
    $server = media_ini_bytes((string) ini_get('upload_max_filesize'));
    return $server > 0 ? min($max, $server) : $max;
}

/**
 * Saves one uploaded file into uploads/media/.
 * Returns ['file' => saved name] or ['error' => message], or [] when the
 * slot was simply left empty.
 */
function media_save_upload(string $tmp, string $originalName, int $size, int $err): array
{
    $maxBytes = media_max_file_bytes();
    $allowed = media_allowed_types();
    $dir = UPLOAD_DIR . 'media/';

    if ($err === UPLOAD_ERR_NO_FILE) {
        return [];
    }
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
        return ['error' => $originalName . ': larger than the ' . media_human_size($maxBytes) . ' limit.'];
    }
    if ($err !== UPLOAD_ERR_OK) {
        return ['error' => $originalName . ': upload failed (error ' . $err . '). Please try again.'];
    }
    $mime = @mime_content_type($tmp);
    if (!is_string($mime) || !isset($allowed[$mime])) {
        return ['error' => $originalName . ': not a supported image. Use JPG, PNG, WEBP, GIF, or AVIF.'];
    }
    if ($size > $maxBytes) {
        return ['error' => $originalName . ': larger than the ' . media_human_size($maxBytes) . ' limit.'];
    }
    if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
        return ['error' => 'Could not create the uploads/media folder on the server. Check that uploads/ is writable.'];
    }

    // Keep the original name (good for SEO), cleaned to a URL-safe slug. The
    // extension always comes from the detected file type, never from what the
    // file claimed to be, and a short random suffix means an upload never
    // overwrites an existing image.
    $base = strtolower(pathinfo($originalName, PATHINFO_FILENAME));
    $base = trim((string) preg_replace('/[^a-z0-9]+/', '-', $base), '-');
    $base = rtrim(substr($base, 0, 60), '-');
    if ($base === '') {
        $base = 'image';
    }
    do {
        $filename = $base . '-' . bin2hex(random_bytes(3)) . '.' . $allowed[$mime];
    } while (is_file($dir . $filename));

    if (!move_uploaded_file($tmp, $dir . $filename)) {
        return ['error' => $originalName . ': could not be saved on the server. Please try again.'];
    }
    @chmod($dir . $filename, 0644);
    return ['file' => $filename];
}

/** Every image in the library (both folders), newest first, for the picker. */
function media_library_images(): array
{
    $items = [];
    foreach (['' => UPLOAD_DIR, 'media/' => UPLOAD_DIR . 'media/'] as $sub => $dir) {
        if (!is_dir($dir)) {
            continue;
        }
        $pattern = $sub === 'media/' ? MEDIA_NAME_PATTERN : MEDIA_LOOSE_NAME_PATTERN;
        foreach (scandir($dir) ?: [] as $f) {
            if (!preg_match($pattern, $f) || !is_file($dir . $f)) {
                continue;
            }
            $dims = @getimagesize($dir . $f);
            $items[] = [
                'file' => $f,
                'url' => UPLOAD_URL . $sub . rawurlencode($f),
                'w' => $dims ? (int) $dims[0] : 0,
                'h' => $dims ? (int) $dims[1] : 0,
                'size' => media_human_size((int) filesize($dir . $f)),
                'mtime' => (int) filemtime($dir . $f),
            ];
        }
    }
    usort($items, fn ($a, $b) => [$b['mtime'], $a['file']] <=> [$a['mtime'], $b['file']]);
    return $items;
}

/**
 * True when $value is a path to an image that exists in the library
 * ("/uploads/x.jpg" or "/uploads/media/x.jpg") — the only values the
 * picker may hand back to a form.
 */
function media_is_library_path(string $value): bool
{
    if (!str_starts_with($value, UPLOAD_URL)) {
        return false;
    }
    $rest = rawurldecode(substr($value, strlen(UPLOAD_URL)));
    if (str_starts_with($rest, 'media/')) {
        $file = substr($rest, 6);
        return (bool) preg_match(MEDIA_NAME_PATTERN, $file) && is_file(UPLOAD_DIR . 'media/' . $file);
    }
    return (bool) preg_match(MEDIA_LOOSE_NAME_PATTERN, $rest) && is_file(UPLOAD_DIR . $rest);
}
