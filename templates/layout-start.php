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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
<style>
<?php include __DIR__ . '/partials/style.php'; ?>
</style>
<link rel="stylesheet" href="<?= asset_url('/assets/palette.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/site-header.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/footer-signoff.css') ?>">
</head>
<body>
