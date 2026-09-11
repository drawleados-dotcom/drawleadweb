<?php $activePage = 'about-us'; include __DIR__ . '/partials/nav.php'; ?>

<!-- ═══════════════════ ABOUT HERO ═══════════════════ -->
<section id="about-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">About Drawlead</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="font-size:clamp(38px,6vw,68px);max-width:920px">Turning MSME dreams into <span class="g">digital growth systems</span></h1>
 <p class="sec-sub rv" style="max-width:640px">Drawlead is a Chennai-based digital transformation company helping MSMEs and SMEs grow through websites, performance marketing, SEO, and intelligent business systems — creating lasting online impressions that connect brands with the audiences that matter most.</p>
 <div class="sec-cta rv">
 <button type="button" data-book class="btn btn-black">Book a Free Consultation →</button>
 <a href="/#cases" class="btn btn-outline2">See Our Work</a>
 </div>
</section>

<!-- ═══════════════════ STORY ═══════════════════ -->
<section id="story">
 <div class="story-grid">
  <div class="story-copy rv">
   <div class="eyebrow" style="justify-content:flex-start"><div class="eyebrow-line"></div><span class="eyebrow-text">Our Story</span></div>
   <h2 style="font-size:clamp(26px,4vw,38px);font-weight:800;letter-spacing:-.02em;line-height:1.18;margin:1.1rem 0 1.25rem">Built for MSMEs who deserve enterprise-grade digital firepower</h2>
   <p style="font-size:14.5px;color:var(--g500);line-height:1.75;margin-bottom:1.1rem">Drawlead started with a simple belief: MSMEs and SMEs deserve the same digital firepower as large enterprises — without the enterprise price tag or complexity. What began as a focused website and marketing studio has grown into a full digital transformation partner spanning web development, performance marketing, SEO, and custom ERP systems.</p>
   <p style="font-size:14.5px;color:var(--g500);line-height:1.75;margin-bottom:1.1rem">Today, Drawlead works with founders, doctors, chefs, hospitality brands, and D2C companies across India — including collaborations with names like Dr. Velumani, Chef Koushik, and hospitality group V Hospitals — turning ambitious ideas into lasting online impressions that connect businesses with the audiences that matter most.</p>
   <p style="font-size:14.5px;color:var(--g500);line-height:1.75">Every engagement follows the same disciplined path: a free consultation to understand the business, a customized growth strategy built around real data, and a hands-on partnership to execute it — not a one-off project handoff.</p>
  </div>
  <div class="story-card rv d2">
   <div class="story-card-label">At a Glance</div>
   <ul class="story-facts">
    <li><span>Headquarters</span><strong>Chennai, Tamil&nbsp;Nadu, India</strong></li>
    <li><span>Founded &amp; Led By</span><strong>Vinothkumar Babu</strong></li>
    <li><span>Focus</span><strong>MSMEs &amp; SMEs</strong></li>
    <li><span>Core Services</span><strong>8 Growth Disciplines</strong></li>
    <li><span>Industries Served</span><strong>8+ Sectors</strong></li>
    <li><span>Approach</span><strong>Consult → Strategize → Grow</strong></li>
   </ul>
   <div class="story-tagline">செயலை மாற்றும் <span>· Transforming Action</span></div>
  </div>
 </div>
</section>

<!-- ═══════════════════ FOUNDER ═══════════════════ -->
<section id="founder">
<?php
/*
 * Leadership. Two bands: a full-bleed grey masthead carrying the name, the role
 * and the portrait flush to the right edge, then a white block split into the
 * bio/quote column and the competencies/socials column.
 *
 * The portrait is optional on purpose. Until assets/img/founder.webp exists the
 * monogram plate renders instead, so the section never shows a broken image; drop
 * the file in and it swaps itself over with no template change.
 */
$fdrPhoto = __DIR__ . '/../assets/img/founder.webp';
$fdrHasPhoto = is_file($fdrPhoto);

/* Only profiles with a real URL are rendered — no placeholder hrefs. */
$fdrSocials = [
 'linkedin'  => 'https://www.linkedin.com/in/vinothkumarbabu7/',
 'instagram' => '',
 'x'         => '',
 'facebook'  => '',
];
$fdrIcons = [
 'linkedin'  => '<svg viewBox="0 0 48 48" aria-hidden="true"><rect width="48" height="48" rx="8" fill="#0A66C2"/><path fill="#fff" d="M17.6 19.4h-5.1V37h5.1V19.4zm.34-5.2a2.9 2.9 0 1 0-5.8 0 2.9 2.9 0 0 0 5.8 0zM37 27.3c0-4.9-2.6-7.2-6.1-7.2-2.8 0-4.1 1.55-4.8 2.64V19.4h-5.1c.07 1.44 0 17.6 0 17.6h5.1v-9.8c0-.46.03-.92.17-1.25.37-.92 1.2-1.86 2.62-1.86 1.85 0 2.59 1.4 2.59 3.47V37H37v-9.7z"/></svg>',
 'instagram' => '<svg viewBox="0 0 48 48" aria-hidden="true"><defs><radialGradient id="fdrIg" cx=".3" cy="1.1" r="1.3"><stop offset="0" stop-color="#FDD05C"/><stop offset=".3" stop-color="#F25F4C"/><stop offset=".6" stop-color="#D92E7F"/><stop offset="1" stop-color="#8A3AB9"/></radialGradient></defs><rect width="48" height="48" rx="11" fill="url(#fdrIg)"/><rect x="12" y="12" width="24" height="24" rx="7" fill="none" stroke="#fff" stroke-width="2.6"/><circle cx="24" cy="24" r="5.6" fill="none" stroke="#fff" stroke-width="2.6"/><circle cx="31.4" cy="16.6" r="1.8" fill="#fff"/></svg>',
 'x'         => '<svg viewBox="0 0 48 48" aria-hidden="true"><rect width="48" height="48" rx="8" fill="#000"/><path fill="#fff" d="M27.6 21.9 36.4 12h-2.6l-7.6 8.6L20.1 12h-7l9.2 13.1L13.1 36h2.6l8-9.1 6.4 9.1h7l-9.5-14.1zm-2.9 3.2-.93-1.3-7.4-10.4h3.1l6 8.4.93 1.3 7.8 10.9h-3.1l-6.4-8.9z"/></svg>',
 'facebook'  => '<svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="21" fill="#1877F2"/><path fill="#fff" d="M31.5 30.2l.93-6.1h-5.85v-3.95c0-1.67.82-3.3 3.44-3.3h2.66v-5.2s-2.42-.41-4.72-.41c-4.82 0-7.97 2.92-7.97 8.21v4.65h-5.36v6.1h5.36V45a21.3 21.3 0 0 0 6.59 0V30.2h4.92z"/></svg>',
];
?>
 <div class="fdr-band">
  <div class="fdr-id">
   <h2 class="fdr-name rv">Vinothkumar Babu</h2>
   <div class="fdr-role rv">Founder &amp; CEO</div>
  </div>
  <div class="fdr-photo">
<?php if ($fdrHasPhoto): ?>
   <img src="<?= asset_url('/assets/img/founder.webp') ?>" alt="Vinothkumar Babu, founder and CEO of Drawlead" decoding="async" fetchpriority="low">
<?php else: ?>
   <div class="fdr-mono" aria-hidden="true">VB</div>
<?php endif; ?>
  </div>
 </div>

 <div class="fdr-lower">
  <div class="fdr-main">
   <p class="fdr-bio rv">Vinothkumar founded Drawlead after studying Computer Science (B.E., St. Joseph College of Engineering, 2018–2022), pairing an engineer's systems thinking with hands-on expertise in web design, WordPress and Shopify development, SEO, and performance marketing. He stays close to the work — personally shaping strategy on Drawlead's website, SEO, and Shopify growth engagements — and shares what he learns with a growing community of 7,000+ followers on LinkedIn.</p>
   <blockquote class="fdr-quote rv">
    <span class="fdr-qmark" aria-hidden="true">&ldquo;</span>
    <p>"Most Shopify stores don't fail because of traffic. They fail because the store…"</p>
    <cite>— Vinothkumar Babu, on LinkedIn</cite>
   </blockquote>
  </div>

  <div class="fdr-aside">
   <div class="fdr-label rv">Core Competencies</div>
   <div class="fdr-skills rv">
    <span class="fdr-tag">Web Design</span><span class="fdr-tag">Web Development</span><span class="fdr-tag">WordPress</span><span class="fdr-tag fdr-tag-on">Shopify</span><span class="fdr-tag">SEO</span><span class="fdr-tag">Social Media Marketing</span><span class="fdr-tag">Lead Generation</span><span class="fdr-tag">Graphic Design</span><span class="fdr-tag">SEM</span>
   </div>
   <div class="fdr-socials rv">
<?php foreach ($fdrSocials as $net => $url): if ($url === '') continue; ?>
    <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="fdr-social" aria-label="<?= ucfirst($net) ?>"><?= $fdrIcons[$net] ?></a>
<?php endforeach; ?>
   </div>
  </div>
 </div>
</section>

<!-- ═══════════════════ SERVICES ═══════════════════ -->
<section id="services">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">What We Do</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Everything a growing business needs, <span class="g">under one roof</span></h2>
 <p class="sec-sub rv">From the first line of code to the last rupee of ad spend — Drawlead runs it end-to-end.</p>

 <!-- Sticky horizontal scroll, mirroring Home 7's Core Platform. The wrapper is
      sized by JS to the row's own overflow, so the pinned run consumes exactly
      the scroll distance the cards need. Styles: .svc-* in partials/style.php. -->
 <div class="svc-scroll-outer" id="svcScrollOuter">
  <div class="svc-scroll-sticky" id="svcScrollSticky">
   <div class="fn-grid" id="svcRow">
<?php
/*
 * What We Do — eight service cards, styled to match Home 7's Core Platform card
 * (.cf-card): white plate, 1px #F0F0F0 border, 12px radius, soft shadow, a 28px
 * black line icon, then name / description / tags / action.
 *
 * Driven by a data array rather than eight hand-written blocks so the copy and the
 * markup stay separable. Icons use the same idiom as .cf-icon — 24x24, no fill,
 * currentColor stroke at 1.75 with round caps and joins — so they sit at the same
 * visual weight as the ones on Home 7.
 *
 * Card styling is .svc-* in partials/style.php, scoped to #services: .fn-card is
 * shared with four other templates and must not shift under them.
 */
$svcCards = [
 [
  'icon' => '<path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M3 9h18"/><path d="M9 13l-1.6 1.6L9 16.2"/><path d="M15 13l1.6 1.6L15 16.2"/>',
  'name' => 'Website Development',
  'desc' => 'Custom websites, WordPress builds, Shopify stores, ecommerce platforms, and high-converting landing pages.',
  'tags' => ['Custom Builds', 'WordPress', 'Shopify'],
 ],
 [
  'icon' => '<circle cx="18" cy="5.5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="18.5" r="2.5"/><path d="M8.2 10.8 15.8 6.7"/><path d="M8.2 13.2 15.8 17.3"/>',
  'name' => 'Social Media Marketing',
  'desc' => 'Strategy, content creation, video production, and analytics across Instagram, Facebook, YouTube &amp; LinkedIn.',
  'tags' => ['Content', 'Video', 'Analytics'],
 ],
 [
  'icon' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="M15.4 15.4 21 21"/><path d="M7.5 12.1 9.6 9.4l1.9 1.6 2.4-3"/>',
  'name' => 'Search Engine Optimization',
  'desc' => 'On-page, off-page, local, and technical SEO backed by keyword research and authoritative link building.',
  'tags' => ['On-Page', 'Technical', 'Local SEO'],
 ],
 [
  'icon' => '<path d="M12 3 21 7.5 12 12 3 7.5z"/><path d="M3 12.2 12 16.7l9-4.5"/><path d="M3 16.7 12 21.2l9-4.5"/>',
  'name' => 'ERP Software Solutions',
  'desc' => 'Custom ERP systems that digitize operations, automate workflows, and give founders real-time visibility.',
  'tags' => ['Automation', 'Workflows', 'Reporting'],
 ],
 [
  'icon' => '<path d="M3 4.5h18l-7 8.2v6.6l-4 2.2v-8.8z"/>',
  'name' => 'Lead Generation',
  'desc' => 'Full-funnel systems that turn strangers into qualified leads, and leads into paying customers.',
  'tags' => ['Funnels', 'CRM', 'Nurturing'],
 ],
 [
  'icon' => '<path d="M12 3.2 13.9 9 19.8 10.9 13.9 12.8 12 18.6 10.1 12.8 4.2 10.9 10.1 9z"/><path d="M18.4 16.4 19.2 18.6 21.4 19.4 19.2 20.2 18.4 22.4 17.6 20.2 15.4 19.4 17.6 18.6z"/>',
  'name' => 'AI Website Solutions',
  'desc' => 'AI-assisted websites and tools that personalize experiences and automate routine business tasks.',
  'tags' => ['AI Automation', 'Personalization'],
 ],
 [
  'icon' => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r="1.1" fill="currentColor" stroke="none"/>',
  'name' => 'Google Ads',
  'desc' => 'Performance-driven search and display campaigns engineered for measurable return on ad spend.',
  'tags' => ['Search Ads', 'Display', 'ROAS'],
 ],
 [
  'icon' => '<path d="M3 10.2v3.6a1 1 0 0 0 1 1h2.2l6.3 4.2V5l-6.3 4.2H4a1 1 0 0 0-1 1z"/><path d="M17 9.4a4 4 0 0 1 0 5.2"/><path d="M19.8 6.8a8 8 0 0 1 0 10.4"/>',
  'name' => 'Meta Ads',
  'desc' => 'High-performing Instagram and Facebook campaigns tuned for reach, engagement, and conversions.',
  'tags' => ['Instagram', 'Facebook', 'Conversions'],
 ],
];
?>
<?php foreach ($svcCards as $c): ?>
    <div class="fn-card">
     <div class="svc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $c['icon'] ?></svg></div>
     <div class="svc-name"><?= $c['name'] ?></div>
     <div class="svc-desc"><?= $c['desc'] ?></div>
     <div class="svc-tags"><?php foreach ($c['tags'] as $t): ?><span class="svc-tag"><?= $t ?></span><?php endforeach; ?></div>
     <button type="button" data-book class="svc-go">Explore service</button>
    </div>
<?php endforeach; ?>
   </div><!-- /fn-grid -->
   <div class="sec-cta rv">
 <button type="button" data-book class="btn btn-black">Book Free Consultation →</button>
 <a href="/#solutions" class="btn btn-outline2">Explore Solutions</a>
   </div>
  </div><!-- /svc-scroll-sticky -->
 </div><!-- /svc-scroll-outer -->
</section>

<script>
// "What We Do": sticky horizontal scroll.
// A verbatim copy of Home 7's Core Platform driver (templates/home7-body.php),
// with only the three element ids changed. The row is pinned via CSS
// position:sticky; as the user scrolls down through the wrapper this maps that
// scroll distance 1:1 to translateX so the row reveals left -> right, then
// releases back to normal vertical scroll once done. The sticky box is sized to
// its own natural content height, so the scroll distance consumed matches only
// the actual horizontal overflow and not an inflated viewport-sized box.
(function(){
 const outer = document.getElementById('svcScrollOuter');
 const sticky = document.getElementById('svcScrollSticky');
 const row = document.getElementById('svcRow');
 if(!outer || !sticky || !row) return;

 const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
 const skipHijack = reduceMotion || window.matchMedia('(max-width:768px)').matches;
 if(skipHijack) return;

 // Read back off the element instead of being duplicated here: the box pins
 // centred in the viewport, and a hard-coded 0 would put the horizontal progress
 // out of step with where it actually sticks.
 let stickyTop = 0;
 let overflow = 0;

 function measure(){
  stickyTop = parseFloat(getComputedStyle(sticky).top) || 0;
  overflow = Math.max(0, row.scrollWidth - sticky.clientWidth);
  outer.style.height = (sticky.offsetHeight + overflow) + 'px';
 }

 function onScroll(){
  if(overflow <= 0){ row.style.transform = 'translateX(0)'; return; }
  const rect = outer.getBoundingClientRect();
  const progress = Math.min(1, Math.max(0, (stickyTop - rect.top) / overflow));
  row.style.transform = `translateX(${-progress * overflow}px)`;
 }

 measure();
 onScroll();
 window.addEventListener('resize', ()=>{ measure(); onScroll(); });
 window.addEventListener('scroll', onScroll, { passive: true });
})();
</script>

<!-- ═══════════════════ INDUSTRIES ═══════════════════ -->
<section id="industries-about" style="background:var(--white)">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Who We Work With</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Industries we help <span class="g">grow</span></h2>
 <p class="sec-sub rv">Across sectors, the goal stays the same: measurable growth, without the guesswork.</p>
<?php
/*
 * Eight industries shown one at a time in a pinned stage: rounded image left,
 * numbered content right. The slides are stacked on top of each other and
 * crossfaded by scroll position, so each replaces the last inside one layout;
 * after the eighth the pin releases and the page scrolls on.
 *
 * Each image is optional. Until assets/img/ind-<slug>.webp exists the slide
 * renders a numbered plate in its place, so nothing breaks and the stage keeps
 * its height; dropping the files in swaps them over with no template change.
 *
 * Styles are .iw-* in partials/style.php. The pinned state is opt-in — the JS
 * adds .iw-on — so with no JS, a thrown error, reduced motion or a narrow
 * viewport the eight slides simply stack and scroll as an ordinary list.
 */
$industries = [
 ['slug' => 'ecommerce',        'name' => 'E-commerce',          'desc' => 'Shopify and WooCommerce stores built to convert, backed by the SEO, ads and automation that keep repeat orders coming in.'],
 ['slug' => 'healthcare',       'name' => 'Healthcare',          'desc' => 'Clinic and hospital systems that handle appointments, records and billing, behind a site patients actually trust.'],
 ['slug' => 'education',        'name' => 'Education',           'desc' => 'Institutions and edtech brands get admissions funnels, course platforms and the reporting to see what really enrols students.'],
 ['slug' => 'saas',             'name' => 'SaaS',                'desc' => 'Product sites, onboarding flows and lifecycle campaigns that turn free trials into paying, long-retained subscribers.'],
 ['slug' => 'hospitality',      'name' => 'Hospitality',         'desc' => 'Hotels, restaurants and venues get direct-booking sites and local SEO that cut the commission paid to aggregators.'],
 ['slug' => 'fashion',          'name' => 'Fashion',             'desc' => 'Lookbook-grade storefronts and campaign creative, wired to inventory so every drop goes live without the scramble.'],
 ['slug' => 'food-beverage',    'name' => 'Food &amp; Beverage',     'desc' => 'Ordering, delivery and multi-outlet operations brought into one system, with marketing that keeps the kitchen busy.'],
 ['slug' => 'trainers-coaches', 'name' => 'Trainers &amp; Coaches',  'desc' => 'Personal brands get booking, payments and content engines, so the hours go into coaching instead of chasing admin.'],
];
$iwTotal = str_pad((string) count($industries), 2, '0', STR_PAD_LEFT);
?>
 <div class="iw-outer" id="iwOuter" style="--iw-count:<?= count($industries) ?>">
  <div class="iw-pin" id="iwPin">
   <div class="iw-stage" id="iwStage">
<?php foreach ($industries as $n => $ind):
  $num = str_pad((string) ($n + 1), 2, '0', STR_PAD_LEFT);
  $rel = '/assets/img/ind-' . $ind['slug'] . '.webp';
  $has = is_file(__DIR__ . '/..' . $rel);
?>
    <article class="iw-slide<?= $n === 0 ? ' is-active' : '' ?>">
     <div class="iw-media<?= $has ? '' : ' iw-media-empty' ?>">
<?php if ($has): ?>
      <img src="<?= asset_url($rel) ?>" alt="<?= strip_tags($ind['name']) ?> businesses Drawlead works with" loading="lazy" decoding="async" fetchpriority="<?= $n === 0 ? 'high' : 'low' ?>">
<?php else: ?>
      <span class="iw-plate" aria-hidden="true"><?= $num ?></span>
<?php endif; ?>
     </div>
     <div class="iw-body">
      <div class="iw-count"><b><?= $num ?></b>/<?= $iwTotal ?></div>
      <h3 class="iw-name"><?= $ind['name'] ?></h3>
      <p class="iw-desc"><?= $ind['desc'] ?></p>
      <button type="button" data-book class="btn btn-black iw-cta">Learn more</button>
     </div>
    </article>
<?php endforeach; ?>
   </div>
  </div>
 </div>
</section>

<script>
// "Who We Work With": pinned crossfade sequence.
//
// The stage is held by CSS position:sticky. This maps the distance scrolled through
// the taller wrapper onto a float position along the slides, then crossfades between
// the two slides either side of it. Nothing is on a timer — every value is read from
// scroll position, so the sequence tracks the scrollbar exactly and reverses cleanly.
//
// Two slides are ever non-zero at once, and smoothstep(x) + smoothstep(1-x) === 1, so
// the pair always sums to full opacity: no dip to grey at the midpoint, no flash.
(function(){
 const outer = document.getElementById('iwOuter');
 const pin   = document.getElementById('iwPin');
 const stage = document.getElementById('iwStage');
 if(!outer || !pin || !stage) return;

 const slides = Array.from(stage.querySelectorAll('.iw-slide'));
 if(slides.length < 2) return;

 const reduce = window.matchMedia('(prefers-reduced-motion: reduce)');
 const narrow = window.matchMedia('(max-width:900px)');

 const media = slides.map(s => s.querySelector('.iw-media img, .iw-plate'));
 const bodies = slides.map(s => s.querySelector('.iw-body'));

 let stickyTop = 0, runway = 0, last = -1, ticking = false, on = false;

 // smoothstep: eases the crossfade without breaking the pair summing to 1
 function ss(x){ return x <= 0 ? 0 : x >= 1 ? 1 : x * x * (3 - 2 * x); }

 function enable(){
  if(on) return;
  on = true;
  outer.classList.add('iw-on');
 }
 function disable(){
  if(!on) return;
  on = false;
  outer.classList.remove('iw-on');
  slides.forEach(function(s, i){
   s.style.opacity = ''; s.classList.toggle('is-active', i === 0);
   s.removeAttribute('inert'); s.removeAttribute('aria-hidden');
   if(media[i]) media[i].style.transform = '';
   if(bodies[i]) bodies[i].style.transform = '';
  });
 }

 function measure(){
  if(reduce.matches || narrow.matches){ disable(); return; }
  enable();
  stickyTop = parseFloat(getComputedStyle(pin).top) || 0;
  // .iw-on sizes the wrapper in CSS (pin height + one --iw-step per transition),
  // so the runway is just whatever is left once the pin is accounted for.
  runway = Math.max(0, outer.offsetHeight - pin.offsetHeight);
 }

 function render(){
  ticking = false;
  if(!on || runway <= 0) return;

  const rect = outer.getBoundingClientRect();
  let p = (stickyTop - rect.top) / runway;
  p = p < 0 ? 0 : p > 1 ? 1 : p;

  const pos = p * (slides.length - 1);
  const active = Math.round(pos);

  for(let i = 0; i < slides.length; i++){
   const d = pos - i;                      // <0 upcoming, 0 active, >0 already passed
   const a = Math.abs(d);
   const o = a >= 1 ? 0 : ss(1 - a);
   slides[i].style.opacity = o.toFixed(4);
   if(media[i]){
    // incoming eases down from slightly larger; outgoing settles slightly smaller
    const sc = 1 - Math.max(-1, Math.min(1, d)) * 0.06;
    media[i].style.transform = 'scale(' + sc.toFixed(4) + ')';
   }
   if(bodies[i]){
    // clamped like the scale: a slide seven places away would otherwise be pushed
    // 210px, which is invisible at opacity 0 but still costs layout on every frame
    const dy = Math.max(-1, Math.min(1, d)) * -30;
    bodies[i].style.transform = 'translateY(' + dy.toFixed(2) + 'px)';
   }
  }

  if(active !== last){
   last = active;
   for(let i = 0; i < slides.length; i++){
    const isActive = i === active;
    slides[i].classList.toggle('is-active', isActive);
    // keeps the eight hidden buttons out of the tab order and off screen readers
    if(isActive){ slides[i].removeAttribute('inert'); slides[i].removeAttribute('aria-hidden'); }
    else { slides[i].setAttribute('inert', ''); slides[i].setAttribute('aria-hidden', 'true'); }
   }
  }
 }

 function request(){
  if(ticking) return;
  ticking = true;
  window.requestAnimationFrame(render);
 }

 measure();
 render();
 window.addEventListener('scroll', request, { passive: true });
 window.addEventListener('resize', function(){ measure(); last = -1; render(); });
 if(reduce.addEventListener) reduce.addEventListener('change', function(){ measure(); last = -1; render(); });
 if(narrow.addEventListener) narrow.addEventListener('change', function(){ measure(); last = -1; render(); });
})();
</script>

<!-- ═══════════════════ HOW WE WORK ═══════════════════ -->
<section id="values" style="background:#0a1310;color:#fff">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">How We Work</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv" style="color:#fff">The principles behind <span>every engagement</span></h2>
 <p class="sec-sub rv" style="color:rgba(255,255,255,.5)">Four commitments that shape how Drawlead partners with every business we work with.</p>
 <div class="why-grid-4">
 <div class="why-card rv d1">
 <div class="why-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 18px rgba(0,0,0,.2)"><svg width="24" height="24" fill="none" viewBox="0 0 40 40"><path d="M20 34 C11 28 5 22 5 15 C5 10 9 6 13.5 6 C17 6 19 8.5 20 11 C21 8.5 23 6 26.5 6 C31 6 35 10 35 15 C35 22 29 28 20 34 Z" fill="rgba(255,255,255,.92)"/></svg></div>
 <div class="why-name">MSME-First</div>
 <div class="why-desc">Every strategy is built around what actually moves the needle for a small or growing business — not vanity metrics.</div>
 </div>
 <div class="why-card rv d2">
 <div class="why-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 18px rgba(0,0,0,.2)"><svg width="24" height="24" fill="none" viewBox="0 0 40 40"><rect x="6" y="24" width="6" height="10" fill="rgba(255,255,255,.5)"/><rect x="17" y="16" width="6" height="18" fill="rgba(255,255,255,.75)"/><rect x="28" y="6" width="6" height="28" fill="white"/></svg></div>
 <div class="why-name">Data Before Design</div>
 <div class="why-desc">We start with keyword research, funnel data, and market reality — then design and build around it.</div>
 </div>
 <div class="why-card rv d3">
 <div class="why-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 18px rgba(0,0,0,.2)"><svg width="24" height="24" fill="none" viewBox="0 0 40 40"><path d="M20 4 L33 9 L33 20 Q33 30 20 36 Q7 30 7 20 L7 9 Z" fill="rgba(255,255,255,.85)"/><polyline points="13,20 18,25 27,14" fill="none" stroke="#14855a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
 <div class="why-name">End-to-End Ownership</div>
 <div class="why-desc">From strategy to code to ad spend — one team owns the outcome. No handoffs, no finger-pointing.</div>
 </div>
 <div class="why-card rv d4">
 <div class="why-icon" style="background:linear-gradient(135deg,#32b46f,#14855a);box-shadow:0 6px 18px rgba(0,0,0,.2)"><svg width="24" height="24" fill="none" viewBox="0 0 40 40"><circle cx="20" cy="13" r="8" fill="white"/><path d="M4 36 C4 25 11 21 20 21 C29 21 36 25 36 36 Z" fill="rgba(255,255,255,.8)"/></svg></div>
 <div class="why-name">Founder-Led Execution</div>
 <div class="why-desc">Vinothkumar and the core team stay hands-on with strategy and execution on every single engagement.</div>
 </div>
 </div>
</section>

<!-- ═══════════════════ CTA ═══════════════════ -->
<section id="cta">
 <div class="cta-grid-bg"></div>
 <div class="cta-glow"></div>
 <h2 class="cta-h rv">Let's build your<br><span class="gr">growth</span> <span class="gr2">engine</span><br>together</h2>
 <p class="cta-p rv">Book a free consultation with Vinothkumar and the Drawlead team — no commitment needed.</p>
 <div class="cta-btns rv">
 <button type="button" data-book class="cta-btn-w">Schedule Free Consultation →</button>
 <a href="/" class="cta-btn-g">Back to Home</a>
 </div>
 <div class="cta-note rv">செயலை மாற்றும் · Intelligent Operating System · Secure · Scalable · Future-Ready</div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
