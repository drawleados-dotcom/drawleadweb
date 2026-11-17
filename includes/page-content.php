<?php
/**
 * Section-wise page content editing (Admin → Pages → Edit → Page Content).
 *
 * Page copy lives in the PHP templates (templates/{template}-body.php), not
 * the database. Rather than rewrite every template around content fields,
 * this works on the HTML a template renders:
 *
 *   1. pc_scan() walks the rendered HTML and finds each top-level <section>
 *      (plus the loose content between sections, like marquees), and inside
 *      each one every visible text run and every <img>.
 *   2. The admin lists those per section as form fields. Saving stores only
 *      what differs from the template in the page_content table.
 *   3. When the page is served, page_content_apply() re-scans the template's
 *      output and splices the saved text and images in. All other markup is
 *      left byte-for-byte as the template wrote it.
 *
 * Each item is keyed by its section plus a hash of its original text (or, for
 * an image, its original file path). If a developer later changes that text in
 * the template, the saved edit simply stops matching and the new template copy
 * shows. Edits never land on the wrong element. Identical text repeated within
 * one section (e.g. a marquee duplicated for a seamless loop) is one field, so
 * every copy stays in step.
 *
 * Never edited here: the shared nav (<nav> is skipped) and footer (rendered by
 * layout-end.php, outside the template), <script>/<style>/<svg> contents, and
 * text that JavaScript builds at runtime.
 */

/** Elements whose contents are raw text up to their closing tag. */
const PC_RAW_TEXT_TAGS = ['script', 'style', 'textarea', 'title', 'xmp', 'iframe', 'noembed', 'noframes'];

/** Elements skipped wholesale, nesting-aware: shared nav, graphics, inert markup. */
const PC_SKIP_TAGS = ['nav', 'svg', 'math', 'template', 'noscript'];

const PC_VOID_TAGS = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'];

/** Elements that best describe a text run, nearest first. */
const PC_LABEL_TAGS = [
    'h1' => 'Main heading', 'h2' => 'Heading', 'h3' => 'Subheading', 'h4' => 'Subheading',
    'h5' => 'Subheading', 'h6' => 'Subheading', 'p' => 'Paragraph', 'li' => 'List item',
    'a' => 'Link text', 'button' => 'Button text', 'label' => 'Label', 'blockquote' => 'Quote',
    'figcaption' => 'Caption', 'td' => 'Table cell', 'th' => 'Table heading', 'dt' => 'Term',
    'dd' => 'Description', 'summary' => 'Summary', 'option' => 'Option',
];

const PC_IMAGE_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
const PC_IMAGE_MAX_BYTES = 5 * 1024 * 1024;

function pc_is_alpha(string $c): bool
{
    return ($c >= 'a' && $c <= 'z') || ($c >= 'A' && $c <= 'Z');
}

/** Collapse whitespace and trim, the way a browser displays a text run. */
function pc_normalize(string $s): string
{
    $collapsed = preg_replace('/\s+/u', ' ', $s);
    return trim($collapsed ?? preg_replace('/\s+/', ' ', $s));
}

/** "about-drawlead" → "About Drawlead". */
function pc_humanize(string $id): string
{
    $words = trim(preg_replace('/[-_]+/', ' ', $id));
    return $words === '' ? '' : ucwords($words);
}

/**
 * Index of the ">" closing the tag that starts at $from. Quotes only count when
 * they open an attribute value (follow "="), so a stray apostrophe can't make
 * the tag swallow the rest of the page.
 */
function pc_tag_end(string $html, int $from, int $len): int
{
    $i = $from;
    while ($i < $len) {
        $i += strcspn($html, "\"'>", $i);
        if ($i >= $len) {
            break;
        }
        $c = $html[$i];
        if ($c === '>') {
            return $i;
        }
        $j = $i - 1;
        while ($j > $from && strpos(" \t\n\r\f", $html[$j]) !== false) {
            $j--;
        }
        if ($html[$j] === '=') {
            $close = strpos($html, $c, $i + 1);
            if ($close === false) {
                return $len - 1;
            }
            $i = $close + 1;
        } else {
            $i++;
        }
    }
    return $len - 1;
}

/**
 * Attributes of one start tag, in order: name (lowercase), decoded value (null
 * for a bare attribute), and byte offsets of the whole "name=value" within $tag.
 */
function pc_parse_attrs(string $tag): array
{
    $attrs = [];
    if (!preg_match('/^<[a-zA-Z][^\s\/>]*/', $tag, $m)) {
        return $attrs;
    }
    $pos = strlen($m[0]);
    $len = strlen($tag);
    while ($pos < $len) {
        $pos += strspn($tag, " \t\n\r\f/", $pos);
        if ($pos >= $len || $tag[$pos] === '>') {
            break;
        }
        if (!preg_match('/\G([^\s"\'>\/=]+)(?:\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+))?/', $tag, $am, 0, $pos)) {
            $pos++;
            continue;
        }
        $value = null;
        if (isset($am[2]) && $am[2] !== '') {
            $raw = $am[2];
            if ($raw[0] === '"' || $raw[0] === "'") {
                $raw = substr($raw, 1, -1);
            }
            $value = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        $attrs[] = ['name' => strtolower($am[1]), 'value' => $value, 'start' => $pos, 'end' => $pos + strlen($am[0])];
        $pos += strlen($am[0]);
    }
    return $attrs;
}

function pc_attr(array $attrs, string $name): ?string
{
    foreach ($attrs as $a) {
        if ($a['name'] === $name) {
            return $a['value'] ?? '';
        }
    }
    return null;
}

/** Image identity for matching: its path without the ?v= cache-buster asset_url() adds. */
function pc_image_path(string $src): string
{
    return str_starts_with($src, 'data:') ? $src : (string) preg_replace('/[?#].*$/s', '', $src);
}

function pc_text_hash(string $groupKey, string $text): string
{
    return substr(md5($groupKey . "\ntext\n" . $text), 0, 16);
}

function pc_image_hash(string $groupKey, string $path): string
{
    return substr(md5($groupKey . "\nimg\n" . $path), 0, 16);
}

/**
 * Walk rendered template HTML and return every editable text run and image,
 * grouped by section, with byte offsets for splicing.
 *
 * @return array{groups: array<string, array>, texts: list<array>, images: list<array>}
 */
function pc_scan(string $html): array
{
    $len = strlen($html);
    $groups = [];
    $texts = [];
    $images = [];

    $stack = [];          // open elements: ['name', 'class', 'seq']
    $seq = 0;
    $ownerParts = [];     // element seq => number of text runs inside it
    $sectionDepth = 0;
    $sectionCount = 0;
    $groupKey = null;     // null = loose content not yet given a group
    $lastSection = null;  // ['key', 'label'] of the most recent closed section

    $pos = 0;
    $textStart = 0;

    while (true) {
        $lt = $pos < $len ? strpos($html, '<', $pos) : false;
        $at = $lt === false ? $len : $lt;

        if ($lt !== false) {
            $next = $html[$lt + 1] ?? '';
            $isMarkup = pc_is_alpha($next) || $next === '!' || $next === '?'
                || ($next === '/' && pc_is_alpha($html[$lt + 2] ?? ''));
            if (!$isMarkup) {
                // A literal "<" in text — keep reading the same text run.
                $pos = $lt + 1;
                continue;
            }
        }

        // ── Text run between the previous markup and this one ──
        if ($at > $textStart) {
            $raw = substr($html, $textStart, $at - $textStart);
            if (strspn($raw, " \t\n\r\f") !== strlen($raw)) {
                $decoded = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $text = pc_normalize($decoded);
                // Needs a letter or digit (skips "→", "•"), and a lone letter is
                // one glyph of a letter-by-letter animation, not editable copy.
                if ($text !== '' && preg_match('/[\p{L}\p{N}]/u', $text) && !preg_match('/^\p{L}$/u', $text)) {
                    if ($groupKey === null) {
                        $groupKey = $lastSection ? 'after:' . $lastSection['key'] : 'top';
                        if (!isset($groups[$groupKey])) {
                            $groups[$groupKey] = [
                                'key' => $groupKey,
                                'type' => 'loose',
                                'label' => $lastSection ? 'Content after ' . $lastSection['label'] : 'Top of page',
                                'anchor' => '',
                                'title' => '',
                                'title_seq' => null,
                                'title_done' => true,
                            ];
                        }
                    }

                    // Describe the run by its nearest meaningful element. A heading
                    // styled in pieces ("One Custom <span>OS</span> with …") gives
                    // several runs with the same owner, labelled "part N of M".
                    $kind = 'Text';
                    $hint = '';
                    $headingSeq = null;
                    $ownerSeq = null;
                    for ($i = count($stack) - 1; $i >= 0; $i--) {
                        $el = $stack[$i];
                        if ($headingSeq === null && in_array($el['name'], ['h1', 'h2', 'h3'], true)) {
                            $headingSeq = $el['seq'];
                        }
                        if ($ownerSeq === null && array_key_exists($el['name'], PC_LABEL_TAGS)) {
                            $kind = PC_LABEL_TAGS[$el['name']];
                            $ownerSeq = $el['seq'];
                        }
                    }
                    if ($stack) {
                        $parent = $stack[count($stack) - 1];
                        $hint = $parent['name'] . ($parent['class'] !== '' ? '.' . preg_split('/\s+/', $parent['class'])[0] : '');
                        $ownerSeq = $ownerSeq ?? $parent['seq'];
                    }
                    $part = 1;
                    if ($ownerSeq !== null) {
                        $part = $ownerParts[$ownerSeq] = ($ownerParts[$ownerSeq] ?? 0) + 1;
                    }

                    // The section's first h1–h3 doubles as its subtitle in the admin.
                    $g = &$groups[$groupKey];
                    if (!$g['title_done']) {
                        if ($headingSeq !== null && ($g['title_seq'] === null || $g['title_seq'] === $headingSeq)) {
                            $g['title'] = trim($g['title'] . ' ' . $text);
                            $g['title_seq'] = $headingSeq;
                        } elseif ($g['title_seq'] !== null) {
                            $g['title_done'] = true;
                        }
                    }
                    unset($g);

                    $texts[] = [
                        'key' => 't' . pc_text_hash($groupKey, $text),
                        'group' => $groupKey,
                        'start' => $textStart,
                        'length' => $at - $textStart,
                        // surrounding whitespace is kept so inline spacing ("Build <em>smarter</em>") survives an edit
                        'lead' => substr($raw, 0, strlen($raw) - strlen(ltrim($raw))),
                        'trail' => substr($raw, strlen(rtrim($raw))),
                        'text' => $text,
                        'kind' => $kind,
                        'hint' => $hint,
                        'owner' => $ownerSeq,
                        'part' => $part,
                    ];
                }
            }
        }

        if ($lt === false) {
            break;
        }

        // ── Markup at $lt ──
        if (substr_compare($html, '<!--', $lt, 4) === 0) {
            $end = strpos($html, '-->', $lt + 4);
            $pos = $end === false ? $len : $end + 3;
        } elseif ($html[$lt + 1] === '!' || $html[$lt + 1] === '?') {
            $end = strpos($html, '>', $lt);
            $pos = $end === false ? $len : $end + 1;
        } elseif ($html[$lt + 1] === '/') {
            preg_match('/\G<\/([a-zA-Z][^\s\/>]*)/', $html, $m, 0, $lt);
            $name = strtolower($m[1] ?? '');
            $end = strpos($html, '>', $lt);
            $pos = $end === false ? $len : $end + 1;

            if ($name === 'section' && $sectionDepth > 0) {
                $sectionDepth--;
                if ($sectionDepth === 0 && $groupKey !== null && isset($groups[$groupKey])) {
                    $lastSection = ['key' => $groupKey, 'label' => $groups[$groupKey]['label']];
                    $groupKey = null;
                }
            }
            for ($i = count($stack) - 1; $i >= 0; $i--) {
                if ($stack[$i]['name'] === $name) {
                    array_splice($stack, $i);
                    break;
                }
            }
        } else {
            $gt = pc_tag_end($html, $lt + 1, $len);
            $tag = substr($html, $lt, $gt - $lt + 1);
            $pos = $gt + 1;
            preg_match('/^<([a-zA-Z][^\s\/>]*)/', $tag, $m);
            $name = strtolower($m[1] ?? '');
            $selfClosing = substr($tag, -2) === '/>';

            if (in_array($name, PC_RAW_TEXT_TAGS, true)) {
                $close = stripos($html, '</' . $name, $pos);
                if ($close === false) {
                    $pos = $len;
                } else {
                    $end = strpos($html, '>', $close);
                    $pos = $end === false ? $len : $end + 1;
                }
            } elseif (in_array($name, PC_SKIP_TAGS, true)) {
                if (!$selfClosing) {
                    $depth = 1;
                    $p = $pos;
                    $re = '/<(\/?)' . preg_quote($name, '/') . '(?=[\s\/>])/i';
                    while ($depth > 0 && preg_match($re, $html, $sm, PREG_OFFSET_CAPTURE, $p)) {
                        $depth += $sm[1][0] === '/' ? -1 : 1;
                        $end = strpos($html, '>', $sm[0][1]);
                        $p = $end === false ? $len : $end + 1;
                    }
                    $pos = $depth > 0 ? $len : $p;
                }
            } else {
                $attrs = null;

                if ($name === 'section') {
                    $sectionDepth++;
                    if ($sectionDepth === 1) {
                        $sectionCount++;
                        $attrs = pc_parse_attrs($tag);
                        $id = trim((string) pc_attr($attrs, 'id'));
                        $key = 'section:' . ($id !== '' ? $id : '#' . $sectionCount);
                        if (isset($groups[$key])) {
                            $key .= '#' . $sectionCount;
                        }
                        $label = $id !== '' ? pc_humanize($id) : 'Section ' . $sectionCount;
                        $groups[$key] = [
                            'key' => $key,
                            'type' => 'section',
                            'label' => $label,
                            'anchor' => $id,
                            'title' => '',
                            'title_seq' => null,
                            'title_done' => false,
                        ];
                        $groupKey = $key;
                    }
                }

                if ($name === 'img') {
                    $attrs = $attrs ?? pc_parse_attrs($tag);
                    $src = (string) pc_attr($attrs, 'src');
                    if ($src !== '') {
                        if ($groupKey === null) {
                            $groupKey = $lastSection ? 'after:' . $lastSection['key'] : 'top';
                            if (!isset($groups[$groupKey])) {
                                $groups[$groupKey] = [
                                    'key' => $groupKey,
                                    'type' => 'loose',
                                    'label' => $lastSection ? 'Content after ' . $lastSection['label'] : 'Top of page',
                                    'anchor' => '',
                                    'title' => '',
                                    'title_seq' => null,
                                    'title_done' => true,
                                ];
                            }
                        }
                        $images[] = [
                            'hash' => pc_image_hash($groupKey, pc_image_path($src)),
                            'group' => $groupKey,
                            'start' => $lt,
                            'length' => strlen($tag),
                            'tag' => $tag,
                            'src' => $src,
                            'alt' => pc_normalize((string) pc_attr($attrs, 'alt')),
                        ];
                    }
                }

                if (!$selfClosing && !in_array($name, PC_VOID_TAGS, true)) {
                    $classAttr = '';
                    if (preg_match('/\sclass\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $tag, $cm)) {
                        $classAttr = trim($cm[2] !== '' ? $cm[2] : ($cm[3] ?? '') . ($cm[4] ?? ''));
                    }
                    $stack[] = ['name' => $name, 'class' => $classAttr, 'seq' => ++$seq];
                }
            }
        }

        $textStart = $pos;
    }

    foreach ($texts as $i => $t) {
        $texts[$i]['parts'] = $t['owner'] !== null ? ($ownerParts[$t['owner']] ?? 1) : 1;
    }

    return ['groups' => $groups, 'texts' => $texts, 'images' => $images];
}

/**
 * Admin view of a scan: sections in page order, each with its unique text
 * fields and images (repeats counted, not duplicated). Empty sections dropped.
 */
function pc_sections(array $scan): array
{
    $sections = [];
    foreach ($scan['groups'] as $key => $g) {
        $sections[$key] = [
            'key' => $key,
            'type' => $g['type'],
            'label' => $g['label'],
            'anchor' => $g['anchor'],
            'title' => $g['title'],
            'texts' => [],
            'images' => [],
        ];
    }
    foreach ($scan['texts'] as $t) {
        $s = &$sections[$t['group']]['texts'];
        if (isset($s[$t['key']])) {
            $s[$t['key']]['count']++;
        } else {
            $s[$t['key']] = $t + ['count' => 1];
        }
        unset($s);
    }
    foreach ($scan['images'] as $img) {
        $s = &$sections[$img['group']]['images'];
        if (isset($s[$img['hash']])) {
            $s[$img['hash']]['count']++;
        } else {
            $s[$img['hash']] = $img + ['count' => 1];
        }
        unset($s);
    }
    return array_filter($sections, fn ($s) => $s['texts'] || $s['images']);
}

/** Saved overrides for a page: item_key => value. Empty if migration 027 hasn't run. */
function page_content_overrides(PDO $pdo, int $pageId): array
{
    try {
        $stmt = $pdo->prepare('SELECT item_key, value FROM page_content WHERE page_id = ?');
        $stmt->execute([$pageId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    } catch (PDOException $e) {
        return [];
    }
}

/** Set, replace or (with null) remove one attribute of a start tag, keeping the rest as written. */
function pc_set_attrs(string $tag, array $changes): string
{
    $attrs = pc_parse_attrs($tag);
    preg_match('/^<[a-zA-Z][^\s\/>]*/', $tag, $m);
    $out = $m[0];
    $done = [];
    foreach ($attrs as $a) {
        if (array_key_exists($a['name'], $changes)) {
            if (!isset($done[$a['name']]) && $changes[$a['name']] !== null) {
                $out .= ' ' . $a['name'] . '="' . h($changes[$a['name']]) . '"';
            }
            $done[$a['name']] = true;
        } else {
            $out .= ' ' . substr($tag, $a['start'], $a['end'] - $a['start']);
        }
    }
    foreach ($changes as $name => $value) {
        if (!isset($done[$name]) && $value !== null) {
            $out .= ' ' . $name . '="' . h($value) . '"';
        }
    }
    return $out . (substr($tag, -2) === '/>' ? ' />' : '>');
}

/**
 * Apply a page's saved content edits to its rendered template HTML. Returns
 * the HTML untouched when nothing is saved, or if anything goes wrong, so a
 * bad edit can never take a page down.
 */
function page_content_apply(PDO $pdo, int $pageId, string $html): string
{
    $overrides = page_content_overrides($pdo, $pageId);
    if (!$overrides) {
        return $html;
    }

    try {
        $scan = pc_scan($html);
        $edits = [];

        foreach ($scan['texts'] as $t) {
            if (isset($overrides[$t['key']])) {
                $edits[] = [$t['start'], $t['length'], $t['lead'] . h($overrides[$t['key']]) . $t['trail']];
            }
        }

        foreach ($scan['images'] as $img) {
            $src = $overrides['i' . $img['hash']] ?? null;
            $alt = $overrides['a' . $img['hash']] ?? null;
            if ($src === null && $alt === null) {
                continue;
            }
            $changes = [];
            if ($src !== null) {
                // A responsive srcset would win over the new src, so drop it.
                $changes = ['src' => $src, 'srcset' => null, 'sizes' => null];
                if (pc_attr(pc_parse_attrs($img['tag']), 'data-src') !== null) {
                    $changes['data-src'] = $src;
                }
            }
            if ($alt !== null) {
                $changes['alt'] = $alt;
            }
            $edits[] = [$img['start'], $img['length'], pc_set_attrs($img['tag'], $changes)];
        }

        if (!$edits) {
            return $html;
        }
        usort($edits, fn ($a, $b) => $b[0] <=> $a[0]);
        foreach ($edits as [$start, $length, $replacement]) {
            $html = substr_replace($html, $replacement, $start, $length);
        }
        return $html;
    } catch (Throwable $e) {
        error_log('page_content_apply(page ' . $pageId . '): ' . $e->getMessage());
        return $html;
    }
}

/** templates/{template}-body.php for a page row, or null if it has none. */
function pc_template_file(array $page): ?string
{
    $template = basename((string) ($page['template'] ?? ''));
    if ($template === '') {
        return null;
    }
    $file = __DIR__ . '/../templates/' . $template . '-body.php';
    return is_file($file) ? $file : null;
}

/**
 * Render a page's template body exactly as index.php's generic page route
 * does, for the admin to scan. Null if the page has no template or it fails.
 */
function pc_render_template(PDO $pdo, array $page): ?string
{
    $templateFile = pc_template_file($page);
    if ($templateFile === null) {
        return null;
    }

    $level = ob_get_level();
    ob_start();
    try {
        (static function (PDO $pdo, array $page, string $templateFile): void {
            $uri = $page['slug'];
            include $templateFile;
        })($pdo, $page, $templateFile);
        $html = (string) ob_get_clean();
    } catch (Throwable $e) {
        while (ob_get_level() > $level) {
            ob_end_clean();
        }
        error_log('pc_render_template(' . $templateFile . '): ' . $e->getMessage());
        $html = null;
    }

    // A template that can't resolve its content (e.g. an unknown industry
    // slug) sets a 404 status — that belongs to the public page, not the admin.
    if (!headers_sent()) {
        http_response_code(200);
    }
    return $html;
}

/**
 * Validate and store one uploaded replacement image from the section form.
 * @return array{path: string, error: string}  path is '' when nothing was uploaded
 */
function pc_store_upload(array $files, string $hash, int $pageId): array
{
    $err = $files['pc_img']['error'][$hash] ?? UPLOAD_ERR_NO_FILE;
    if ((int) $err === UPLOAD_ERR_NO_FILE) {
        return ['path' => '', 'error' => ''];
    }
    if ((int) $err !== UPLOAD_ERR_OK) {
        $tooBig = in_array((int) $err, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true);
        return ['path' => '', 'error' => $tooBig ? 'is larger than the server accepts.' : 'did not upload. Please try again.'];
    }

    $tmp = (string) ($files['pc_img']['tmp_name'][$hash] ?? '');
    $size = (int) ($files['pc_img']['size'][$hash] ?? 0);
    $mime = $tmp !== '' ? (string) @mime_content_type($tmp) : '';

    if (!array_key_exists($mime, PC_IMAGE_TYPES)) {
        return ['path' => '', 'error' => 'must be a JPG, PNG, WEBP, or GIF file.'];
    }
    if ($size > PC_IMAGE_MAX_BYTES) {
        return ['path' => '', 'error' => 'must be smaller than 5MB.'];
    }
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    $filename = 'page-' . $pageId . '-' . bin2hex(random_bytes(5)) . '.' . PC_IMAGE_TYPES[$mime];
    if (!move_uploaded_file($tmp, UPLOAD_DIR . $filename)) {
        return ['path' => '', 'error' => 'could not be saved. Please try again.'];
    }
    return ['path' => UPLOAD_URL . $filename, 'error' => ''];
}

/**
 * Save one section's form. A text field left empty, or matching the template,
 * removes its override so the template copy shows again; the same goes for
 * alt text. Uploads are all validated before anything is written.
 *
 * @return array{error: string, changed: int}
 */
function pc_save_section(PDO $pdo, int $pageId, array $section, array $post, array $files): array
{
    $current = page_content_overrides($pdo, $pageId);
    $postedTexts = is_array($post['pc_text'] ?? null) ? $post['pc_text'] : [];
    $postedAlts = is_array($post['pc_alt'] ?? null) ? $post['pc_alt'] : [];
    $resets = is_array($post['pc_img_reset'] ?? null) ? $post['pc_img_reset'] : [];

    $uploads = [];
    $n = 0;
    foreach ($section['images'] as $hash => $img) {
        $n++;
        if (!empty($resets[$hash])) {
            continue;
        }
        $up = pc_store_upload($files, (string) $hash, $pageId);
        if ($up['error'] !== '') {
            return ['error' => 'Image ' . $n . ' ' . $up['error'], 'changed' => 0];
        }
        if ($up['path'] !== '') {
            $uploads[$hash] = $up['path'];
        }
    }

    $upsert = $pdo->prepare(
        'INSERT INTO page_content (page_id, item_key, section_key, kind, original, value) VALUES (?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE section_key = VALUES(section_key), kind = VALUES(kind),
         original = VALUES(original), value = VALUES(value)'
    );
    $delete = $pdo->prepare('DELETE FROM page_content WHERE page_id = ? AND item_key = ?');
    $changed = 0;

    $set = function (string $key, string $kind, string $original, ?string $value) use ($pageId, $section, $current, $upsert, $delete, &$changed): void {
        if ($value === null) {
            if (array_key_exists($key, $current)) {
                $delete->execute([$pageId, $key]);
                $changed++;
            }
        } elseif (($current[$key] ?? null) !== $value) {
            $upsert->execute([$pageId, $key, $section['key'], $kind, $original, $value]);
            $changed++;
        }
    };

    foreach ($section['texts'] as $key => $item) {
        if (!array_key_exists($key, $postedTexts)) {
            continue;
        }
        $value = pc_normalize((string) $postedTexts[$key]);
        $set((string) $key, 'text', $item['text'], ($value === '' || $value === $item['text']) ? null : $value);
    }

    foreach ($section['images'] as $hash => $img) {
        if (!empty($resets[$hash])) {
            $set('i' . $hash, 'img_src', $img['src'], null);
        } elseif (isset($uploads[$hash])) {
            $set('i' . $hash, 'img_src', $img['src'], $uploads[$hash]);
        }
        if (array_key_exists($hash, $postedAlts)) {
            $alt = pc_normalize((string) $postedAlts[$hash]);
            $set('a' . $hash, 'img_alt', $img['alt'], ($alt === '' || $alt === $img['alt']) ? null : $alt);
        }
    }

    return ['error' => '', 'changed' => $changed];
}

/** Stable DOM id for a section's panel in the admin. */
function pc_dom_id(string $sectionKey): string
{
    return 'pc-' . substr(md5($sectionKey), 0, 10);
}
