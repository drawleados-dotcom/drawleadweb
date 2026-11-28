<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
// Every route in index.php sets $seo before including this file. This
// fallback only protects against a route that forgets to — it should
// never be the only thing that runs in practice.
$seo = $seo ?? [
    'title' => $metaTitle ?? 'Drawlead', 'description' => $metaDescription ?? '',
    'canonical' => '', 'robots_index' => 'index', 'robots_follow' => 'follow',
    'og_title' => '', 'og_description' => '', 'og_image' => '', 'og_type' => 'website', 'schema' => null,
];
// Admin → SEO → Global Settings / Schema fill in defaults, site-wide tags and JSON-LD.
$seoUri = $uri ?? rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$seoUri = $seoUri === '' ? '/' : $seoUri;
$seo = seo_apply_globals($pdo, $seo, $seoUri);
echo seo_head_tags($seo);
echo seo_extra_head_tags($pdo, $seo, $seoUri);

// Favicon set in Admin → SEO → Global Settings (served from /uploads/media/).
$siteFavicon = seo_global($pdo)['favicon'] ?? '';
if (is_string($siteFavicon) && $siteFavicon !== '') {
    $faviconUrl = h($siteFavicon);
    echo '<link rel="icon" href="' . $faviconUrl . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . $faviconUrl . '">' . "\n";
}
?>
<?php
// Inter and Montserrat are served from this site (assets/fonts/, Google Fonts'
// own WOFF2 files; both are SIL Open Font License). One variable-font file per
// family covers every weight. Self-hosted, they come over the page's own
// connection instead of two extra ones to Google. They are not preloaded on
// purpose: on a slow phone a preload competes with the stylesheets, and text
// showed later (measured, Nov 2026). font-display:swap shows the fallback font
// until they arrive. Latin Extended (₹ and accented letters) only downloads on
// pages that use those characters.
?>
<style>
@font-face{font-family:'Inter';font-style:normal;font-weight:300 900;font-display:swap;src:url(/assets/fonts/inter-latin-ext.woff2) format('woff2');unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C4,U+2113,U+2C60-2C7F,U+A720-A7FF}
@font-face{font-family:'Inter';font-style:normal;font-weight:300 900;font-display:swap;src:url(/assets/fonts/inter-latin.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}
@font-face{font-family:'Montserrat';font-style:normal;font-weight:300 900;font-display:swap;src:url(/assets/fonts/montserrat-latin-ext.woff2) format('woff2');unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C4,U+2113,U+2C60-2C7F,U+A720-A7FF}
@font-face{font-family:'Montserrat';font-style:normal;font-weight:300 900;font-display:swap;src:url(/assets/fonts/montserrat-latin.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}
</style>
<?php
$gscTag = get_setting($pdo, 'gsc_verification_tag', '');
if ($gscTag !== '') {
    echo $gscTag . "\n";
}

$gaId = get_setting($pdo, 'ga_measurement_id', '');
// The admin's live page preview ($pcPreview, index.php) isn't a real visit.
if ($gaId !== '' && empty($pcPreview)):
?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= h($gaId) ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?= h($gaId) ?>');
</script>
<?php endif; ?>
<?php
$clarityId = get_setting($pdo, 'clarity_project_id', '');
// Same guard as GA — the admin's live page preview ($pcPreview) isn't a real visit.
if ($clarityId !== '' && empty($pcPreview)):
?>
<script type="text/javascript">
(function(c,l,a,r,i,t,y){
    c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
})(window, document, "clarity", "script", "<?= h($clarityId) ?>");
</script>
<?php endif; ?>
<?php // Site-wide base styles (it used to be inline, from templates/partials/style.php): a cached file keeps 120 KB out of every page's HTML. ?>
<link rel="stylesheet" href="<?= asset_url('/assets/site.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/palette.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/site-header.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/footer-signoff.css') ?>">
</head>
<body>
