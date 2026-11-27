<?php
/**
 * The big "Drawlead" wordmark that opens every footer. Styles live in
 * assets/footer-signoff.css (linked from layout-start.php).
 *
 * Each letter is its own span so it can lift and brighten to pure white
 * under the pointer. The word is decorative (the footer logo already names the brand),
 * so it is hidden from screen readers; the line under it is read normally.
 *
 * $signClass: extra classes from the including footer. The dark footers
 * (Home 10, About, blog) pass 'wrap' for the page width; the white footer in
 * partials/footer.php passes 'dl-sign--light'.
 */
$signClass = trim('dl-sign ' . ($signClass ?? ''));
?>
<div class="<?= h($signClass) ?>">
  <div class="dl-sign__w" aria-hidden="true"><canvas class="dl-rk dl-rk--back"></canvas><?php foreach (str_split('Drawlead') as $signLetter): ?><span><?= $signLetter ?></span><?php endforeach; ?><canvas class="dl-rk dl-rk--front"></canvas></div>
  <div class="dl-sign__l"><b>Business Systems &amp; Technology.</b> <span>Built around the way businesses work.</span></div>
</div>
<script src="<?= asset_url('/assets/footer-rocket.js') ?>" defer></script>
<?php unset($signClass, $signLetter); ?>
