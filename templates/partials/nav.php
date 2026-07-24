<?php
/**
 * Shared site nav, included by every public template.
 * Set $activePage (one of: home, about-us, blog) before including this
 * to highlight the current link.
 */
$activePage = $activePage ?? '';
?>
<nav>
 <a href="/" class="logo" aria-label="Drawlead — home"><img src="data:image/png;base64,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" alt="Drawlead — Digital Transformation Company" width="705" height="138"></a>
 <ul class="nav-links">
 <li><a href="/"<?= $activePage === 'home' ? ' style="color:var(--black)"' : '' ?>>Home</a></li>
 <li class="has-mega">
   <a href="/#functions">Platform</a>
   <div class="mega-panel">
    <div class="mega-inner mega-inner-grid3">

     <?php foreach (platform_modules_ordered() as $entry): $m = $entry['module']; ?>
     <div class="mega-col">
      <div class="mega-col-icon" style="background:linear-gradient(135deg,<?= h($m['color']) ?>,#0a1310)"><?= $m['icon'] ?></div>
      <div class="mega-col-title"><?= h($m['name']) ?></div>
      <ul class="mega-list">
       <?php foreach (array_slice($m['features'], 0, 3) as $feature): ?>
       <li><?= h($feature) ?></li>
       <?php endforeach; ?>
      </ul>
      <a href="/platform-<?= h($entry['key']) ?>" class="mega-know">Know More →</a>
     </div>
     <?php endforeach; ?>

     <div class="mega-cta mega-cta-span2">
      <div class="mega-cta-title">See it all together</div>
      <p class="mega-cta-text">Get a personalised walkthrough of all 7 modules working in sync.</p>
      <button type="button" data-book class="mega-cta-btn">Book a Free Consultation →</button>
     </div>

    </div>
   </div>
  </li>
 <li class="has-mega">
   <a href="/#solutions">Solutions</a>
   <div class="mega-panel">
    <div class="mega-inner">

     <div class="mega-col">
      <div class="mega-col-icon" style="background:linear-gradient(135deg,#32b46f,#14855a)">
       <svg width="19" height="19" viewBox="0 0 40 40" fill="none"><rect x="5" y="5" width="13" height="13" rx="2.5" fill="white"/><rect x="22" y="5" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="5" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.6)"/><rect x="22" y="22" width="13" height="13" rx="2.5" fill="rgba(255,255,255,.85)"/><path d="M18 11.5 L22 11.5 M11.5 18 L11.5 22 M28.5 18 L28.5 22 M18 28.5 L22 28.5" stroke="rgba(255,255,255,.5)" stroke-width="2" stroke-linecap="round"/></svg>
      </div>
      <div class="mega-col-title">Custom ERP Solution</div>
      <ul class="mega-list">
       <li>Custom modules for your exact operating process</li>
       <li>Role-based access, approvals &amp; audit trails</li>
       <li>Migration from spreadsheets &amp; legacy systems</li>
      </ul>
      <a href="/custom-erp-solution" class="mega-know">Know More →</a>
     </div>

     <div class="mega-col">
      <div class="mega-col-icon" style="background:linear-gradient(135deg,#32b46f,#14855a)">
       <svg width="19" height="19" viewBox="0 0 40 40" fill="none"><path d="M4 10 L10 10 L14 27 L32 27" stroke="rgba(255,255,255,.55)" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M11.5 14 L35 14 L32 24 L13.8 24 Z" fill="white"/><circle cx="16" cy="33" r="3" fill="rgba(255,255,255,.85)"/><circle cx="30" cy="33" r="3" fill="rgba(255,255,255,.85)"/></svg>
      </div>
      <div class="mega-col-title">Ecommerce Solutions</div>
      <ul class="mega-list">
       <li>Shopify, WooCommerce &amp; custom storefront builds</li>
       <li>Live inventory sync across every sales channel</li>
       <li>Automated order, invoice &amp; GST workflows</li>
      </ul>
      <a href="/ecommerce-solutions" class="mega-know">Know More →</a>
     </div>

     <div class="mega-col">
      <div class="mega-col-icon" style="background:linear-gradient(135deg,#32b46f,#14855a)">
       <svg width="19" height="19" viewBox="0 0 40 40" fill="none"><path d="M4 6 L36 6 L24 21 L24 34 L16 30 L16 21 Z" fill="white"/><path d="M16 21 L24 21 L24 27 L16 27 Z" fill="rgba(255,255,255,.55)"/><path d="M4 6 L36 6 L31 12 L9 12 Z" fill="rgba(255,255,255,.6)"/></svg>
      </div>
      <div class="mega-col-title">Marketing Solutions</div>
      <ul class="mega-list">
       <li>Technical SEO, Core Web Vitals &amp; site architecture</li>
       <li>Google, Meta &amp; LinkedIn performance campaigns</li>
       <li>Instant WhatsApp &amp; email follow-up on every lead</li>
      </ul>
      <a href="/marketing-solutions" class="mega-know">Know More →</a>
     </div>

     <div class="mega-cta">
      <div class="mega-cta-title">Not sure which fits?</div>
      <p class="mega-cta-text">Get a free consultation and we'll map the right solution to your business.</p>
      <button type="button" data-book class="mega-cta-btn">Book a Free Consultation →</button>
     </div>

    </div>
   </div>
  </li>
 <li class="has-mega">
   <a href="/case-studies">Case Studies</a>
   <div class="mega-panel">
    <div class="mega-inner">

     <div class="mega-col">
      <div class="mega-col-icon" style="background:linear-gradient(135deg,#32b46f,#14855a)">
       <svg width="19" height="19" viewBox="0 0 40 40" fill="none"><rect x="6" y="18" width="8" height="16" fill="white"/><rect x="16" y="10" width="8" height="24" fill="rgba(255,255,255,.75)"/><rect x="26" y="4" width="8" height="30" fill="rgba(255,255,255,.55)"/></svg>
      </div>
      <div class="mega-col-title">Construction &amp; Real Estate</div>
      <ul class="mega-list">
       <li>Better operational visibility across all project sites</li>
       <li>Faster reporting workflows and billing automation</li>
       <li>Improved multi-site project management controls</li>
      </ul>
      <a href="/case-studies" class="mega-know">Know More →</a>
     </div>

     <div class="mega-col">
      <div class="mega-col-icon" style="background:linear-gradient(135deg,#32b46f,#14855a)">
       <svg width="19" height="19" viewBox="0 0 40 40" fill="none"><path d="M20 4 L34 10 L34 22 Q34 32 20 37 Q6 32 6 22 L6 10 Z" fill="rgba(255,255,255,.85)"/><path d="M20 12 L20 26 M13 19 L27 19" stroke="#14855a" stroke-width="3.5" stroke-linecap="round"/></svg>
      </div>
      <div class="mega-col-title">Healthcare &amp; Wellness</div>
      <ul class="mega-list">
       <li>Streamlined clinic workflows across branches</li>
       <li>Improved scheduling efficiency and capacity</li>
       <li>Centralized billing and cross-branch reporting</li>
      </ul>
      <a href="/case-studies" class="mega-know">Know More →</a>
     </div>

     <div class="mega-col">
      <div class="mega-col-icon" style="background:linear-gradient(135deg,#32b46f,#14855a)">
       <svg width="19" height="19" viewBox="0 0 40 40" fill="none"><path d="M20 4 L24 16 L37 20 L24 24 L20 37 L16 24 L3 20 L16 16 Z" fill="white"/></svg>
      </div>
      <div class="mega-col-title">Marketing Agencies</div>
      <ul class="mega-list">
       <li>Improved team collaboration and project delivery</li>
       <li>Better client and pipeline management</li>
       <li>Measurable increase in team productivity</li>
      </ul>
      <a href="/case-studies" class="mega-know">Know More →</a>
     </div>

     <div class="mega-cta">
      <div class="mega-cta-title">Want results like these?</div>
      <p class="mega-cta-text">See how Drawlead can do the same for your business — starting with a free call.</p>
      <button type="button" data-book class="mega-cta-btn">Book a Free Consultation →</button>
     </div>

    </div>
   </div>
  </li>
 <li><a href="/#industries">Industries</a></li>
 <li><a href="/blog"<?= $activePage === 'blog' ? ' style="color:var(--black)"' : '' ?>>Blog</a></li>
 <li><a href="/about-us"<?= $activePage === 'about-us' ? ' style="color:var(--black)"' : '' ?>>About Us</a></li>
 </ul>
 <button type="button" data-book class="nav-btn">Free Consultation Call →</button>
</nav>
<script>
(function(){
 // Pure-CSS :hover closes the mega menu the instant the mouse leaves the
 // trigger <li>, so a fast/diagonal move toward the panel below can drop
 // out of :hover before reaching the buttons. This adds a short grace
 // period (hover-intent) via a JS-driven class, on top of the existing
 // CSS :hover/:focus-within, so the panel stays open long enough to reach.
 document.querySelectorAll('.has-mega').forEach(function(li){
  var closeTimer = null;
  function open(){
   clearTimeout(closeTimer);
   li.classList.add('mega-open');
  }
  function scheduleClose(){
   clearTimeout(closeTimer);
   closeTimer = setTimeout(function(){ li.classList.remove('mega-open'); }, 350);
  }
  li.addEventListener('mouseenter', open);
  li.addEventListener('mouseleave', scheduleClose);
  li.addEventListener('focusin', open);
  li.addEventListener('focusout', function(e){
   if (!li.contains(e.relatedTarget)) scheduleClose();
  });
 });
})();
</script>
