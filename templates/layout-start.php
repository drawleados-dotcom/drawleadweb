<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($metaTitle) ?></title>
<?php if (!empty($metaDescription)): ?>
<meta name="description" content="<?= h($metaDescription) ?>">
<?php endif; ?>
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
