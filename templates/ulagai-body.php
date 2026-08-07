<?php
/**
 * Ulagai — a dark/violet ecommerce-agency-style landing hero, built to
 * match a supplied reference screenshot as closely as possible. A
 * completely different visual system from the rest of the site, so all
 * styling is scoped under .ul- in this page-local <style> block —
 * same pattern already used by Home 2.0 and /analyze.
 *
 * The glow-arc graphic behind the hero is a CSS/SVG approximation of the
 * reference image (radial gradients + blurred arcs), not a pixel
 * extraction — that source graphic looks generative/rendered and can't
 * be lifted exactly from a screenshot.
 */
$activePage = 'ulagai';
include __DIR__ . '/partials/nav.php';
?>
<style>
.ul-page{background:#07070f}
.ul-hero{
 position:relative;overflow:hidden;padding:7.5rem 1.5rem 2rem;
 background:radial-gradient(ellipse 900px 500px at 50% -10%,#171733 0%,#0a0a18 55%,#07070f 100%);
 text-align:center;
}
.ul-badge{
 display:inline-flex;align-items:center;gap:8px;margin:0 auto 2rem;padding:11px 26px;
 background:#16161f;border:1px solid rgba(255,255,255,.1);border-radius:999px;
 font-size:11.5px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:#c7c7d6;
}
.ul-h1{
 max-width:920px;margin:0 auto 1.3rem;font-size:clamp(30px,4.6vw,52px);font-weight:800;
 letter-spacing:-.02em;line-height:1.18;color:#fff;
}
.ul-highlight{
 display:inline-block;background:#7c5cf6;color:#fff;padding:.1em .3em;border-radius:10px;
}
.ul-sub{max-width:640px;margin:0 auto .9rem;font-size:clamp(16px,2vw,21px);font-weight:500;color:rgba(255,255,255,.88);line-height:1.5}
.ul-sub2{margin:0 auto 2.4rem;font-size:15px;font-weight:700;color:rgba(255,255,255,.82)}
.ul-btns{display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;position:relative;z-index:2;margin-bottom:1rem}
.ul-btn-primary,.ul-btn-secondary{
 font-family:var(--font);font-size:14.5px;font-weight:700;letter-spacing:-.005em;
 padding:14px 26px;border-radius:9px;text-decoration:none;cursor:pointer;border:none;
 display:inline-flex;align-items:center;gap:8px;transition:transform .15s,opacity .15s;
}
.ul-btn-primary{background:#fff;color:#0a0a14}
.ul-btn-primary:hover{transform:translateY(-1px);opacity:.92}
.ul-btn-secondary{background:rgba(255,255,255,.08);color:#fff;border:1px solid rgba(255,255,255,.12)}
.ul-btn-secondary:hover{background:rgba(255,255,255,.13)}

.ul-arc{position:relative;height:340px;margin-top:2rem}
.ul-arc-glow{
 position:absolute;left:50%;bottom:6%;transform:translateX(-50%);width:760px;max-width:92vw;height:340px;
 background:radial-gradient(ellipse 50% 55% at 50% 100%,rgba(255,255,255,.9) 0%,rgba(168,124,255,.75) 22%,rgba(124,92,246,.4) 45%,transparent 72%);
 filter:blur(6px);pointer-events:none;
}
.ul-arc-svg{position:absolute;left:50%;bottom:6%;transform:translateX(-50%);width:760px;max-width:92vw;height:340px}

.ul-industries{background:#0a0a14;padding:3.5rem 0 4.5rem;border-top:1px solid rgba(255,255,255,.05)}
.ul-ind-title{text-align:center;color:#fff;font-size:19px;font-weight:700;margin-bottom:2.2rem}
.ul-ind-track-wrap{overflow:hidden}
.ul-ind-track{display:flex;width:max-content;animation:ulMarquee 32s linear infinite;gap:1.1rem;padding:0 1.1rem}
.ul-ind-card{
 display:flex;align-items:center;gap:14px;white-space:nowrap;flex-shrink:0;
 background:#12121d;border:1px solid rgba(255,255,255,.07);border-radius:16px;padding:1.3rem 1.9rem;
}
.ul-ind-card svg{flex-shrink:0;width:24px;height:24px;color:#a78bfa}
.ul-ind-card span{font-size:16.5px;font-weight:600;color:#fff}
@keyframes ulMarquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}

@media(max-width:640px){
 .ul-hero{padding-top:6.5rem}
 .ul-btns{flex-direction:column;width:100%}
 .ul-btn-primary,.ul-btn-secondary{width:100%;justify-content:center}
 .ul-arc{height:220px}
 .ul-arc-glow,.ul-arc-svg{height:220px}
}
</style>

<div class="ul-page">
 <section class="ul-hero">
  <div class="ul-badge">Built for Orders</div>
  <h1 class="ul-h1">We Engineer High-Performance <span class="ul-highlight">Ecommerce Stores</span></h1>
  <p class="ul-sub">Designed To Convert Traffic Into Consistent Online Orders.</p>
  <p class="ul-sub2">For scaling D2C brands serious about growth</p>
  <div class="ul-btns">
   <button type="button" data-book class="ul-btn-primary">Apply to Work With Us</button>
   <a href="/case-studies" class="ul-btn-secondary">View Case Studies</a>
  </div>

  <div class="ul-arc" aria-hidden="true">
   <div class="ul-arc-glow"></div>
   <svg class="ul-arc-svg" viewBox="0 0 760 340" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
     <linearGradient id="ulArcGrad1" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0%" stop-color="#7c5cf6" stop-opacity="0"/>
      <stop offset="50%" stop-color="#cbb8ff" stop-opacity=".9"/>
      <stop offset="100%" stop-color="#7c5cf6" stop-opacity="0"/>
     </linearGradient>
     <linearGradient id="ulArcGrad2" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0%" stop-color="#7c5cf6" stop-opacity="0"/>
      <stop offset="50%" stop-color="#ffffff" stop-opacity=".95"/>
      <stop offset="100%" stop-color="#7c5cf6" stop-opacity="0"/>
     </linearGradient>
    </defs>
    <path d="M40 320 C 40 150, 220 40, 380 40 C 540 40, 720 150, 720 320" stroke="url(#ulArcGrad1)" stroke-width="2"/>
    <path d="M120 320 C 120 190, 250 90, 380 90 C 510 90, 640 190, 640 320" stroke="url(#ulArcGrad1)" stroke-width="2"/>
    <path d="M210 320 C 210 230, 290 150, 380 150 C 470 150, 550 230, 550 320" stroke="url(#ulArcGrad2)" stroke-width="2.5"/>
   </svg>
  </div>
 </section>

 <section class="ul-industries">
  <h2 class="ul-ind-title">Industries We Serve</h2>
  <div class="ul-ind-track-wrap">
   <div class="ul-ind-track">
    <?php
    $ulIndustries = [
        ['label' => 'Health & Supplements', 'icon' => '<path d="M6.5 17.5 17.5 6.5a5 5 0 0 1 7 7L13.5 24.5a5 5 0 0 1-7-7Z"/><path d="M10 10 14 14" stroke-linecap="round"/>'],
        ['label' => 'Fitness & Wellness', 'icon' => '<path d="M4 12h2M18 12h2M8 9v6M16 9v6M8 12h8" stroke-linecap="round" stroke-linejoin="round"/><rect x="2" y="9" width="3" height="6" rx="1"/><rect x="19" y="9" width="3" height="6" rx="1"/>'],
        ['label' => 'Home & Living', 'icon' => '<path d="M4 11 12 4l8 7" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 10v9a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-9" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 20v-5h4v5" stroke-linecap="round" stroke-linejoin="round"/>'],
        ['label' => 'Jewelry & Luxury', 'icon' => '<path d="M3 8l4-4h10l4 4-9 13z" stroke-linejoin="round"/><path d="M3 8h18M9.5 4 8 8l4 13 4-13-1.5-4" stroke-linejoin="round"/>'],
        ['label' => 'Baby & Kids', 'icon' => '<circle cx="12" cy="12" r="9"/><path d="M9 10h.01M15 10h.01" stroke-linecap="round"/><path d="M8.5 15c1 1 2.2 1.5 3.5 1.5s2.5-.5 3.5-1.5" stroke-linecap="round"/>'],
        ['label' => 'Pet Products', 'icon' => '<circle cx="12" cy="15.5" r="3.2"/><circle cx="6" cy="9" r="1.9"/><circle cx="18" cy="9" r="1.9"/><circle cx="9.5" cy="5.5" r="1.7"/><circle cx="14.5" cy="5.5" r="1.7"/>'],
    ];
    foreach (array_merge($ulIndustries, $ulIndustries) as $ind):
    ?>
    <div class="ul-ind-card">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><?= $ind['icon'] ?></svg>
      <span><?= h($ind['label']) ?></span>
    </div>
    <?php endforeach; ?>
   </div>
  </div>
 </section>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
