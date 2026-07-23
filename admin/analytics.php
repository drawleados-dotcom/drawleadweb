<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$error = '';
$success = '';

$gaId = get_setting($pdo, 'ga_measurement_id', '');
$gscTag = get_setting($pdo, 'gsc_verification_tag', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $newGaId = trim($_POST['ga_measurement_id'] ?? '');
    $newGscTag = trim($_POST['gsc_verification_tag'] ?? '');

    if ($newGaId !== '' && !preg_match('/^(G-[A-Za-z0-9]+|UA-\d+-\d+)$/', $newGaId)) {
        $error = 'That doesn\'t look like a valid GA4 Measurement ID (should look like G-XXXXXXXXXX).';
    }

    if (!$error && $newGscTag !== '') {
        // Only allow exactly one <meta name="google-site-verification" content="..."> tag through.
        if (!preg_match('/^<meta\s+[^>]*name=["\']google-site-verification["\'][^>]*>$/i', $newGscTag)) {
            $error = 'Paste the full HTML tag Google gave you — it should start with <meta name="google-site-verification" ...>';
        } else {
            $newGscTag = strip_tags($newGscTag, '<meta>');
            $newGscTag = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $newGscTag);
        }
    }

    if (!$error) {
        set_setting($pdo, 'ga_measurement_id', $newGaId);
        set_setting($pdo, 'gsc_verification_tag', $newGscTag);
        $gaId = $newGaId;
        $gscTag = $newGscTag;
        $success = 'Saved. These now apply to every page on the live site.';
    }
}

$pageTitle = 'Analytics & Search Console';
$pageSub = 'Connect Google Analytics and verify Google Search Console.';
$activeNav = 'analytics';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="post" novalidate>
  <?= csrf_field() ?>

  <div class="card">
    <div class="card-title">Google Analytics (GA4)</div>
    <div class="card-desc">
      In Google Analytics: Admin → Data Streams → your web stream → copy the <b>Measurement ID</b> (looks like <code>G-XXXXXXXXXX</code>).
      Paste it below and the tracking script is automatically added to every page.
    </div>
    <div class="field">
      <label for="ga_measurement_id">Measurement ID</label>
      <input type="text" id="ga_measurement_id" name="ga_measurement_id" placeholder="G-XXXXXXXXXX" value="<?= h($gaId) ?>">
    </div>
  </div>

  <div class="card">
    <div class="card-title">Google Search Console</div>
    <div class="card-desc">
      In Search Console: Add Property → HTML tag method → copy the full <code>&lt;meta&gt;</code> tag it gives you and paste it below to verify ownership.
    </div>
    <div class="field">
      <label for="gsc_verification_tag">Verification Meta Tag</label>
      <textarea id="gsc_verification_tag" name="gsc_verification_tag" rows="2" placeholder='<meta name="google-site-verification" content="..." />'><?= h($gscTag) ?></textarea>
    </div>
  </div>

  <div class="card">
    <button type="submit" class="btn btn-primary">Save Connections</button>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
