<?php
/**
 * Lead Source — where each lead came from.
 *
 * assets/lead-source.js remembers how a visitor arrived (referrer, UTM tags,
 * ad click ids, landing page) and the booking / WhatsApp forms send that
 * along with the lead as `source`. lead_source_save() cleans it, works out a
 * channel ("Google Ads", "Google Search", "Instagram", "Direct", ...) and
 * stores one lead_sources row per lead. Admin → Lead Source lists them.
 */

/** Every channel lead_source_classify() can return, in display order. */
const LEAD_SOURCE_CHANNELS = [
    'Google Ads', 'Google Search', 'Google Business Profile',
    'Meta Ads', 'Facebook', 'Instagram',
    'LinkedIn Ads', 'LinkedIn', 'YouTube', 'X (Twitter)', 'Other Social',
    'WhatsApp', 'Email', 'Microsoft Ads', 'Bing Search', 'Other Search',
    'AI Chat', 'Other Campaign', 'Referral', 'Direct',
];

/** Label used for leads saved before tracking existed (no lead_sources row). */
const LEAD_SOURCE_NOT_RECORDED = 'Not recorded';

/**
 * This site's other addresses, never counted as a referral. admin.drawlead.com
 * served the site until drawlead.com went live (2026-11-27) and now redirects
 * there, so a tab still open on it hands over admin.drawlead.com as the
 * referrer. Checked here as well as in assets/lead-source.js (OWN_HOSTS),
 * because browsers that stored that referrer before the fix keep sending it
 * for up to 90 days.
 */
const LEAD_SOURCE_OWN_HOSTS = ['admin.drawlead.com'];

function lead_source_str($v, int $max): string
{
    if (!is_string($v) && !is_numeric($v)) {
        return '';
    }
    $v = trim((string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', (string) $v)); // null on malformed UTF-8 → ''
    return mb_substr($v, 0, $max);
}

/** Host of a URL, lower-case and without "www.", or '' when there isn't one. */
function lead_source_host(string $url): string
{
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    return preg_replace('/^www\./', '', $host);
}

function lead_source_host_is(string $host, array $domains): bool
{
    foreach ($domains as $d) {
        if ($host === $d || str_ends_with($host, '.' . $d)) {
            return true;
        }
    }
    return false;
}

/** Clean one touch ({landing, referrer, utm_*, click_id, ts}) sent by the browser. */
function lead_source_clean_touch($t): array
{
    $t = is_array($t) ? $t : [];
    $ownHost = preg_replace('/^www\./', '', strtolower((string) ($_SERVER['HTTP_HOST'] ?? '')));
    $referrer = lead_source_str($t['referrer'] ?? '', 500);
    $refHost = lead_source_host($referrer);
    if (!preg_match('#^https?://#i', $referrer)
        || ($ownHost !== '' && $refHost === $ownHost)
        || in_array($refHost, LEAD_SOURCE_OWN_HOSTS, true)) {
        $referrer = ''; // only external referrers mean anything here
    }
    $click = strtolower(lead_source_str($t['click_id'] ?? '', 20));
    $ts = is_numeric($t['ts'] ?? null) ? (int) ($t['ts'] / 1000) : 0; // JS sends milliseconds
    return [
        'landing'      => lead_source_str($t['landing'] ?? '', 500),
        'referrer'     => $referrer,
        'utm_source'   => lead_source_str($t['utm_source'] ?? '', 190),
        'utm_medium'   => lead_source_str($t['utm_medium'] ?? '', 190),
        'utm_campaign' => lead_source_str($t['utm_campaign'] ?? '', 190),
        'utm_term'     => lead_source_str($t['utm_term'] ?? '', 190),
        'utm_content'  => lead_source_str($t['utm_content'] ?? '', 190),
        'click_id'     => in_array($click, ['gclid', 'gbraid', 'wbraid', 'fbclid', 'msclkid', 'li_fat_id', 'ttclid'], true) ? $click : '',
        'seen_at'      => ($ts > 946684800 && $ts <= time() + 86400) ? date('Y-m-d H:i:s', $ts) : null,
    ];
}

/**
 * Work out the channel for a touch. Returns [channel, detail]: the channel
 * is one of LEAD_SOURCE_CHANNELS; the detail is the referring site or
 * campaign source, for showing next to it.
 */
function lead_source_classify(array $t): array
{
    $src = strtolower($t['utm_source'] ?? '');
    $med = strtolower($t['utm_medium'] ?? '');
    $click = $t['click_id'] ?? '';
    $host = lead_source_host($t['referrer'] ?? '');
    $paid = $med !== '' && (bool) preg_match('/(cpc|ppc|paid|cpm|cpv|display|^ads?$|banner|retarget|remarket|sponsored)/', $med);
    // Short words ("fb", "ig") must match the whole source — as a substring
    // "ig" would match "digital". Longer words match anywhere ("facebook_ads").
    $srcHas = function (array $words) use ($src): bool {
        foreach ($words as $w) {
            if ($src === $w || (strlen($w) > 3 && str_contains($src, $w))) {
                return true;
            }
        }
        return false;
    };
    if (implode('', [$t['landing'] ?? '', $src, $click, $host]) === '') {
        return ['', '']; // nothing recorded for this touch
    }
    $detail = ($t['utm_source'] ?? '') !== '' ? $t['utm_source'] : $host;

    // Paid clicks first — an ad click id or a paid medium wins over everything else.
    if (in_array($click, ['gclid', 'gbraid', 'wbraid'], true) || ($srcHas(['google', 'adwords']) && $paid)) {
        return ['Google Ads', $t['utm_campaign'] ?: $detail];
    }
    if ($click === 'msclkid' || ($srcHas(['bing', 'microsoft']) && $paid)) {
        return ['Microsoft Ads', $t['utm_campaign'] ?: $detail];
    }
    if ($srcHas(['facebook', 'instagram', 'meta', 'fb', 'ig']) && $paid) {
        return ['Meta Ads', $t['utm_campaign'] ?: $detail];
    }
    if ($click === 'li_fat_id' || ($srcHas(['linkedin']) && $paid)) {
        return ['LinkedIn Ads', $t['utm_campaign'] ?: $detail];
    }

    if (preg_match('/^(gmb|gbp)$/', $med) || preg_match('/^(gmb|gbp|google_?business|google_?my_?business|business\.google)/', $src)) {
        return ['Google Business Profile', $detail];
    }
    if (preg_match('/(e-?mail|newsletter)/', $med) || $srcHas(['newsletter', 'mailchimp', 'brevo', 'sendinblue'])) {
        return ['Email', $detail];
    }
    if ($med === 'whatsapp' || $srcHas(['whatsapp']) || lead_source_host_is($host, ['wa.me', 'whatsapp.com'])) {
        return ['WhatsApp', $detail];
    }
    if ($srcHas(['chatgpt', 'openai', 'perplexity', 'gemini', 'claude', 'copilot'])
        || lead_source_host_is($host, ['chatgpt.com', 'chat.openai.com', 'perplexity.ai', 'gemini.google.com', 'claude.ai', 'copilot.microsoft.com'])) {
        return ['AI Chat', $detail];
    }

    $social = [
        'Instagram'    => [['instagram', 'ig'], ['instagram.com']],
        'Facebook'     => [['facebook', 'fb'], ['facebook.com', 'fb.com', 'fb.me']],
        'LinkedIn'     => [['linkedin'], ['linkedin.com', 'lnkd.in']],
        'YouTube'      => [['youtube'], ['youtube.com', 'youtu.be']],
        'X (Twitter)'  => [['twitter', 'x.com'], ['twitter.com', 'x.com', 't.co']],
        'Other Social' => [['pinterest', 'reddit', 'quora', 'threads', 'tiktok', 'telegram'], ['pinterest.com', 'reddit.com', 'quora.com', 'threads.net', 'tiktok.com', 't.me']],
    ];
    foreach ($social as $channel => [$words, $domains]) {
        if ($srcHas($words) || lead_source_host_is($host, $domains)) {
            return [$channel, $detail];
        }
    }
    if ($click === 'fbclid') {
        return ['Facebook', $detail]; // Facebook tags every outbound link, ads or not
    }

    if ((bool) preg_match('/^google(\.[a-z]{2,3}){1,2}$/', $host) || ($src === 'google' && $med !== '')) {
        return ['Google Search', $detail];
    }
    if (lead_source_host_is($host, ['bing.com']) || $src === 'bing') {
        return ['Bing Search', $detail];
    }
    if (lead_source_host_is($host, ['duckduckgo.com', 'yahoo.com', 'yandex.ru', 'yandex.com', 'baidu.com', 'ecosia.org', 'search.brave.com'])) {
        return ['Other Search', $detail];
    }

    if ($src !== '') {
        return ['Other Campaign', $detail];
    }
    if ($host !== '') {
        return ['Referral', $host];
    }
    return ['Direct', ''];
}

function lead_source_device(): string
{
    $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
    if ($ua === '') {
        return '';
    }
    if (preg_match('/iPad|Tablet|PlayBook|Silk|(Android(?!.*Mobile))/i', $ua)) {
        return 'Tablet';
    }
    if (preg_match('/Mobi|iPhone|iPod|Android|BlackBerry|Opera Mini|IEMobile/i', $ua)) {
        return 'Mobile';
    }
    return 'Desktop';
}

/**
 * Store where a lead came from. $source is the raw `source` object the
 * browser sent. Never throws: a missing table (migration 030 not run yet)
 * or bad data must not lose the lead itself, which is already saved.
 */
function lead_source_save(PDO $pdo, string $leadType, int $leadId, $source): void
{
    try {
        $source = is_array($source) ? $source : [];
        $last = lead_source_clean_touch($source['session'] ?? []);
        $first = lead_source_clean_touch($source['first'] ?? []);
        [$channel, $detail] = lead_source_classify($last);
        [$firstChannel, $firstDetail] = lead_source_classify($first);
        if ($channel === '') {
            $channel = 'Direct'; // browser sent nothing (script blocked/old cache) — treat as direct
        }

        $page = lead_source_str($source['page'] ?? '', 500);
        if ($page !== '' && $page[0] !== '/') {
            $page = ''; // the browser sends a path; anything else isn't ours
        }

        $stmt = $pdo->prepare(
            'INSERT INTO lead_sources
             (lead_type, lead_id, channel, source_detail, page_url, page_title, trigger_label,
              landing_page, referrer, utm_source, utm_medium, utm_campaign, utm_term, utm_content, click_id,
              first_channel, first_detail, first_landing_page, first_referrer, first_utm_campaign, first_seen_at, device)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $leadType, $leadId, $channel, mb_substr($detail, 0, 190),
            $page, lead_source_str($source['title'] ?? '', 255), lead_source_str($source['trigger'] ?? '', 190),
            $last['landing'], $last['referrer'], $last['utm_source'], $last['utm_medium'], $last['utm_campaign'],
            $last['utm_term'], $last['utm_content'], $last['click_id'],
            $firstChannel, mb_substr($firstDetail, 0, 190), $first['landing'], $first['referrer'], $first['utm_campaign'],
            $first['seen_at'], lead_source_device(),
        ]);
    } catch (Throwable $e) {
        error_log('lead_source_save: ' . $e->getMessage());
    }
}

/** SQL for the lead_sources table — shared by schema.sql and migration 030. */
function lead_source_table_sql(): string
{
    return "CREATE TABLE IF NOT EXISTS lead_sources (
        id                  INT AUTO_INCREMENT PRIMARY KEY,
        lead_type           VARCHAR(20)  NOT NULL,
        lead_id             INT          NOT NULL,
        channel             VARCHAR(60)  NOT NULL DEFAULT 'Direct',
        source_detail       VARCHAR(190) NOT NULL DEFAULT '',
        page_url            VARCHAR(500) NOT NULL DEFAULT '',
        page_title          VARCHAR(255) NOT NULL DEFAULT '',
        trigger_label       VARCHAR(190) NOT NULL DEFAULT '',
        landing_page        VARCHAR(500) NOT NULL DEFAULT '',
        referrer            VARCHAR(500) NOT NULL DEFAULT '',
        utm_source          VARCHAR(190) NOT NULL DEFAULT '',
        utm_medium          VARCHAR(190) NOT NULL DEFAULT '',
        utm_campaign        VARCHAR(190) NOT NULL DEFAULT '',
        utm_term            VARCHAR(190) NOT NULL DEFAULT '',
        utm_content         VARCHAR(190) NOT NULL DEFAULT '',
        click_id            VARCHAR(20)  NOT NULL DEFAULT '',
        first_channel       VARCHAR(60)  NOT NULL DEFAULT '',
        first_detail        VARCHAR(190) NOT NULL DEFAULT '',
        first_landing_page  VARCHAR(500) NOT NULL DEFAULT '',
        first_referrer      VARCHAR(500) NOT NULL DEFAULT '',
        first_utm_campaign  VARCHAR(190) NOT NULL DEFAULT '',
        first_seen_at       DATETIME     NULL,
        device              VARCHAR(20)  NOT NULL DEFAULT '',
        created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_lead (lead_type, lead_id),
        KEY idx_channel (channel)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}
