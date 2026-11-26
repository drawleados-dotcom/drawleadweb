<footer class="foot">
  <?php $signClass = 'wrap'; include __DIR__ . '/footer-signoff.php'; ?>
  <div class="wrap foot__in">
    <a href="<?= h($homeUrl ?? '/') ?>" aria-label="Drawlead home"><img src="/assets/home10/logos/drawlead-white.webp" alt="Drawlead" width="153" height="30" loading="lazy"></a>
    <p>Business Systems. Digital Commerce. Intelligent Growth.</p>
    <nav aria-label="Footer navigation">
      <a href="<?= h($homeUrl ?? '/') ?>#solutions">Solutions</a>
      <a href="<?= h($homeUrl ?? '/') ?>#program">8-Week Program</a>
      <a href="<?= h($homeUrl ?? '/') ?>#case-studies">Case Studies</a>
      <a href="<?= h($homeUrl ?? '/') ?>#industries">Industries</a>
      <a href="/about-us">About Us</a>
      <a href="/blog">Blog</a>
    </nav>
  </div>
  <div class="wrap foot__base">
    <span>official@drawlead.co.in &nbsp;·&nbsp; www.drawlead.co.in &nbsp;·&nbsp; Chennai, Tamil Nadu, IN</span>
    <span>© <?= date('Y') ?> Drawlead. All rights reserved.</span>
  </div>
</footer>
