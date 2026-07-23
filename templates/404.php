<?php $activePage = ''; include __DIR__ . '/partials/nav.php'; ?>

<section id="not-found" style="min-height:70vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding-top:9rem">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">404</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:640px">This page <span class="g">wandered off</span></h1>
 <p class="sec-sub rv">The page you're looking for doesn't exist or may have been moved.</p>
 <div class="sec-cta rv">
  <a href="/" class="btn btn-black">Back to Home →</a>
  <a href="/blog" class="btn btn-outline2">Read the Blog</a>
 </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
