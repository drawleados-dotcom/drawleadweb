<?php
// The footer's links are Admin → Menu → Footer (includes/nav-menu.php).
require_once __DIR__ . '/../../includes/nav-menu.php';
?>
<footer>
 <?php $signClass = 'dl-sign--light'; include __DIR__ . '/footer-signoff.php'; ?>
 <div>
 <div class="ft-logo" style="background:linear-gradient(115deg,#32b46f,#14855a);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Drawlead</div>
 <div class="ft-sub">Secure · Intelligent<br>Scalable · Future-Ready</div>
 </div>
 <div class="ft-links"><?= footer_menu_links($pdo, 'links') ?></div>
 <div>
 <div class="ft-contact">
 official@drawlead.co.in<br>
 www.drawlead.co.in<br>
 Chennai, Tamil Nadu, IN
 </div>
 <div class="ft-copy">© <?= date('Y') ?> Drawlead. All rights reserved.</div>
 <?php if ($footLegal = footer_menu_links($pdo, 'legal', ' · ')): ?><div class="ft-copy ft-legal"><?= $footLegal ?></div><?php endif; ?>
 </div>
</footer>
