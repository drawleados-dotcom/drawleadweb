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
echo seo_head_tags($seo);
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<?php
$gscTag = get_setting($pdo, 'gsc_verification_tag', '');
if ($gscTag !== '') {
    echo $gscTag . "\n";
}

$gaId = get_setting($pdo, 'ga_measurement_id', '');
if ($gaId !== ''):
?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= h($gaId) ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?= h($gaId) ?>');
</script>
<?php endif; ?>
<style>
<?php include __DIR__ . '/partials/style.php'; ?>
</style>
</head>
<body>
