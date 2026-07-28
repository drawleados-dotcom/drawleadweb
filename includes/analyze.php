<?php
/**
 * Drawlead Analyze — rule-based CRO analysis engine.
 *
 * Fetches a public URL server-side, parses its HTML, scores it against a
 * fixed CRO best-practices checklist, infers a likely target audience
 * from its own content, and builds a "rebuilt" content set (headline,
 * subhead, feature cards, trust stats, CTA) from the page's real copy —
 * dropped into Drawlead's own CRO layout for the Tab 1 preview.
 *
 * This is a deterministic checklist, not an AI/LLM model — every score
 * and "what changed" line traces back to a specific, named rule below,
 * so the report is always explainable.
 */

function analyze_generate_token(): string
{
    return bin2hex(random_bytes(8));
}

/** Adds https:// if the visitor typed a bare domain like "example.com". */
function analyze_normalize_url(string $input): string
{
    $input = trim($input);
    if ($input === '') {
        return '';
    }
    if (!preg_match('#^https?://#i', $input)) {
        $input = 'https://' . $input;
    }
    return $input;
}

/**
 * SSRF guard: true only if every IP the host resolves to is a public,
 * routable address — blocks loopback, RFC1918 private ranges, link-local
 * (including the 169.254.169.254 cloud metadata address), and other
 * reserved ranges.
 */
function analyze_host_is_public(string $host): bool
{
    $host = strtolower($host);
    if ($host === 'localhost' || str_ends_with($host, '.local') || str_ends_with($host, '.internal')) {
        return false;
    }

    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return (bool) filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    $ips = [];
    $records = @dns_get_record($host, DNS_A | DNS_AAAA);
    if ($records) {
        foreach ($records as $r) {
            if (!empty($r['ip'])) {
                $ips[] = $r['ip'];
            }
            if (!empty($r['ipv6'])) {
                $ips[] = $r['ipv6'];
            }
        }
    }
    if (!$ips) {
        $resolved = @gethostbyname($host);
        if ($resolved && $resolved !== $host) {
            $ips[] = $resolved;
        }
    }
    if (!$ips) {
        return false;
    }

    foreach ($ips as $ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }
    }
    return true;
}

/** Resolves a redirect Location header (which may be relative) against the current URL. */
function analyze_resolve_redirect(string $base, string $location): string
{
    if ($location === '') {
        return $base;
    }
    if (preg_match('#^https?://#i', $location)) {
        return $location;
    }
    $baseParts = parse_url($base);
    $scheme = $baseParts['scheme'] ?? 'https';
    $host = $baseParts['host'] ?? '';
    $port = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';
    if (str_starts_with($location, '//')) {
        return $scheme . ':' . $location;
    }
    if (str_starts_with($location, '/')) {
        return "$scheme://$host$port$location";
    }
    $basePath = $baseParts['path'] ?? '/';
    $dir = strrpos($basePath, '/') !== false ? substr($basePath, 0, strrpos($basePath, '/') + 1) : '/';
    return "$scheme://$host$port$dir$location";
}

/**
 * Fetches a URL server-side with SSRF protection (public IPs only, no
 * private/loopback/link-local ranges, http/https only), a manual bounded
 * redirect loop (each hop re-checked), timeouts, and a response size cap.
 * Returns ['ok'=>bool, 'html'=>string, 'final_url'=>string, 'error'=>string].
 */
function analyze_fetch_html(string $url): array
{
    $maxBytes = 3 * 1024 * 1024;
    $current = $url;

    for ($hop = 0; $hop <= 5; $hop++) {
        $parts = parse_url($current);
        if (!$parts || empty($parts['scheme']) || empty($parts['host']) || !in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return ['ok' => false, 'error' => 'Only http:// and https:// URLs are supported.'];
        }
        if (!analyze_host_is_public($parts['host'])) {
            return ['ok' => false, 'error' => 'That host could not be reached.'];
        }

        $ch = curl_init($current);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_USERAGENT => 'DrawleadAnalyze/1.0 (+https://drawlead.com/analyze)',
            CURLOPT_HTTPHEADER => ['Accept: text/html,application/xhtml+xml'],
            CURLOPT_RANGE => '0-' . ($maxBytes - 1),
        ]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $redirectUrl = (string) curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            return ['ok' => false, 'error' => $curlErr !== '' ? "Could not reach that site ($curlErr)." : 'Could not reach that site.'];
        }

        if (in_array($status, [301, 302, 303, 307, 308], true)) {
            if ($redirectUrl === '') {
                return ['ok' => false, 'error' => 'That URL redirected without a destination.'];
            }
            $current = analyze_resolve_redirect($current, $redirectUrl);
            continue;
        }

        if ($status < 200 || $status >= 300) {
            return ['ok' => false, 'error' => "That page returned a $status response — check the URL and try again."];
        }

        if ($contentType !== '' && !str_contains(strtolower($contentType), 'html') && !str_contains(strtolower($contentType), 'text/plain')) {
            return ['ok' => false, 'error' => "That URL doesn't look like a webpage ($contentType)."];
        }

        return ['ok' => true, 'html' => (string) $body, 'final_url' => $current, 'error' => ''];
    }

    return ['ok' => false, 'error' => 'Too many redirects.'];
}

/** Parses raw HTML into the structured content the scoring/audience/rebuild steps need. */
function analyze_extract_content(string $html): array
{
    $doc = new DOMDocument();
    $prevSetting = libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_NOWARNING | LIBXML_NOERROR);
    libxml_clear_errors();
    libxml_use_internal_errors($prevSetting);

    $textOf = static function (DOMNode $node): string {
        return trim(preg_replace('/\s+/', ' ', $node->textContent));
    };

    $title = '';
    $titleNodes = $doc->getElementsByTagName('title');
    if ($titleNodes->length > 0) {
        $title = trim(preg_replace('/\s+/', ' ', $titleNodes->item(0)->textContent));
    }

    $description = '';
    $viewport = false;
    foreach ($doc->getElementsByTagName('meta') as $meta) {
        $name = strtolower((string) $meta->getAttribute('name'));
        $property = strtolower((string) $meta->getAttribute('property'));
        if (($name === 'description' || $property === 'og:description') && $description === '') {
            $description = trim((string) $meta->getAttribute('content'));
        }
        if ($name === 'viewport') {
            $viewport = true;
        }
    }

    $favicon = false;
    foreach ($doc->getElementsByTagName('link') as $link) {
        if (str_contains(strtolower((string) $link->getAttribute('rel')), 'icon')) {
            $favicon = true;
        }
    }

    // Strip script/style noise before pulling body text, so word counts
    // and keyword matching reflect real page content, not boilerplate.
    foreach (['script', 'style', 'noscript'] as $tag) {
        $nodes = $doc->getElementsByTagName($tag);
        for ($i = $nodes->length - 1; $i >= 0; $i--) {
            $node = $nodes->item($i);
            $node->parentNode?->removeChild($node);
        }
    }

    $h1s = [];
    foreach ($doc->getElementsByTagName('h1') as $h1) {
        $t = $textOf($h1);
        if ($t !== '') {
            $h1s[] = $t;
        }
    }

    $sections = [];
    foreach (['h2', 'h3'] as $tag) {
        foreach ($doc->getElementsByTagName($tag) as $heading) {
            $headingText = $textOf($heading);
            if ($headingText === '' || mb_strlen($headingText) > 90) {
                continue;
            }
            $excerpt = '';
            $sibling = $heading->nextSibling;
            $hops = 0;
            while ($sibling && $excerpt === '' && $hops < 6) {
                if ($sibling instanceof DOMElement && !in_array(strtolower($sibling->tagName), ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
                    $t = $textOf($sibling);
                    if ($t !== '') {
                        $excerpt = $t;
                    }
                }
                $sibling = $sibling->nextSibling;
                $hops++;
            }
            $sections[] = ['heading' => $headingText, 'text' => mb_substr($excerpt, 0, 220)];
        }
    }

    $images = $doc->getElementsByTagName('img');
    $imageCount = $images->length;
    $imagesWithAlt = 0;
    foreach ($images as $img) {
        if (trim((string) $img->getAttribute('alt')) !== '') {
            $imagesWithAlt++;
        }
    }

    $ctaPhrases = ['book a', 'book now', 'get started', 'start free', 'free trial', 'contact us', 'buy now', 'shop now', 'sign up', 'request a', 'get a quote', 'schedule a', 'download', 'subscribe', 'get in touch', 'talk to', 'call now', 'order now', 'apply now', 'join now', 'consultation'];
    $clickables = [];
    foreach (['a', 'button'] as $tag) {
        foreach ($doc->getElementsByTagName($tag) as $node) {
            $t = $textOf($node);
            if ($t !== '') {
                $clickables[] = $t;
            }
        }
    }
    $ctaFound = [];
    $ctaEarly = false;
    $earlyCutoff = max(1, (int) (count($clickables) * 0.4));
    foreach ($clickables as $i => $t) {
        $lower = strtolower($t);
        foreach ($ctaPhrases as $phrase) {
            if (str_contains($lower, $phrase)) {
                $ctaFound[] = $t;
                if ($i < $earlyCutoff) {
                    $ctaEarly = true;
                }
                break;
            }
        }
    }
    $ctaFound = array_values(array_unique($ctaFound));

    $bodyText = '';
    $body = $doc->getElementsByTagName('body')->item(0);
    if ($body) {
        $bodyText = $textOf($body);
    }
    $wordCount = str_word_count($bodyText);

    $hasPhone = (bool) preg_match('/(\+?\d[\d\-\s\(\)]{7,}\d)/', $bodyText);
    $hasEmail = (bool) preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $bodyText);

    $trustKeywords = ['testimonial', 'trusted by', 'client', 'customers', 'review', 'rated', 'guarantee', 'award', 'certified', 'case study', 'partner'];
    $lowerBody = strtolower($bodyText);
    $trustHits = 0;
    foreach ($trustKeywords as $kw) {
        if (str_contains($lowerBody, $kw)) {
            $trustHits++;
        }
    }

    preg_match_all('/\b(\d[\d,]{1,7}\+?\s?(?:%|customers|clients|projects|years|reviews|users|businesses)|\d(?:\.\d)?\s?\/\s?5|\d(?:\.\d)?\s?stars?)\b/i', $bodyText, $proofMatches);
    $socialProof = array_slice(array_values(array_unique($proofMatches[0] ?? [])), 0, 3);

    $formFieldCounts = [];
    foreach ($doc->getElementsByTagName('form') as $form) {
        $count = 0;
        foreach (['input', 'textarea', 'select'] as $tag) {
            foreach ($form->getElementsByTagName($tag) as $field) {
                if (!in_array(strtolower((string) $field->getAttribute('type')), ['hidden', 'submit', 'button'], true)) {
                    $count++;
                }
            }
        }
        if ($count > 0) {
            $formFieldCounts[] = $count;
        }
    }

    $externalResourceCount = $doc->getElementsByTagName('script')->length + $doc->getElementsByTagName('link')->length;

    return [
        'title' => $title,
        'description' => $description,
        'viewport' => $viewport,
        'favicon' => $favicon,
        'h1s' => $h1s,
        'sections' => array_slice($sections, 0, 8),
        'image_count' => $imageCount,
        'images_with_alt' => $imagesWithAlt,
        'cta_found' => $ctaFound,
        'cta_early' => $ctaEarly,
        'word_count' => $wordCount,
        'has_phone' => $hasPhone,
        'has_email' => $hasEmail,
        'trust_hits' => $trustHits,
        'social_proof' => $socialProof,
        'form_field_counts' => $formFieldCounts,
        'external_resource_count' => $externalResourceCount,
        'body_text' => mb_substr($bodyText, 0, 4000),
        'html_bytes' => strlen($html),
    ];
}

/**
 * Scores extracted content against a fixed CRO checklist. Every check
 * carries a title + reason usable both as "kept as-is" praise and as a
 * "what changed" explanation, so the same list powers the score and the
 * report's reasoning — no separate free-text generation involved.
 */
function analyze_score(array $e): array
{
    $checks = [];

    $h1Count = count($e['h1s']);
    if ($h1Count === 1) {
        $words = str_word_count($e['h1s'][0]);
        if ($words > 0 && $words <= 12) {
            $checks[] = ['category' => 'Clarity', 'points' => 40, 'earned' => 40,
                'title' => 'One clear, scannable headline',
                'reason' => 'A single H1 under 12 words lets visitors understand the page in seconds. Kept as-is in the rebuild.'];
        } else {
            $checks[] = ['category' => 'Clarity', 'points' => 40, 'earned' => 15,
                'title' => 'Headline was hard to scan',
                'reason' => "The original H1 ran to $words words. The rebuild opens with a tightened one-line headline and a separate supporting subheadline, so the core message reads in under 3 seconds."];
        }
    } else {
        $checks[] = ['category' => 'Clarity', 'points' => 40, 'earned' => 0,
            'title' => $h1Count === 0 ? 'No H1 headline found' : "Multiple H1s found ($h1Count)",
            'reason' => 'Visitors and search engines both rely on one clear H1 to know what a page is about. The rebuild uses exactly one.'];
    }

    $descLen = mb_strlen($e['description']);
    if ($descLen >= 50 && $descLen <= 160) {
        $checks[] = ['category' => 'Clarity', 'points' => 30, 'earned' => 30,
            'title' => 'Meta description is a healthy length',
            'reason' => 'A 50-160 character description shows fully in search results and social shares. Kept as-is.'];
    } elseif ($descLen > 0) {
        $checks[] = ['category' => 'Clarity', 'points' => 30, 'earned' => 12,
            'title' => 'Meta description needs trimming',
            'reason' => "At $descLen characters, it will get cut off in search results or social previews. The rebuild uses a tightened subheadline instead."];
    } else {
        $checks[] = ['category' => 'Clarity', 'points' => 30, 'earned' => 0,
            'title' => 'No meta description found',
            'reason' => 'Without one, Google writes its own snippet — usually a worse first impression than one you control. The rebuild adds a clear subheadline in its place.'];
    }

    if (count($e['sections']) >= 2) {
        $checks[] = ['category' => 'Clarity', 'points' => 30, 'earned' => 30,
            'title' => 'Content is broken into clear sections',
            'reason' => 'Subheadings let visitors scan instead of reading a wall of text. Kept as-is.'];
    } else {
        $checks[] = ['category' => 'Clarity', 'points' => 30, 'earned' => 8,
            'title' => 'Little to no section structure',
            'reason' => 'The page reads as one long block rather than scannable sections. The rebuild breaks your content into a clear card grid.'];
    }

    if (!empty($e['cta_found'])) {
        $checks[] = ['category' => 'CTA Strength', 'points' => 55, 'earned' => 55,
            'title' => 'Action-oriented call to action found',
            'reason' => 'Phrases like "' . $e['cta_found'][0] . '" give visitors an obvious next step. Kept and made more prominent.'];
    } else {
        $checks[] = ['category' => 'CTA Strength', 'points' => 55, 'earned' => 0,
            'title' => 'No clear call to action found',
            'reason' => 'Without action-oriented copy (e.g. "Book a Call", "Get Started"), visitors have no obvious next step. The rebuild adds a prominent CTA button in the hero.'];
    }
    if ($e['cta_early']) {
        $checks[] = ['category' => 'CTA Strength', 'points' => 45, 'earned' => 45,
            'title' => 'Call to action appears early',
            'reason' => 'The CTA shows up before visitors have to scroll far. Kept above the fold.'];
    } else {
        $checks[] = ['category' => 'CTA Strength', 'points' => 45, 'earned' => 0,
            'title' => 'Call to action was buried',
            'reason' => 'The strongest CTA appeared late on the page, after most visitors have already decided whether to stay. The rebuild puts one directly in the hero, above the fold.'];
    }

    $contactPoints = ($e['has_phone'] ? 1 : 0) + ($e['has_email'] ? 1 : 0);
    if ($contactPoints > 0) {
        $checks[] = ['category' => 'Trust Signals', 'points' => 35, 'earned' => 35,
            'title' => 'Contact information is visible',
            'reason' => 'A visible phone number or email reduces hesitation for visitors close to converting. Kept as-is.'];
    } else {
        $checks[] = ['category' => 'Trust Signals', 'points' => 35, 'earned' => 0,
            'title' => 'No visible contact information',
            'reason' => 'No phone number or email was found in the page text. The rebuild surfaces a direct contact CTA instead of leaving visitors to search for one.'];
    }
    if ($e['trust_hits'] > 0) {
        $checks[] = ['category' => 'Trust Signals', 'points' => 35, 'earned' => 35,
            'title' => 'Trust language present',
            'reason' => 'Words like reviews, clients, or guarantee build credibility before asking for the sale. Kept as-is.'];
    } else {
        $checks[] = ['category' => 'Trust Signals', 'points' => 35, 'earned' => 5,
            'title' => 'Little credibility language',
            'reason' => 'No testimonial, review, or guarantee language was detected. Worth adding real client proof — the rebuild reserves a section for it.'];
    }
    if (!empty($e['social_proof'])) {
        $checks[] = ['category' => 'Trust Signals', 'points' => 30, 'earned' => 30,
            'title' => 'Social proof numbers found',
            'reason' => 'Specific numbers ("' . $e['social_proof'][0] . '") are more persuasive than vague claims. Highlighted as stat callouts in the rebuild.'];
    } else {
        $checks[] = ['category' => 'Trust Signals', 'points' => 30, 'earned' => 0,
            'title' => 'No specific numbers to prove scale',
            'reason' => 'Nothing like "500+ clients" or "4.9/5 rating" was found. Numbers are more convincing than adjectives — worth adding real ones.'];
    }

    if ($e['viewport']) {
        $checks[] = ['category' => 'Mobile & Speed', 'points' => 40, 'earned' => 40,
            'title' => 'Mobile viewport configured',
            'reason' => 'The page tells mobile browsers how to scale correctly. Kept as-is.'];
    } else {
        $checks[] = ['category' => 'Mobile & Speed', 'points' => 40, 'earned' => 0,
            'title' => 'No mobile viewport tag',
            'reason' => 'Without it, most mobile browsers render a zoomed-out desktop layout — a common mobile conversion killer on its own.'];
    }
    if ($e['favicon']) {
        $checks[] = ['category' => 'Mobile & Speed', 'points' => 15, 'earned' => 15,
            'title' => 'Favicon present', 'reason' => 'A small polish signal that the site is actively maintained. Kept as-is.'];
    } else {
        $checks[] = ['category' => 'Mobile & Speed', 'points' => 15, 'earned' => 0,
            'title' => 'No favicon found', 'reason' => 'A missing browser-tab icon is a small but easy-to-fix polish gap.'];
    }
    if ($e['html_bytes'] < 500000) {
        $checks[] = ['category' => 'Mobile & Speed', 'points' => 25, 'earned' => 25,
            'title' => 'Reasonable page weight',
            'reason' => 'A lean HTML payload loads faster, especially on mobile networks. Kept lean in the rebuild.'];
    } else {
        $kb = round($e['html_bytes'] / 1024);
        $checks[] = ['category' => 'Mobile & Speed', 'points' => 25, 'earned' => 8,
            'title' => "Heavy page ({$kb}KB of HTML)",
            'reason' => 'Larger pages load slower, and every extra second of load time measurably increases bounce rate. The rebuild ships a lighter page.'];
    }
    if ($e['external_resource_count'] <= 15) {
        $checks[] = ['category' => 'Mobile & Speed', 'points' => 20, 'earned' => 20,
            'title' => 'Reasonable number of external resources',
            'reason' => 'Fewer scripts and stylesheets mean fewer render-blocking requests. Kept lean.'];
    } else {
        $checks[] = ['category' => 'Mobile & Speed', 'points' => 20, 'earned' => 5,
            'title' => $e['external_resource_count'] . ' external scripts/stylesheets loaded',
            'reason' => 'Each one is a separate network request before the page can finish rendering. The rebuild uses a single lightweight stylesheet.'];
    }

    $wc = $e['word_count'];
    if ($wc >= 150 && $wc <= 2500) {
        $checks[] = ['category' => 'Content Structure', 'points' => 40, 'earned' => 40,
            'title' => "Healthy content length ($wc words)",
            'reason' => 'Enough to explain the offer without overwhelming visitors. Kept as-is.'];
    } elseif ($wc < 150) {
        $checks[] = ['category' => 'Content Structure', 'points' => 40, 'earned' => 10,
            'title' => "Very little content ($wc words)",
            'reason' => 'Thin pages give visitors and search engines little to work with. The rebuild expands on the value proposition.'];
    } else {
        $checks[] = ['category' => 'Content Structure', 'points' => 40, 'earned' => 18,
            'title' => "Dense page ($wc words)",
            'reason' => 'A lot of text on one page can overwhelm visitors. The rebuild organizes it into scannable sections instead of long paragraphs.'];
    }
    if ($e['image_count'] === 0) {
        $checks[] = ['category' => 'Content Structure', 'points' => 30, 'earned' => 20,
            'title' => 'No images on the page',
            'reason' => 'Not necessarily a problem, but visuals typically increase engagement and time on page.'];
    } else {
        $altPct = (int) round(($e['images_with_alt'] / $e['image_count']) * 100);
        if ($altPct >= 70) {
            $checks[] = ['category' => 'Content Structure', 'points' => 30, 'earned' => 30,
                'title' => "Most images have alt text ($altPct%)", 'reason' => 'Good for accessibility and image search. Kept as-is.'];
        } else {
            $checks[] = ['category' => 'Content Structure', 'points' => 30, 'earned' => 10,
                'title' => "Only $altPct% of images have alt text",
                'reason' => 'Missing alt text hurts accessibility and image SEO. Worth adding descriptive alt text to every image.'];
        }
    }
    $maxFormFields = $e['form_field_counts'] ? max($e['form_field_counts']) : 0;
    if ($maxFormFields === 0 || $maxFormFields <= 6) {
        $checks[] = ['category' => 'Content Structure', 'points' => 30, 'earned' => 30,
            'title' => $maxFormFields === 0 ? 'No long forms to worry about' : "Form length is reasonable ($maxFormFields fields)",
            'reason' => 'Short forms convert better — every extra field measurably reduces completion rate. Kept as-is.'];
    } else {
        $checks[] = ['category' => 'Content Structure', 'points' => 30, 'earned' => 10,
            'title' => "Long form ($maxFormFields fields)",
            'reason' => 'Every additional form field reduces completion rate. The rebuild trims the form to the essentials — name, email, and one qualifying question.'];
    }

    $categories = [];
    foreach ($checks as $c) {
        $categories[$c['category']]['points'] = ($categories[$c['category']]['points'] ?? 0) + $c['points'];
        $categories[$c['category']]['earned'] = ($categories[$c['category']]['earned'] ?? 0) + $c['earned'];
    }
    $subScores = [];
    foreach ($categories as $name => $vals) {
        $subScores[$name] = $vals['points'] > 0 ? (int) round(($vals['earned'] / $vals['points']) * 100) : 0;
    }
    $overall = $subScores ? (int) round(array_sum($subScores) / count($subScores)) : 0;

    return ['overall' => $overall, 'sub_scores' => $subScores, 'checks' => $checks];
}

/** Keyword-matches extracted text against a fixed set of audience archetypes. */
function analyze_infer_audience(array $e): array
{
    $text = strtolower($e['title'] . ' ' . $e['description'] . ' ' . implode(' ', $e['h1s']) . ' ' . implode(' ', array_column($e['sections'], 'heading')) . ' ' . $e['body_text']);

    $map = [
        'Ecommerce & Retail' => ['shop', 'cart', 'checkout', 'add to cart', 'free shipping', 'product', 'sizes', 'collection', 'buy now', 'sale price'],
        'SaaS & Technology' => ['software', 'dashboard', 'api', 'integration', 'free trial', 'pricing plan', 'sign up', 'cloud', 'platform', 'subscription', 'saas'],
        'Healthcare & Wellness' => ['patient', 'clinic', 'doctor', 'appointment', 'treatment', 'therapy', 'wellness', 'physician', 'diagnosis'],
        'Construction & Real Estate' => ['construction', 'real estate', 'contractor', 'builder', 'renovation', 'property', 'site management', 'architect'],
        'Professional Services & Agency' => ['agency', 'consulting', 'our clients', 'portfolio', 'case study', 'marketing services', 'strategy', 'campaign'],
        'Education & Training' => ['course', 'curriculum', 'students', 'enroll', 'certificate', 'training program', 'instructor', 'syllabus'],
        'Hospitality & Food' => ['restaurant', 'menu', 'reservation', 'hotel', 'cuisine', 'dine', 'chef', 'book a table'],
        'Finance & Insurance' => ['insurance', 'loan', 'investment', 'policy', 'premium', 'wealth management', 'mortgage', 'bank account'],
        'Manufacturing & Industrial' => ['manufacturing', 'factory', 'industrial', 'supply chain', 'machinery', 'production line', 'wholesale', 'oem'],
    ];

    $scores = [];
    foreach ($map as $audience => $keywords) {
        $hits = 0;
        foreach ($keywords as $kw) {
            if (str_contains($text, $kw)) {
                $hits++;
            }
        }
        if ($hits > 0) {
            $scores[$audience] = $hits;
        }
    }

    if (!$scores) {
        return ['audience' => 'General Business', 'match_score' => 45];
    }

    arsort($scores);
    $top = array_key_first($scores);
    $total = array_sum($scores);
    $matchScore = (int) max(50, min(96, round(($scores[$top] / $total) * 100)));

    return ['audience' => $top, 'match_score' => $matchScore];
}

/** Builds the Tab 1 "rebuilt page" content set from the site's own extracted copy. */
function analyze_build_new_page(array $e, array $audience): array
{
    $headline = $e['h1s'][0] ?? ($e['title'] ?: 'Your Business, Reimagined');
    $subheadline = $e['description'] ?: (mb_substr($e['body_text'], 0, 180) ?: 'A clear, focused page that gives visitors one obvious next step.');

    $ctaText = 'Get Started Today';
    if (!empty($e['cta_found'])) {
        $candidate = ucwords(strtolower($e['cta_found'][0]));
        if (mb_strlen($candidate) <= 30) {
            $ctaText = $candidate;
        }
    }

    $cards = [];
    foreach (array_slice($e['sections'], 0, 6) as $s) {
        if ($s['heading'] === '') {
            continue;
        }
        $cards[] = ['title' => $s['heading'], 'text' => $s['text'] !== '' ? $s['text'] : 'Learn more about this.'];
    }

    return [
        'headline' => mb_substr($headline, 0, 140),
        'subheadline' => mb_substr($subheadline, 0, 220),
        'cta_text' => $ctaText,
        'cards' => $cards,
        'trust_stats' => $e['social_proof'],
        'audience' => $audience['audience'],
    ];
}

/** Orchestrates fetch -> extract -> score -> audience -> rebuild for a submitted URL. */
function analyze_run(string $url): array
{
    $fetch = analyze_fetch_html($url);
    if (!$fetch['ok']) {
        return ['ok' => false, 'error' => $fetch['error']];
    }

    $extracted = analyze_extract_content($fetch['html']);
    $scoring = analyze_score($extracted);
    $audience = analyze_infer_audience($extracted);
    $newPage = analyze_build_new_page($extracted, $audience);

    return [
        'ok' => true,
        'final_url' => $fetch['final_url'],
        'title' => $extracted['title'],
        'description' => $extracted['description'],
        'score' => $scoring['overall'],
        'sub_scores' => $scoring['sub_scores'],
        'checks' => $scoring['checks'],
        'audience' => $audience['audience'],
        'audience_match' => $audience['match_score'],
        'new_page' => $newPage,
    ];
}

function analyze_save_report(PDO $pdo, string $url, array $result): string
{
    $token = analyze_generate_token();
    $stmt = $pdo->prepare(
        'INSERT INTO analyze_reports
         (token, target_url, page_title, page_description, cro_score, sub_scores, target_audience, audience_match_score, changes_json, new_page_json)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $token,
        mb_substr($url, 0, 490),
        mb_substr($result['title'], 0, 290),
        mb_substr($result['description'], 0, 490),
        $result['score'],
        json_encode($result['sub_scores']),
        $result['audience'],
        $result['audience_match'],
        json_encode($result['checks']),
        json_encode($result['new_page']),
    ]);
    return $token;
}

function analyze_get_report(PDO $pdo, string $token): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM analyze_reports WHERE token = ?');
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    $row['sub_scores'] = json_decode((string) $row['sub_scores'], true) ?: [];
    $row['checks'] = json_decode((string) $row['changes_json'], true) ?: [];
    $row['new_page'] = json_decode((string) $row['new_page_json'], true) ?: [];
    return $row;
}
