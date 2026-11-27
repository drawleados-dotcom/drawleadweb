<?php
// The footer's links are Admin → Menu → Footer (includes/nav-menu.php).
require_once __DIR__ . '/../../includes/nav-menu.php';
?>
<footer class="foot">
  <?php $signClass = 'wrap'; include __DIR__ . '/footer-signoff.php'; ?>
  <div class="wrap foot__in">
    <a href="<?= h($homeUrl ?? '/') ?>" aria-label="Drawlead home"><img src="/assets/home10/logos/drawlead-white.webp" alt="Drawlead" width="153" height="30" loading="lazy"></a>
    <p>Business Systems. Digital Commerce. Intelligent Growth.</p>
    <?php if ($footLinks = footer_menu_links($pdo, 'links')): ?><nav aria-label="Footer navigation"><?= $footLinks ?></nav><?php endif; ?>
  </div>
  <div class="wrap foot__base">
    <span>official@drawlead.co.in &nbsp;·&nbsp; www.drawlead.co.in &nbsp;·&nbsp; Chennai, Tamil Nadu, IN</span>
    <span>© <?= date('Y') ?> Drawlead. All rights reserved.</span>
    <?php if ($footLegal = footer_menu_links($pdo, 'legal')): ?><nav class="foot__legal" aria-label="Legal"><?= $footLegal ?></nav><?php endif; ?>
  </div>
</footer>
