<?php
/**
 * Visual page editor (Admin → Pages → Edit → Page Content).
 *
 * Page copy lives in the PHP templates (templates/{template}-body.php), not
 * the database. Rather than rewrite every template around content fields,
 * this works on the HTML a template renders:
 *
 *   1. pc_scan() walks the rendered HTML and finds each top-level <section>
 *      (plus the loose content between sections, like marquees), and inside
 *      each one its editable text and every <img>.
 *   2. The admin lists those per section beside a live preview. Publishing
 *      stores only what differs from the template in the page_content table,
 *      plus one "layout" row for hidden / reordered sections.
 *   3. When the page is served, page_content_apply() re-scans the template's
 *      output and splices the saved text, images and layout in. All other
 *      markup is left byte-for-byte as the template wrote it.
 *
 * Editable text comes in two shapes:
 *   - a block: an element holding text plus simple styled pieces, e.g.
 *     <h1>One <span class="grad">OS</span> with <span>AI</span></h1>. It is one
 *     field, "One [OS] with [AI]": each [bracketed] piece keeps the tag of the
 *     original styled piece at that position, and a new line is a <br>.
 *   - a run: any other piece of text, edited on its own.
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

/** Elements that best describe a piece of text, nearest first. */
const PC_LABEL_TAGS = [
    'h1' => 'Main heading', 'h2' => 'Heading', 'h3' => 'Subheading', 'h4' => 'Subheading',
    'h5' => 'Subheading', 'h6' => 'Subheading', 'p' => 'Paragraph', 'li' => 'List item',
    'a' => 'Link text', 'button' => 'Button text', 'label' => 'Label', 'blockquote' => 'Quote',
    'figcaption' => 'Caption', 'td' => 'Table cell', 'th' => 'Table heading', 'dt' => 'Term',
    'dd' => 'Description', 'summary' => 'Summary', 'option' => 'Option',
];

/** Elements that can be edited as one block field. */
const PC_BLOCK_TAGS = [
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'li', 'a', 'button', 'label', 'blockquote', 'figcaption',
    'td', 'th', 'dt', 'dd', 'summary', 'div', 'span', 'strong', 'em', 'b', 'i', 'small', 'q', 'cite', 'mark', 'u',
];

/** Styled pieces a block may contain (each holding plain text only). */
const PC_INLINE_TAGS = [
    'span', 'em', 'strong', 'b', 'i', 'u', 'mark', 'small', 'sup', 'sub', 'a', 'abbr', 'code',
    's', 'del', 'ins', 'q', 'cite', 'time', 'font',
];

const PC_IMAGE_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
const PC_IMAGE_MAX_BYTES = 5 * 1024 * 1024;

/** Words shown in capitals in section names made from ids ("cta-mid" → "CTA Mid"). */
const PC_ACRONYMS = ['cta', 'faq', 'erp', 'crm', 'ai', 'os', 'hr', 'seo', 'ui', 'api', 'roi', 'kpi'];

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

/** Normalise a block value typed by an editor: each line tidied, new lines kept as line breaks. */
function pc_normalize_block(string $s): string
{
    $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $s));
    return trim(implode("\n", array_map('pc_normalize', $lines)), "\n");
}

/** "about-drawlead" → "About Drawlead", "cta-mid" → "CTA Mid". */
function pc_humanize(string $id): string
{
    $words = preg_split('/[-_\s]+/', trim($id), -1, PREG_SPLIT_NO_EMPTY);
    return implode(' ', array_map(
        fn ($w) => in_array(strtolower($w), PC_ACRONYMS, true) ? strtoupper($w) : ucfirst($w),
        $words ?: []
    ));
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

function pc_class_hint(string $name, string $class): string
{
    $first = $class !== '' ? (preg_split('/\s+/', $class)[0] ?? '') : '';
    return $name . ($first !== '' ? '.' . $first : '');
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

function pc_block_hash(string $groupKey, string $text): string
{
    return substr(md5($groupKey . "\nblock\n" . $text), 0, 16);
}

function pc_image_hash(string $groupKey, string $path): string
{
    return substr(md5($groupKey . "\nimg\n" . $path), 0, 16);
}

/** A block's field value from its parts: text as-is, styled pieces in [brackets], <br> as a new line. */
function pc_block_display(array $parts): string
{
    $s = '';
    foreach ($parts as $p) {
        if ($p[0] === 't') {
            $s .= html_entity_decode($p[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } elseif ($p[0] === 'c') {
            $s .= '[' . html_entity_decode($p[2], ENT_QUOTES | ENT_HTML5, 'UTF-8') . ']';
        } else {
            $s .= "\x01";
        }
    }
    // Source line breaks are just spacing; only <br> (\x01) is a real new line.
    $s = (string) (preg_replace('/\s+/u', ' ', $s) ?? preg_replace('/\s+/', ' ', $s));
    $s = (string) preg_replace('/ *\x01 */', "\n", $s);
    return trim($s, " \n");
}

/** Inner HTML for a block from an edited value (see pc_block_display()). */
function pc_block_html(array $block, string $value): string
{
    $tokens = preg_split('/(\[[^\[\]\n]*\]|\n)/u', $value, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    if ($tokens === false) {
        $tokens = [$value];
    }
    $children = $block['children'];
    $count = count($children);
    $out = '';
    $k = 0;
    foreach ($tokens as $tok) {
        if ($tok === "\n") {
            $out .= $block['br'] ?? '<br>';
        } elseif ($count > 0 && strlen($tok) >= 2 && $tok[0] === '[' && substr($tok, -1) === ']') {
            // More bracketed pieces than the template had reuse the last style.
            $c = $children[min($k, $count - 1)];
            $k++;
            $out .= $c['open'] . h(substr($tok, 1, -1)) . $c['close'];
        } else {
            $out .= h($tok);
        }
    }
    return $block['lead'] . $out . $block['trail'];
}

/**
 * Walk rendered template HTML and return every editable text run, text block
 * and image, grouped by section, with byte offsets for splicing.
 *
 * @return array{groups: array<string, array>, texts: list<array>, blocks: list<array>, images: list<array>}
 */
function pc_scan(string $html): array
{
    $len = strlen($html);
    $groups = [];
    $texts = [];
    $blocks = [];
    $images = [];

    $stack = [];
    $seq = 0;
    $ownerParts = [];     // element seq => number of text runs inside it
    $sectionDepth = 0;
    $sectionCount = 0;
    $groupKey = null;     // null = loose content not yet given a group
    $lastSection = null;  // ['key', 'label'] of the most recent closed section

    // Loose content (between sections) is grouped as "Content after <section>".
    $ensureGroup = function () use (&$groupKey, &$groups, &$lastSection): string {
        if ($groupKey === null) {
            $groupKey = $lastSection ? 'after:' . $lastSection['key'] : 'top';
            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'key' => $groupKey, 'type' => 'loose',
                    'label' => $lastSection ? 'Content after ' . $lastSection['label'] : 'Top of page',
                    'anchor' => '', 'title' => '', 'title_seq' => null, 'title_done' => true,
                    'start' => null, 'end' => null,
                ];
            }
        }
        return $groupKey;
    };
    $complexTop = function () use (&$stack): void {
        if ($stack) {
            $stack[count($stack) - 1]['complex'] = true;
        }
    };

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
            if ($stack) {
                $stack[count($stack) - 1]['parts'][] = ['t', $raw];
            }
            if (strspn($raw, " \t\n\r\f") !== strlen($raw)) {
                $decoded = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $text = pc_normalize($decoded);
                // Needs a letter or digit (skips "→", "•"), and a lone letter is
                // one glyph of a letter-by-letter animation, not editable copy.
                if ($text !== '' && preg_match('/[\p{L}\p{N}]/u', $text) && !preg_match('/^\p{L}$/u', $text)) {
                    $gk = $ensureGroup();

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
                        $hint = pc_class_hint($parent['name'], $parent['class']);
                        $ownerSeq = $ownerSeq ?? $parent['seq'];
                    }
                    $part = 1;
                    if ($ownerSeq !== null) {
                        $part = $ownerParts[$ownerSeq] = ($ownerParts[$ownerSeq] ?? 0) + 1;
                    }

                    // The section's first h1–h3 doubles as its subtitle in the admin.
                    $g = &$groups[$gk];
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
                        'type' => 'text',
                        'key' => 't' . pc_text_hash($gk, $text),
                        'group' => $gk,
                        'start' => $textStart,
                        'length' => $at - $textStart,
                        // surrounding whitespace is kept so inline spacing survives an edit
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
                    $groups[$groupKey]['end'] = $pos;
                    $lastSection = ['key' => $groupKey, 'label' => $groups[$groupKey]['label']];
                    $groupKey = null;
                }
            }

            $idx = null;
            for ($i = count($stack) - 1; $i >= 0; $i--) {
                if ($stack[$i]['name'] === $name) {
                    $idx = $i;
                    break;
                }
            }
            if ($idx !== null) {
                $el = $stack[$idx];
                if ($idx !== count($stack) - 1) {
                    $el['complex'] = true; // it had unclosed elements inside
                }
                array_splice($stack, $idx);

                $plain = !$el['complex'] && !$el['has_elem'];
                $inner = substr($html, $el['inner_start'], $lt - $el['inner_start']);

                // One field for "text + simple styled pieces" (see file header).
                if (!$el['complex'] && $el['has_elem'] && $el['all_inline'] && in_array($name, PC_BLOCK_TAGS, true)) {
                    $meaningful = 0;
                    $textParts = 0;
                    $singleChars = 0;
                    foreach ($el['parts'] as $p) {
                        if ($p[0] === 't') {
                            $textParts += trim($p[1]) !== '' ? 1 : 0;
                            $meaningful += trim($p[1]) !== '' ? 1 : 0;
                        } else {
                            $meaningful++;
                            if ($p[0] === 'c' && mb_strlen(trim(html_entity_decode($p[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')), 'UTF-8') <= 1) {
                                $singleChars++;
                            }
                        }
                    }
                    $display = pc_block_display($el['parts']);
                    $letterAnimation = count($el['children']) >= 3 && $singleChars * 2 >= count($el['children']);
                    // Needs some plain text of its own: "[Title][Subtitle]" with nothing
                    // between is two separate items, better as two fields.
                    if ($meaningful >= 2 && $textParts >= 1 && !$letterAnimation && preg_match('/[\p{L}\p{N}]/u', $display)) {
                        $gk = $ensureGroup();
                        $blocks[] = [
                            'type' => 'block',
                            'key' => 'b' . pc_block_hash($gk, $display),
                            'group' => $gk,
                            'start' => $el['open_start'],
                            'name_end' => $el['open_start'] + 1 + strlen($name),
                            'inner_start' => $el['inner_start'],
                            'inner_end' => $lt,
                            'lead' => substr($inner, 0, strlen($inner) - strlen(ltrim($inner))),
                            'trail' => substr($inner, strlen(rtrim($inner))),
                            'children' => $el['children'],
                            'br' => $el['br'],
                            'text' => $display,
                            'kind' => array_key_exists($name, PC_LABEL_TAGS) ? PC_LABEL_TAGS[$name] : 'Text',
                            'hint' => pc_class_hint($name, $el['class']),
                        ];
                    }
                }

                // Tell the parent what it contains.
                if ($stack) {
                    $t = count($stack) - 1;
                    $stack[$t]['has_elem'] = true;
                    // An empty styled piece (a decorative cursor, a spacer) would show
                    // as a bare "[]" — leave such an element as separate runs.
                    if ($plain && in_array($name, PC_INLINE_TAGS, true) && trim($inner) !== '') {
                        $stack[$t]['children'][] = ['open' => $el['open_tag'], 'close' => substr($html, $lt, $pos - $lt)];
                        $stack[$t]['parts'][] = ['c', count($stack[$t]['children']) - 1, $inner];
                    } else {
                        $stack[$t]['all_inline'] = false;
                    }
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
                $complexTop();
                $close = stripos($html, '</' . $name, $pos);
                if ($close === false) {
                    $pos = $len;
                } else {
                    $end = strpos($html, '>', $close);
                    $pos = $end === false ? $len : $end + 1;
                }
            } elseif (in_array($name, PC_SKIP_TAGS, true)) {
                $complexTop();
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
                        $groups[$key] = [
                            'key' => $key, 'type' => 'section',
                            'label' => $id !== '' ? pc_humanize($id) : 'Section ' . $sectionCount,
                            'anchor' => $id, 'title' => '', 'title_seq' => null, 'title_done' => false,
                            'start' => $lt, 'end' => null,
                        ];
                        $groupKey = $key;
                    }
                }

                if ($name === 'img') {
                    $attrs = $attrs ?? pc_parse_attrs($tag);
                    $src = (string) pc_attr($attrs, 'src');
                    if ($src !== '') {
                        $gk = $ensureGroup();
                        $images[] = [
                            'hash' => pc_image_hash($gk, pc_image_path($src)),
                            'group' => $gk,
                            'start' => $lt,
                            'length' => strlen($tag),
                            'tag' => $tag,
                            'src' => $src,
                            'alt' => pc_normalize((string) pc_attr($attrs, 'alt')),
                        ];
                    }
                }

                if ($name === 'br') {
                    if ($stack) {
                        $t = count($stack) - 1;
                        $stack[$t]['has_elem'] = true;
                        $stack[$t]['parts'][] = ['br'];
                        $stack[$t]['br'] = $stack[$t]['br'] ?? $tag;
                    }
                } elseif ($selfClosing || in_array($name, PC_VOID_TAGS, true)) {
                    $complexTop(); // an image, input, etc. — not a plain text block
                } else {
                    $classAttr = '';
                    if (preg_match('/\sclass\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $tag, $cm)) {
                        $classAttr = trim(($cm[2] ?? '') !== '' ? $cm[2] : ($cm[3] ?? '') . ($cm[4] ?? ''));
                    }
                    $stack[] = [
                        'name' => $name, 'class' => $classAttr, 'seq' => ++$seq,
                        'open_start' => $lt, 'inner_start' => $pos, 'open_tag' => $tag,
                        'parts' => [], 'children' => [], 'br' => null,
                        'has_elem' => false, 'all_inline' => true, 'complex' => false,
                    ];
                }
            }
        }

        $textStart = $pos;
    }

    // A run inside a block is edited through that block, not on its own.
    if ($blocks) {
        $ranges = array_map(fn ($b) => [$b['inner_start'], $b['inner_end']], $blocks);
        $texts = array_values(array_filter($texts, function ($t) use ($ranges) {
            foreach ($ranges as [$s, $e]) {
                if ($t['start'] >= $s && $t['start'] < $e) {
                    return false;
                }
            }
            return true;
        }));
    }
    foreach ($texts as $i => $t) {
        $texts[$i]['parts'] = $t['owner'] !== null ? ($ownerParts[$t['owner']] ?? 1) : 1;
    }

    return ['groups' => $groups, 'texts' => $texts, 'blocks' => $blocks, 'images' => $images];
}

/**
 * Admin view of a scan: groups in page order, each with its unique text fields
 * (runs and blocks, in page order) and images, repeats counted not duplicated.
 * Every <section> is kept, even with nothing to edit, so it can be hidden or
 * moved; empty loose groups are dropped.
 */
function pc_sections(array $scan): array
{
    $sections = [];
    foreach ($scan['groups'] as $key => $g) {
        $sections[$key] = [
            'key' => $key, 'type' => $g['type'], 'label' => $g['label'], 'anchor' => $g['anchor'],
            'title' => $g['title'], 'texts' => [], 'images' => [],
        ];
    }
    $items = array_merge($scan['texts'], $scan['blocks']);
    usort($items, fn ($a, $b) => $a['start'] <=> $b['start']);
    foreach ($items as $t) {
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
    return array_filter($sections, fn ($s) => $s['type'] === 'section' || $s['texts'] || $s['images']);
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

/**
 * The saved section layout: order (section keys) and hidden (key => true),
 * limited to sections the template still has. Sections new to the template
 * keep their place relative to the ones around them at the end of the order.
 */
function pc_layout(array $overrides, array $sectionKeys): array
{
    $saved = json_decode((string) ($overrides['layout'] ?? ''), true);
    $known = array_flip($sectionKeys);
    $order = [];
    foreach ((array) ($saved['order'] ?? []) as $k) {
        if (is_string($k) && isset($known[$k]) && !in_array($k, $order, true)) {
            $order[] = $k;
        }
    }
    foreach ($sectionKeys as $k) {
        if (!in_array($k, $order, true)) {
            $order[] = $k;
        }
    }
    $hidden = [];
    foreach ((array) ($saved['hidden'] ?? []) as $k) {
        if (is_string($k) && isset($known[$k])) {
            $hidden[$k] = true;
        }
    }
    return ['order' => $order, 'hidden' => $hidden];
}

/** Set, replace or (with null) remove attributes of a start tag, keeping the rest as written. */
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

/** Apply [start, length, replacement] edits (sorted by start, descending) that fall inside [$from, $to). */
function pc_splice_range(string $html, array $edits, int $from, int $to): string
{
    $out = substr($html, $from, $to - $from);
    foreach ($edits as [$start, $length, $replacement]) {
        if ($start >= $from && $start + $length <= $to) {
            $out = substr_replace($out, $replacement, $start - $from, $length);
        }
    }
    return $out;
}

/**
 * Apply a page's saved content edits and section layout to its rendered
 * template HTML. Returns the HTML untouched when nothing is saved, or if
 * anything goes wrong, so a bad edit can never take a page down.
 *
 * $preview (the admin's live preview, logged-in editors only) also tags every
 * editable item with data-pc-* attributes / <!--pc:key--> markers for the
 * editor's script, and leaves hiding and reordering to that script.
 */
function page_content_apply(PDO $pdo, int $pageId, string $html, bool $preview = false): string
{
    $overrides = page_content_overrides($pdo, $pageId);
    if (!$overrides && !$preview) {
        return $html;
    }

    try {
        $scan = pc_scan($html);
        $edits = [];

        foreach ($scan['texts'] as $t) {
            $value = $overrides[$t['key']] ?? null;
            if ($preview) {
                $content = $value !== null ? h($value) : substr($html, $t['start'] + strlen($t['lead']), $t['length'] - strlen($t['lead']) - strlen($t['trail']));
                $edits[] = [$t['start'], $t['length'], $t['lead'] . '<!--pc:' . $t['key'] . '-->' . $content . '<!--/pc-->' . $t['trail']];
            } elseif ($value !== null) {
                $edits[] = [$t['start'], $t['length'], $t['lead'] . h($value) . $t['trail']];
            }
        }

        foreach ($scan['blocks'] as $b) {
            if (isset($overrides[$b['key']])) {
                $edits[] = [$b['inner_start'], $b['inner_end'] - $b['inner_start'], pc_block_html($b, $overrides[$b['key']])];
            }
            if ($preview) {
                $edits[] = [$b['name_end'], 0, ' data-pc-block="' . h($b['key']) . '"'];
            }
        }

        foreach ($scan['images'] as $img) {
            $src = $overrides['i' . $img['hash']] ?? null;
            $alt = $overrides['a' . $img['hash']] ?? null;
            if ($src === null && $alt === null && !$preview) {
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
            if ($preview) {
                $changes['data-pc-img'] = $img['hash'];
            }
            $edits[] = [$img['start'], $img['length'], pc_set_attrs($img['tag'], $changes)];
        }

        $sections = array_values(array_filter($scan['groups'], fn ($g) => $g['type'] === 'section' && $g['end'] !== null));
        if ($preview) {
            foreach ($sections as $s) {
                $edits[] = [$s['start'] + strlen('<section'), 0, ' data-pc-section="' . h($s['key']) . '"'];
            }
        }

        usort($edits, fn ($a, $b) => $b[0] <=> $a[0]);

        $layout = pc_layout($overrides, array_column($sections, 'key'));
        $reordered = $layout['order'] !== array_column($sections, 'key');
        if ($preview || (!$reordered && !$layout['hidden'])) {
            return pc_splice_range($html, $edits, 0, strlen($html));
        }

        // Hidden and reordered sections: each section slot, in page order, gets
        // the section the layout puts there. Content between slots stays put.
        $byKey = array_column($sections, null, 'key');
        $out = '';
        $cursor = 0;
        foreach ($sections as $i => $slot) {
            $out .= pc_splice_range($html, $edits, $cursor, $slot['start']);
            $sec = $byKey[$layout['order'][$i]];
            $content = pc_splice_range($html, $edits, $sec['start'], $sec['end']);
            if (isset($layout['hidden'][$sec['key']])) {
                // Kept in the page (its scripts may look for it) but never shown.
                // The first style attribute wins, so this beats the section's own.
                $content = (string) preg_replace('/^<section\b/i', '<section hidden style="display:none !important"', $content, 1);
            }
            $out .= $content;
            $cursor = $slot['end'];
        }
        return $out . pc_splice_range($html, $edits, $cursor, strlen($html));
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
 * Validate and store one uploaded replacement image.
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
 * Publish the editor's form for the whole page. Only fields the editor
 * changed are posted; anything not posted is left as it is. A text left empty,
 * or matching the template, removes its override so the template copy shows
 * again (same for alt text). Uploads are validated before anything is written.
 *
 * @return array{error: string, changed: int}
 */
function pc_save_page(PDO $pdo, int $pageId, array $sections, array $post, array $files): array
{
    $current = page_content_overrides($pdo, $pageId);
    $arr = fn (string $k) => is_array($post[$k] ?? null) ? $post[$k] : [];
    $postedTexts = $arr('pc_text');
    $postedAlts = $arr('pc_alt');
    $resets = $arr('pc_img_reset');
    $postedUrls = $arr('pc_img_url');

    // New image per slot: an uploaded file wins over a pasted URL (e.g. one
    // copied from Admin → Media).
    $uploads = [];
    foreach ($sections as $section) {
        $n = 0;
        foreach ($section['images'] as $hash => $img) {
            $n++;
            if (!empty($resets[$hash])) {
                continue;
            }
            $where = '“' . $section['label'] . '” image ' . $n;
            $up = pc_store_upload($files, (string) $hash, $pageId);
            if ($up['error'] !== '') {
                return ['error' => $where . ' ' . $up['error'], 'changed' => 0];
            }
            if ($up['path'] !== '') {
                $uploads[$hash] = $up['path'];
                continue;
            }
            $url = trim((string) ($postedUrls[$hash] ?? ''));
            if ($url !== '') {
                if (!preg_match('#^(https?://|/)\S+$#i', $url)) {
                    return ['error' => $where . ' URL must start with https:// or / (copy it from Admin → Media).', 'changed' => 0];
                }
                $uploads[$hash] = $url;
            }
        }
    }

    $upsert = $pdo->prepare(
        'INSERT INTO page_content (page_id, item_key, section_key, kind, original, value) VALUES (?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE section_key = VALUES(section_key), kind = VALUES(kind),
         original = VALUES(original), value = VALUES(value)'
    );
    $delete = $pdo->prepare('DELETE FROM page_content WHERE page_id = ? AND item_key = ?');
    $changed = 0;

    $set = function (string $key, string $sectionKey, string $kind, string $original, ?string $value) use ($pageId, $current, $upsert, $delete, &$changed): void {
        if ($value === null) {
            if (array_key_exists($key, $current)) {
                $delete->execute([$pageId, $key]);
                $changed++;
            }
        } elseif (($current[$key] ?? null) !== $value) {
            $upsert->execute([$pageId, $key, $sectionKey, $kind, $original, $value]);
            $changed++;
        }
    };

    foreach ($sections as $section) {
        foreach ($section['texts'] as $key => $item) {
            $key = (string) $key;
            if (!array_key_exists($key, $postedTexts)) {
                continue;
            }
            $value = $item['type'] === 'block'
                ? pc_normalize_block((string) $postedTexts[$key])
                : pc_normalize((string) $postedTexts[$key]);
            $set($key, $section['key'], 'text', $item['text'], ($value === '' || $value === $item['text']) ? null : $value);
        }

        foreach ($section['images'] as $hash => $img) {
            if (!empty($resets[$hash])) {
                $set('i' . $hash, $section['key'], 'img_src', $img['src'], null);
            } elseif (isset($uploads[$hash])) {
                $set('i' . $hash, $section['key'], 'img_src', $img['src'], $uploads[$hash]);
            }
            if (array_key_exists($hash, $postedAlts)) {
                $alt = pc_normalize((string) $postedAlts[$hash]);
                $set('a' . $hash, $section['key'], 'img_alt', $img['alt'], ($alt === '' || $alt === $img['alt']) ? null : $alt);
            }
        }
    }

    if (isset($post['pc_layout']) && is_string($post['pc_layout'])) {
        $sectionKeys = array_keys(array_filter($sections, fn ($s) => $s['type'] === 'section'));
        $layout = pc_layout(['layout' => $post['pc_layout']], $sectionKeys);
        $isDefault = $layout['order'] === $sectionKeys && !$layout['hidden'];
        $set('layout', '', 'text', '', $isDefault ? null : json_encode(
            ['order' => $layout['order'], 'hidden' => array_keys($layout['hidden'])],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ));
    }

    return ['error' => '', 'changed' => $changed];
}
