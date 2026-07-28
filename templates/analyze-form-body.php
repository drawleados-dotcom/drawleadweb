<?php
/**
 * Drawlead Analyze — URL input page. POST handling (fetch, score, save,
 * redirect to /analyze/{token}) happens in index.php before this
 * template is included; $analyzeError and $analyzeUrlValue come from
 * there.
 */
$activePage = 'analyze';
include __DIR__ . '/partials/nav.php';
?>

<section id="az-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Drawlead Analyze</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:760px">Is your website built to <span class="g">convert</span>?</h1>
 <p class="sec-sub rv" style="max-width:600px">Paste your URL. Get a free CRO scorecard, your likely target audience, and a rebuilt version of your page in a modern, high-converting layout — in under a minute.</p>

 <form method="post" action="/analyze" class="az-form-card rv" novalidate>
  <?= csrf_field() ?>
  <?php if ($analyzeError !== ''): ?>
  <div class="az-error"><?= h($analyzeError) ?></div>
  <?php endif; ?>
  <div class="az-input-row">
   <input type="text" name="url" class="az-url-input" placeholder="yourbusiness.com" value="<?= h($analyzeUrlValue) ?>" autocomplete="url" required>
   <button type="submit" class="btn btn-black az-submit">Analyze My Site →</button>
  </div>
  <div class="az-form-hint">Free. No signup. We only read your page's public HTML — nothing is installed or changed.</div>
 </form>
</section>

<section id="az-how">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">How It Works</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Three steps. <span class="g">One minute.</span></h2>

 <div class="h2-steps rv" style="max-width:900px">
  <div class="h2-step">
   <div class="h2-step-n">01</div>
   <div class="h2-step-name">Enter Your URL</div>
   <div class="h2-step-desc">We read your page's public HTML — headline, copy, images, forms, and calls to action.</div>
  </div>
  <div class="h2-step">
   <div class="h2-step-n">02</div>
   <div class="h2-step-name">Get Scored</div>
   <div class="h2-step-desc">A fixed CRO checklist scores clarity, CTA strength, trust signals, mobile readiness, and content structure.</div>
  </div>
  <div class="h2-step">
   <div class="h2-step-n">03</div>
   <div class="h2-step-name">See The Rebuild</div>
   <div class="h2-step-desc">Your own headline and copy, dropped into a modern, conversion-focused layout — with a full reasoning report.</div>
  </div>
 </div>

 <div class="sec-cta rv" style="margin-top:3rem">
  <button type="button" data-book class="btn btn-outline2">Want a Human to Review It Instead? →</button>
 </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
