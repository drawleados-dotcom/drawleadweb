<?php
/**
 * Home 10: the homepage from the drawlead-website design, served as a
 * DB-managed page (Admin -> Pages; migration 032 adds it and puts it at /).
 *
 * Ported from _dev/design/drawlead-website/index.html. This file is now the source for
 * the live page: edit it here, or in Admin -> Pages -> Edit -> Page Content.
 *
 * Styling is /assets/home10.css, with every rule scoped under .h10 so neither
 * this design nor the site-wide stylesheet (assets/site.css) leaks into the
 * other. The design's .btn and .logo are .h10-btn and .h10-logo here for the
 * same reason. Behaviour is /assets/home10.js, images are in /assets/home10/.
 *
 * What differs from the static design:
 *  - The header is the site's own (partials/nav.php), which every page shares.
 *  - Consultation buttons open the booking popup (data-book), or go to /book
 *    when JavaScript is off.
 *  - Case study cards link to the matching case study, or to the list when
 *    there is no match (see $h10Case).
 *  - The footer also links to About Us and Blog and carries the contact line.
 *
 * Testimonials still holds placeholder copy. Migration 032 hides the section;
 * once real quotes are in, show it from Page Content.
 *
 * @var PDO $pdo
 */
$activePage = 'home10';
include __DIR__ . '/partials/nav.php';

// A case study's address if it is still published, else $fallback, so a card
// never links to a 404 after a case study is renamed or unpublished in admin.
$h10Case = function (string $slug, string $fallback) use ($pdo): string {
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM case_studies WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        return $stmt->fetchColumn() ? '/case-studies/' . $slug : $fallback;
    } catch (PDOException $e) {
        return $fallback;
    }
};
?>
<link rel="stylesheet" href="<?= asset_url('/assets/home10.css') ?>">
<script>document.documentElement.classList.add('js')</script>

<div class="h10">
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs><symbol id="i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></symbol><symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol><symbol id="i-sparkle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z" fill="currentColor" stroke="none"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z" fill="currentColor" stroke="none"/></symbol><symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2.2 2.2L15 10"/></symbol><symbol id="i-layers" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 12.5l9 5 9-5"/><path d="M3 17l9 5 9-5" opacity=".55"/></symbol><symbol id="i-briefcase" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M9 7V5.5A1.5 1.5 0 0110.5 4h3A1.5 1.5 0 0115 5.5V7M3 13h18"/></symbol><symbol id="i-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 10h18M8 3v4M16 3v4M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></symbol><symbol id="i-building" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="10" height="18" rx="1.5"/><path d="M15 9h4v12h-4M8 7h4M8 11h4M8 15h4"/></symbol><symbol id="i-cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2.2l2.4 11h10.4L20 8H6.3"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></symbol><symbol id="i-puzzle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h4a2 2 0 1 1 4 0h4v4a2 2 0 1 1 0 4v4H4v-4a2 2 0 1 0 0-4z"/></symbol><symbol id="i-store" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9l1.5-5h13L20 9M4 9h16v1.5a2.7 2.7 0 0 1-5.3.7 2.7 2.7 0 0 1-5.4 0A2.7 2.7 0 0 1 4 10.5zM5.5 13v7h13v-7M10 20v-4h4v4"/></symbol><symbol id="i-chip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="7" width="10" height="10" rx="2"/><path d="M10 3v4M14 3v4M10 17v4M14 17v4M3 10h4M3 14h4M17 10h4M17 14h4M10.5 12h3"/></symbol><symbol id="i-trend" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"/><path d="M15 7h6v6"/></symbol><symbol id="i-wallet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="13" rx="3"/><path d="M3 10h18M16.5 14.5h1"/></symbol><symbol id="i-grid" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="3.5" width="7" height="7" rx="2"/><rect x="13.5" y="3.5" width="7" height="7" rx="2"/><rect x="3.5" y="13.5" width="7" height="7" rx="2"/><rect x="13.5" y="13.5" width="7" height="7" rx="2"/></symbol><symbol id="i-verified" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5l2.3 1.8 2.9-.4 1.1 2.7 2.7 1.1-.4 2.9 1.8 2.3-1.8 2.3.4 2.9-2.7 1.1-1.1 2.7-2.9-.4-2.3 1.8-2.3-1.8-2.9.4-1.1-2.7-2.7-1.1.4-2.9L2.5 12l1.8-2.3-.4-2.9 2.7-1.1 1.1-2.7 2.9.4z"/><path d="M8.5 12.2l2.4 2.4 4.6-4.6"/></symbol><symbol id="i-bag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z"/><path d="M9 10V7a3 3 0 0 1 6 0v3"/></symbol><symbol id="i-hardhat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17h18v2.5H3zM5 17a7 7 0 0 1 14 0M12 10V6.5M9 11V8.2M15 11V8.2"/></symbol><symbol id="i-stetho" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v6a4 4 0 0 0 8 0V3M10 13v2.5a4 4 0 0 0 8 0V14"/><circle cx="18" cy="12" r="2"/></symbol><symbol id="i-wa" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.5-4.1A8 8 0 1 1 20 11.5z"/><path d="M9.4 8.6c.3 2.2 2.5 4.6 5 5.2l1.1-1.2-1.7-1-1 .8c-1-.4-1.9-1.3-2.3-2.3l.8-1-1-1.7z"/></symbol><symbol id="i-bot" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="11" rx="3.5"/><path d="M12 8V5M9.5 13v1.6M14.5 13v1.6M2 13v3M22 13v3"/><circle cx="12" cy="4" r="1"/></symbol><symbol id="i-megaphone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10v4l12 5V5z"/><path d="M16 9.5a3 3 0 010 5M7 14.5V19"/></symbol><symbol id="i-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M17 14c2.8 0 5 2.2 5 5"/></symbol><symbol id="i-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2.5"/><path d="M8 11V8a4 4 0 018 0v3"/></symbol><symbol id="i-db" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/></symbol><symbol id="i-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5A1.5 1.5 0 015.5 4h13A1.5 1.5 0 0120 5.5v9a1.5 1.5 0 01-1.5 1.5H10l-4.5 4v-4h0A1.5 1.5 0 014 14.5z"/><path d="M8 9h8M8 12h5"/></symbol><symbol id="i-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V4M4 20h16"/><path d="M8 16v-4M12 16V8M16 16v-6"/></symbol><symbol id="i-workflow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="6.5" height="6.5" rx="1.8"/><rect x="14.5" y="13.5" width="6.5" height="6.5" rx="1.8"/><path d="M9.5 7.2H12a3 3 0 013 3v3.3"/></symbol><symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></symbol><symbol id="i-code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 8l-4 4 4 4M16 8l4 4-4 4M13.5 5l-3 14"/></symbol><symbol id="i-rocket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c3 2 5 5 5 9l-2.5 3h-5L7 12c0-4 2-7 5-9z"/><circle cx="12" cy="10" r="1.6"/><path d="M9.5 15L7 19M14.5 15L17 19M12 15v5"/></symbol><symbol id="i-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></symbol><symbol id="i-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M4 12h16M4 17h16"/></symbol><symbol id="i-hierarchy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="3" width="6" height="5" rx="1.2"/><rect x="3" y="16" width="6" height="5" rx="1.2"/><rect x="15" y="16" width="6" height="5" rx="1.2"/><path d="M12 8v4M6 16v-4h12v4"/></symbol><symbol id="i-heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-8-4.6-8-10.2A4.3 4.3 0 0112 7.2a4.3 4.3 0 018 2.6C20 15.4 12 20 12 20z"/><path d="M7 12.2h2.6l1.4-2.4 2 4.4 1.4-2H17"/></symbol><symbol id="i-gem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h12l4 5-10 11L2 9z"/><path d="M2 9h20M9 4L7 9l5 11M15 4l2 5-5 11"/></symbol><symbol id="i-factory" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V10l6 3.5V10l6 3.5V5h4v16z"/><path d="M8 17h2M13 17h2"/></symbol><symbol id="i-plug" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3v5M15 3v5M6 8h12v3a6 6 0 01-12 0zM12 17v4"/></symbol><symbol id="i-gear" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/></symbol><symbol id="i-target" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r=".6"/></symbol><symbol id="i-quote" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h4v4.2c0 3-1.5 5-4 6M15 7h4v4.2c0 3-1.5 5-4 6"/></symbol><symbol id="i-pause" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5v14M15 5v14"/></symbol><symbol id="i-play" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 5l11 7-11 7z" fill="currentColor"/></symbol><symbol id="i-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></symbol><symbol id="i-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></symbol><symbol id="i-up" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V6M6 12l6-6 6 6"/></symbol><symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3.5 7l8.5 6 8.5-6"/></symbol></defs></svg>
<a class="skip" href="#top">Skip to content</a>
<div class="progress" aria-hidden="true"><i></i></div><main>
<section class="hero" id="top">
  <div class="hero__bg" aria-hidden="true"><i class="blob b1"></i><i class="blob b2"></i><i class="blob b3"></i><i class="grid"></i></div>
  <div class="wrap">
    <div class="hero__top">
      <div class="hero__copy">
        <span class="hero-eb reveal"><span class="hero-eb__ic" aria-hidden="true"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-sparkle"/></svg></span><span class="hero-eb__t">Turning Business Data into Intelligent AI &amp; Automation Solutions</span></span>
        <h1 class="hero__title">
          <span class="strike reveal" style="--d:.08s" data-fit-strike><s>Stop Managing Business Complexity.<svg class="strike__svg" viewBox="0 0 300 24" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M3 14 C 50 9, 92 16, 140 12 S 236 9, 297 11"/><path d="M8 16.5 C 70 13, 130 18, 190 14.5 S 262 13, 293 15"/></svg></s></span>
          <span class="hero__start reveal" style="--d:.2s"><span class="hero__sb">Start Building</span><br><em class="hero__hl">Smarter Business Solutions.</em></span>
        </h1>
      </div>
    </div>
    <div class="hero__mid">
      <p class="hero__lede reveal" style="--d:.18s">We work closely with your teams to understand your business and build technology around your needs, from custom systems and software to AI, automation and digital commerce that simplify operations and drive growth.</p>
    </div>
    <div class="hs reveal" style="--d:.24s">
      <span class="hs-label" aria-hidden="true">What we build</span>
      <ul class="hs-row" role="list" aria-label="What we build">
<?php foreach ([
  ['#DAF1E5', 'i-layers', 'Business Systems', 'Custom ERP, CRM & Business OS'],
  ['#DFEAF9', 'i-store', 'Digital Commerce', 'High-performing online stores'],
  ['#E8E2F5', 'i-chip', 'AI & Automation', 'Less manual work, smarter flows'],
  ['#DCEFF0', 'i-wa', 'WhatsApp Automation', 'API, chatbots & instant replies'],
  ['#F5E1E9', 'i-db', 'Data & Analytics', 'Dashboards, reports & insights'],
  ['#F8E6D6', 'i-trend', 'Marketing & Growth', 'SEO, AEO, GEO & paid ads'],
] as [$ht, $hic, $htitle, $hsub]): ?>
        <li class="hs-s" style="--t:<?= $ht ?>"><a href="#solutions"><span class="hs-ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#<?= $hic ?>"/></svg></span><b><?= h($htitle) ?></b><span class="hs-sub"><?= h($hsub) ?></span></a></li>
<?php endforeach; ?>
      </ul>
    </div>
    <div class="hs-cta-w reveal" style="--d:.3s"><div class="hs-cta">
      <span class="hs-cta__k"><b>8</b><span>Weeks</span></span>
      <p>Looking for a structured business transformation?</p>
      <a class="h10-btn h10-btn--green" href="#program"><span>Explore Our 8-Week Program</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
    </div></div>
    <ul class="pillars" role="list"><li class="reveal" style="--d:0.0s"><span class="pillar__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-puzzle"/></svg></span><h3>No Generic Software</h3><p>Solutions engineered around your business.</p></li><li class="reveal" style="--d:0.05s"><span class="pillar__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-target"/></svg></span><h3>No One-Size-Fits-All</h3><p>Every business gets a tailored approach.</p></li><li class="reveal" style="--d:0.1s"><span class="pillar__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-hierarchy"/></svg></span><h3>Hierarchy-Based Systems</h3><p>Structured roles, permissions and workflows.</p></li><li class="reveal" style="--d:0.15000000000000002s"><span class="pillar__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-shield"/></svg></span><h3>Security-First Engineering</h3><p>Security considered throughout implementation.</p></li></ul>
  </div>
  <div class="strip" aria-label="Services">
    <div class="marquee marquee--strip"><ul class="marquee__track" role="list"><li>ERP Development</li><li>CRM Solutions</li><li>AI Agents</li><li>Workflow Automation</li><li>AI Integration</li><li>WhatsApp API Integration</li><li>Data Integration</li><li>E-Commerce for Conversions</li><li>Secure Engineering</li><li aria-hidden="true">ERP Development</li><li aria-hidden="true">CRM Solutions</li><li aria-hidden="true">AI Agents</li><li aria-hidden="true">Workflow Automation</li><li aria-hidden="true">AI Integration</li><li aria-hidden="true">WhatsApp API Integration</li><li aria-hidden="true">Data Integration</li><li aria-hidden="true">E-Commerce for Conversions</li><li aria-hidden="true">Secure Engineering</li><li aria-hidden="true">ERP Development</li><li aria-hidden="true">CRM Solutions</li><li aria-hidden="true">AI Agents</li><li aria-hidden="true">Workflow Automation</li><li aria-hidden="true">AI Integration</li><li aria-hidden="true">WhatsApp API Integration</li><li aria-hidden="true">Data Integration</li><li aria-hidden="true">E-Commerce for Conversions</li><li aria-hidden="true">Secure Engineering</li><li aria-hidden="true">ERP Development</li><li aria-hidden="true">CRM Solutions</li><li aria-hidden="true">AI Agents</li><li aria-hidden="true">Workflow Automation</li><li aria-hidden="true">AI Integration</li><li aria-hidden="true">WhatsApp API Integration</li><li aria-hidden="true">Data Integration</li><li aria-hidden="true">E-Commerce for Conversions</li><li aria-hidden="true">Secure Engineering</li></ul></div>
  </div>
</section>
<section class="cred" id="credibility">
  <div class="wrap">
    <div class="cred__head">
      <div>
        <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Trusted Technology Partner</span>
        <h2 class="h2 reveal" style="--d:.06s">Engineering Partnerships.<br><span class="hl">Building Business Solutions.</span></h2>
      </div>
      <ul class="stats" role="list"><li class="stat reveal" style="--d:0.0s"><span class="stat__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-grid"/></svg></span><div><b class="stat__n"><span data-count="20">20</span>+</b><span class="stat__l">Industries</span></div></li><li class="stat reveal" style="--d:0.08s"><span class="stat__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-briefcase"/></svg></span><div><b class="stat__n"><span data-count="50">50</span>+</b><span class="stat__l">Projects Delivered</span></div></li><li class="stat reveal" style="--d:0.16s"><span class="stat__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-calendar"/></svg></span><div><b class="stat__n"><span data-count="8">8</span> Weeks</b><span class="stat__l">Implementation Program</span></div></li></ul>
    </div>
  </div>
  <div class="logorows reveal" style="--d:.1s" aria-label="Client logos">
    <div class="marquee marquee--logos"><ul class="marquee__track" role="list"><li class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li><li class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li aria-hidden="true" class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li aria-hidden="true" class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li></ul></div>
    <div class="marquee marquee--logos marquee--rev"><ul class="marquee__track" role="list"><li class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li><li class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li aria-hidden="true" class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li aria-hidden="true" class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li></ul></div>
  </div>
</section>
<?php
$capabilityIds = ['systems', 'commerce', 'automation', 'growth'];
$capabilityOrder = $capabilityIds;
try {
    $capRaw = $pdo->query("SELECT `value` FROM settings WHERE `key`='home10_capabilities_order'")->fetchColumn();
    $capSaved = is_string($capRaw) ? json_decode($capRaw, true) : null;
    if (is_array($capSaved) && isset($capSaved['order']) && is_array($capSaved['order'])) {
        $capabilityOrder = array_values(array_unique(array_filter($capSaved['order'], static fn ($id) => is_string($id) && in_array($id, $capabilityIds, true))));
        $capabilityOrder = array_merge($capabilityOrder, array_values(array_diff($capabilityIds, $capabilityOrder)));
    }
} catch (PDOException $e) {
    // Preserve the original order if site settings are unavailable.
}
$capabilityPositions = array_flip($capabilityOrder);
?>
<section class="caps" id="solutions">
  <div class="wrap"><div class="cx-shell">
    <span class="cx-pill reveal"><span class="cx-pill__dot" aria-hidden="true"></span>Our Core Capabilities</span>
    <h2 class="cx-h2 reveal" style="--d:.06s">Better Systems. Smoother<br>Operations. Stronger Business Growth.</h2>
    <p class="cx-sub reveal" style="--d:.12s">We empower business growth through intelligent solutions, automation, &amp; strategic marketing.</p>
    <div class="cx reveal" style="--d:.18s">
      <div class="cx-tabs" aria-label="Our core capabilities">
        <button type="button" class="cx-tab" id="cxt-1" aria-controls="cxp-1" aria-current="true" style="--ac:#DAF1E5;--pt:#DFEAF9;order:<?= $capabilityPositions['systems'] ?>"><span class="cx-tab__n"><?= sprintf('%02d', $capabilityPositions['systems'] + 1) ?></span><span class="cx-tab__t"><b>Business Systems</b><span>Custom software &amp; operating systems</span></span></button>
        <button type="button" class="cx-tab" id="cxt-2" aria-controls="cxp-2" aria-current="false" style="--ac:#E8E2F5;--pt:#DCEFF0;order:<?= $capabilityPositions['commerce'] ?>"><span class="cx-tab__n"><?= sprintf('%02d', $capabilityPositions['commerce'] + 1) ?></span><span class="cx-tab__t"><b>Digital Commerce</b><span>High-performing ecommerce platforms and connected stores</span></span></button>
        <button type="button" class="cx-tab" id="cxt-3" aria-controls="cxp-3" aria-current="false" style="--ac:#F8E6D6;--pt:#E3E5F6;order:<?= $capabilityPositions['automation'] ?>"><span class="cx-tab__n"><?= sprintf('%02d', $capabilityPositions['automation'] + 1) ?></span><span class="cx-tab__t"><b>AI &amp; Automation</b><span>Intelligent workflows that reduce manual work</span></span></button>
        <button type="button" class="cx-tab" id="cxt-4" aria-controls="cxp-4" aria-current="false" style="--ac:#F5E1E9;--pt:#EDF0D7;order:<?= $capabilityPositions['growth'] ?>"><span class="cx-tab__n"><?= sprintf('%02d', $capabilityPositions['growth'] + 1) ?></span><span class="cx-tab__t"><b>Marketing &amp; Growth</b><span>Data-driven digital marketing strategies</span></span></button>
      </div>
      <div class="cx-panels">
        <div class="cx-panel" role="region" id="cxp-1" aria-labelledby="cxt-1" style="--ac:#DAF1E5;--pt:#DFEAF9;order:<?= $capabilityPositions['systems'] ?>">
          <svg class="cx-ic" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><ellipse cx="32" cy="32" rx="28" ry="11"/><ellipse cx="32" cy="32" rx="28" ry="11" transform="rotate(60 32 32)"/><ellipse cx="32" cy="32" rx="28" ry="11" transform="rotate(120 32 32)"/></svg>
          <span class="cx-tag">Next-Gen Business OS</span>
          <h3 class="cx-h">Still Running Your Growing Business On <span class="cx-mk"><svg class="cx-hi" viewBox="0 0 20 24" aria-hidden="true" focusable="false"><path d="M3 1h10l6 6v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2z" fill="#23A566"/><path d="M13 1v6h6z" fill="#8ED1AE"/><path d="M5 11h10v8H5zM5 15h10M10 11v8" fill="none" stroke="#fff" stroke-width="1.4"/></svg>Spreadsheets,</span> <span class="cx-mk"><svg class="cx-hi" viewBox="-2 0 28 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="5.6" fill="#E5484D"/><path d="M12 9v3.4M12 15v.1" stroke="#fff" stroke-width="1.9" stroke-linecap="round"/><path d="M7 7q-3.6 5 0 10M17 7q3.6 5 0 10M3.6 4q-5.6 8 0 16M20.4 4q5.6 8 0 16" fill="none" stroke="#E5484D" stroke-width="1.7" stroke-linecap="round"/></svg>Disconnected Tools?</span></h3>
          <p class="cx-p">Unify your departments, projects, people and processes with a custom-built Business OS designed around your workflows</p>
          <ul class="cx-checks" role="list"><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Custom ERP &amp; Business OS</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Project &amp; Operations Management</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>CRM &amp; Workflow Management</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Department-Wise Systems Automation</li></ul>
          <span class="cx-stack__l">Technologies we engineer with</span>
          <div class="marquee marquee--tech cx-tech"><ul class="marquee__track" role="list"><li class="cx-logo"><img src="/assets/home10/tech/azure.webp" alt="Microsoft Azure" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/aws.webp" alt="AWS" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/mongodb.webp" alt="MongoDB" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/supabase.webp" alt="Supabase" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/react.webp" alt="React" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/nodejs.webp" alt="Node.js" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/python.webp" alt="Python" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/docker.webp" alt="Docker" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/github.webp" alt="GitHub" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/postgresql.webp" alt="PostgreSQL" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/redis.webp" alt="Redis" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/cloudflare.webp" alt="Cloudflare" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/azure.webp" alt="Microsoft Azure" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/aws.webp" alt="AWS" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/mongodb.webp" alt="MongoDB" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/supabase.webp" alt="Supabase" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/react.webp" alt="React" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/nodejs.webp" alt="Node.js" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/python.webp" alt="Python" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/docker.webp" alt="Docker" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/github.webp" alt="GitHub" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/postgresql.webp" alt="PostgreSQL" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/redis.webp" alt="Redis" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/cloudflare.webp" alt="Cloudflare" loading="lazy"></li></ul></div>
          <a class="cx-btn" href="/book" data-book>Let&#x27;s Build a Solution, Not Just Software<svg class="ic" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
        </div>
        <div class="cx-panel" role="region" id="cxp-2" aria-labelledby="cxt-2" style="--ac:#E8E2F5;--pt:#DCEFF0;order:<?= $capabilityPositions['commerce'] ?>">
          <svg class="cx-ic" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><path d="M6 10h8l7 30h28l6-20H17"/><path d="M23 32h30"/><circle cx="25" cy="50" r="4"/><circle cx="46" cy="50" r="4"/></svg>
          <span class="cx-tag">High-Performance E-Commerce</span>
          <h3 class="cx-h">Ready To Turn Your Ecommerce Vision Into A <span class="cx-mk"><svg class="cx-hi" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 10V6.5a3 3 0 0 1 6 0V10" fill="none" stroke="#4F46C8" stroke-width="1.9" stroke-linecap="round"/><path d="M5 8h14l-1.2 12.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z" fill="#5B5BD6"/><path d="M9.4 15.2l2 2 3.6-3.6" fill="none" stroke="#fff" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>High-Performing Online Store?</span></h3>
          <p class="cx-p">Create conversion-focused ecommerce experiences around your brand story, with custom storefronts and customer journeys designed to increase sales and scale.</p>
          <ul class="cx-checks" role="list"><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Brand Story-Driven Storefronts</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Custom Ecommerce Development</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>App-Free Functionality</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Customer-Centric &amp; Scalable Experiences</li></ul>
          <span class="cx-stack__l">Technologies we engineer with</span>
          <div class="marquee marquee--tech cx-tech"><ul class="marquee__track" role="list"><li class="cx-logo"><img src="/assets/home10/tech/shopify.webp" alt="Shopify" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/razorpay.webp" alt="Razorpay" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/stripe.webp" alt="Stripe" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/shiprocket.webp" alt="Shiprocket" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/shopify.webp" alt="Shopify" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/razorpay.webp" alt="Razorpay" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/stripe.webp" alt="Stripe" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/shiprocket.webp" alt="Shiprocket" loading="lazy"></li></ul></div>
          <a class="cx-btn" href="/book" data-book>Let&#x27;s Build a High-Converting Ecommerce Store<svg class="ic" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
        </div>
        <div class="cx-panel" role="region" id="cxp-3" aria-labelledby="cxt-3" style="--ac:#F8E6D6;--pt:#E3E5F6;order:<?= $capabilityPositions['automation'] ?>">
          <svg class="cx-ic" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><rect x="16" y="16" width="32" height="32" rx="6"/><path d="M24 8v8M32 8v8M40 8v8M24 48v8M32 48v8M40 48v8M8 24h8M8 32h8M8 40h8M48 24h8M48 32h8M48 40h8"/><path d="M32 22l2.6 7.4L42 32l-7.4 2.6L32 42l-2.6-7.4L22 32l7.4-2.6z"/></svg>
          <span class="cx-tag">AI-Powered Business Solutions</span>
          <h3 class="cx-h">Are <span class="cx-mk"><svg class="cx-hi" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9.5" fill="#E8833A"/><path d="M12 6.8V12l3.6 2.2" fill="none" stroke="#fff" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>Manual Tasks</span> And <span class="cx-mk"><svg class="cx-hi" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 11a7.5 7.5 0 0 1 12.8-5.2L18.5 7.5M20 13a7.5 7.5 0 0 1-12.8 5.2L5.5 16.5" fill="none" stroke="#6E56CF" stroke-width="2.2" stroke-linecap="round"/><path d="M18.5 3v4.5H14M5.5 21v-4.5H10" fill="none" stroke="#6E56CF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>Repetitive Workflows</span> Slowing Your Business Growth?</h3>
          <p class="cx-p">Transform repetitive business processes with intelligent AI solutions and automation designed to reduce manual work, improve efficiency and scale operations.</p>
          <ul class="cx-checks" role="list"><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>AI-Powered Business Solutions</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Intelligent Workflow Automation</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>WhatsApp &amp; Communication Automation</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Custom AI Applications &amp; Agents</li></ul>
          <span class="cx-stack__l">Technologies we engineer with</span>
          <div class="marquee marquee--tech cx-tech"><ul class="marquee__track" role="list"><li class="cx-logo"><img src="/assets/home10/tech/openai.webp" alt="OpenAI" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/claude.webp" alt="Claude" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/gemini.webp" alt="Gemini" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/elevenlabs.webp" alt="ElevenLabs" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/n8n.webp" alt="n8n" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/zapier.webp" alt="Zapier" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/make.webp" alt="Make" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/power-automate.webp" alt="Microsoft Power Automate" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/openai.webp" alt="OpenAI" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/claude.webp" alt="Claude" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/gemini.webp" alt="Gemini" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/elevenlabs.webp" alt="ElevenLabs" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/n8n.webp" alt="n8n" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/zapier.webp" alt="Zapier" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/make.webp" alt="Make" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/power-automate.webp" alt="Microsoft Power Automate" loading="lazy"></li></ul></div>
          <a class="cx-btn" href="/book" data-book>Let&#x27;s Automate the Way You Work<svg class="ic" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
        </div>
        <div class="cx-panel" role="region" id="cxp-4" aria-labelledby="cxt-4" style="--ac:#F5E1E9;--pt:#EDF0D7;order:<?= $capabilityPositions['growth'] ?>">
          <svg class="cx-ic" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><path d="M8 56h50"/><path d="M10 46l14-14 10 8 22-24"/><path d="M44 16h12v12"/><path d="M16 56V48M28 56V42M40 56V44M52 56V34"/></svg>
          <span class="cx-tag">Data-Driven Marketing &amp; Growth</span>
          <h3 class="cx-h">Reaching The Right Audience But Struggling To Generate <span class="cx-mk"><svg class="cx-hi" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10" fill="#F5C2D3"/><circle cx="12" cy="12" r="6.4" fill="#D6456F"/><circle cx="12" cy="12" r="2.8" fill="#fff"/></svg>Quality Leads?</span></h3>
          <p class="cx-p">Drive growth through performance marketing, qualified leads, and strategies that increase visibility across search and AI platforms.</p>
          <ul class="cx-checks" role="list"><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Performance Marketing &amp; Paid Campaigns</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Lead Generation &amp; Customer Acquisition</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>SEO, AEO &amp; GEO</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Conversion Optimization &amp; Analytics</li></ul>
          <span class="cx-stack__l">Marketing &amp; growth technology stack</span>
          <div class="marquee marquee--tech cx-tech"><ul class="marquee__track" role="list"><li class="cx-logo"><img src="/assets/home10/tech/meta-ads.webp" alt="Meta Ads" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/google-ads.webp" alt="Google Ads" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/google-analytics.webp" alt="Google Analytics 4" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/search-console.webp" alt="Google Search Console" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/tag-manager.webp" alt="Google Tag Manager" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/semrush.webp" alt="Semrush" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/ahrefs.webp" alt="Ahrefs" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/hotjar.webp" alt="Hotjar" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/meta-ads.webp" alt="Meta Ads" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/google-ads.webp" alt="Google Ads" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/google-analytics.webp" alt="Google Analytics 4" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/search-console.webp" alt="Google Search Console" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/tag-manager.webp" alt="Google Tag Manager" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/semrush.webp" alt="Semrush" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/ahrefs.webp" alt="Ahrefs" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/hotjar.webp" alt="Hotjar" loading="lazy"></li></ul></div>
          <a class="cx-btn" href="/book" data-book>Let&#x27;s Build Your Growth Engine<svg class="ic" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
        </div>
      </div>
    </div>
  </div></div>
</section>
<section class="prob" id="problems">
  <div class="wrap">
    <div class="pb-head">
      <h2 class="pb-h2 reveal"><span class="pb-sr">More Tools. More Complexity. Less Control.</span><span aria-hidden="true">More <span class="pb-tool" data-flip><span class="pb-tool__w">Tools</span><span class="pb-flip"><span>Tools</span><span>Complexity</span><span>Tools</span></span></span> Less Control.</span></h2>
      <p class="pb-sub reveal" style="--d:.08s">We connect your teams, systems &amp; workflows into one streamlined ecosystem, simplifying operations &amp; driving business growth</p>
    </div>
    <div class="pb-notes">
      <div class="pb-col reveal"><div class="pb-note pb-note--p">
        <span class="pb-pin" aria-hidden="true"><i class="pb-pin__base"></i><i class="pb-pin__head"></i></span>
        <div class="pb-note__in">
          <h3 class="pb-label"><span class="pb-label__dot" aria-hidden="true"></span>The Problems</h3>
          <ol class="pb-list" role="list">
            <li><span class="pb-badge">01</span><div><h4>Disconnected Software</h4><p>Your business tools operate in silos without sharing information.</p></div></li>
            <li><span class="pb-badge">02</span><div><h4>Scattered Business Data</h4><p>Important information is spread across spreadsheets and multiple platforms.</p></div></li>
            <li><span class="pb-badge">03</span><div><h4>Manual &amp; Repetitive Work</h4><p>Teams waste valuable time on repetitive tasks and manual updates.</p></div></li>
            <li><span class="pb-badge">04</span><div><h4>Lack of Real-Time Visibility</h4><p>You lack a clear view of business performance and progress.</p></div></li>
            <li><span class="pb-badge">05</span><div><h4>Disconnected Teams &amp; Processes</h4><p>Departments struggle with communication gaps and uncoordinated workflows.</p></div></li>
            <li><span class="pb-badge">06</span><div><h4>Difficult to Scale</h4><p>Existing systems cannot keep up with your growing business.</p></div></li>
            <li><span class="pb-badge">07</span><div><h4>Scattered Marketing &amp; Lead Sources</h4><p>Leads arrive from multiple channels but remain scattered across platforms.</p></div></li>
          </ol>
        </div>
      </div>
      </div>
      <div class="pb-col reveal" style="--d:.1s"><div class="pb-note pb-note--s">
        <span class="pb-pin" aria-hidden="true"><i class="pb-pin__base"></i><i class="pb-pin__head"></i></span>
        <div class="pb-note__in">
          <h3 class="pb-label"><span class="pb-label__dot" aria-hidden="true"></span>The Solutions</h3>
          <ul class="pb-list" role="list">
            <li><span class="pb-badge"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg></span><div><h4>Connected Business Systems</h4><p>Connect your applications, departments and workflows in one ecosystem.</p></div></li>
            <li><span class="pb-badge"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg></span><div><h4>Centralized Business Data</h4><p>Bring business information together for better access and decisions.</p></div></li>
            <li><span class="pb-badge"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg></span><div><h4>Intelligent Workflow Automation</h4><p>Automate repetitive tasks and simplify everyday business operations.</p></div></li>
            <li><span class="pb-badge"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg></span><div><h4>Real-Time Business Intelligence</h4><p>Monitor business performance through centralized dashboards and reports.</p></div></li>
            <li><span class="pb-badge"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg></span><div><h4>Unified Business Operations</h4><p>Align teams, responsibilities and processes through connected workflows.</p></div></li>
            <li><span class="pb-badge"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg></span><div><h4>Scalable Technology Solutions</h4><p>Build flexible systems that adapt as your business grows.</p></div></li>
            <li><span class="pb-badge"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg></span><div><h4>Unified Lead Management</h4><p>Capture, organize and track leads from every marketing channel in one place.</p></div></li>
          </ul>
        </div>
      </div>
      </div>
    </div>
    <div class="pb-cta reveal"><a class="pb-btn" href="/book" data-book><span>Streamline Your Business</span><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a></div>
  </div>
</section>
<?php
// Selected Work. Edit the copy here: one entry per colour panel, in page order;
// the first starts open. 'accent' is the panel's colour, one of the site tints
// mint, sky, lilac or aqua. 'photo' is a greyscale picture the panel tints (the files in
// assets/home10/work are Unsplash stock). 'mock' picks the dashboard drawn over
// it; its sample numbers are in the markup below.
$swProjects = [
    [
        'id' => 'construction',
        'accent' => 'mint',
        'icon' => 'i-building',
        'photo' => '/assets/home10/work/construction.webp',
        'photoAlt' => 'Site crew working among steel reinforcement, seen from above',
        'category' => 'Construction & Real Estate',
        'title' => ['Multiple Construction Operations.', 'One Connected Business System'],
        'text' => 'A custom-engineered Construction Business OS connecting project management, site operations, procurement, materials, contractors and finance, bringing greater visibility and control across everyday construction workflows.',
        'chips' => ['Project Management', 'Site Operations', 'Procurement', 'Material Management', 'Contractor Management', 'Finance (Profit Per Project)'],
        'href' => $h10Case('from-manual-construction-operations-to-a-fully-integrated-erp-ecosystem', '/case-studies'),
        'mock' => 'profit',
        'mockLabel' => 'Sample dashboard: project profitability across four construction projects',
    ],
    [
        'id' => 'ecommerce',
        'accent' => 'sky',
        'icon' => 'i-cart',
        'photo' => '/assets/home10/work/ecommerce.webp',
        'photoAlt' => 'Rail of plain t-shirts on hangers',
        'category' => 'Ecommerce Solutions',
        'title' => ['Beyond Standard Shopify.', '100+ Custom Product Pages Built to Convert.'],
        'text' => 'A custom-developed Shopify ecommerce experience featuring 100+ uniquely designed product pages, intuitive navigation and tailored functionality. Engineered to simplify product discovery, create a seamless shopping journey and support higher conversion rates.',
        'chips' => ['Custom Shopify Development', '100+ Unique Product Pages', 'Custom Functionality', 'User Experience Design', 'Conversion Rate Optimization'],
        'href' => '/case-studies?service=Ecommerce+Solutions',
        'mock' => 'store',
        'mockLabel' => 'Sample dashboard: store revenue, conversion and top product pages',
    ],
    [
        'id' => 'healthcare',
        'accent' => 'lilac',
        'icon' => 'i-heart',
        'photo' => '/assets/home10/work/healthcare.webp',
        'photoAlt' => 'Clinician fitting a wrist brace for a patient',
        'category' => 'Healthcare',
        'title' => ['Connected Digital Solutions for', 'Multi-Branch Healthcare Operations'],
        'text' => 'Digital solutions supporting a multi-branch physiotherapy business through its online presence, digital marketing and operational technology initiatives. Bringing together patient-focused digital experiences and business growth solutions.',
        'chips' => ['Healthcare Digital Solutions', 'SEO', 'Performance Marketing', 'Business Systems', 'Digital Growth'],
        'href' => $h10Case('from-fragmented-clinic-operations-to-a-unified-multi-branch-physiotherapy-management-system', '/case-studies'),
        'mock' => 'branches',
        'mockLabel' => 'Sample dashboard: enquiries and bookings across four branches',
    ],
    [
        'id' => 'automation',
        'accent' => 'aqua',
        'icon' => 'i-chat',
        'photo' => '/assets/home10/work/automation.webp',
        'photoAlt' => 'Paper speech bubble with three dots',
        'category' => 'AI & Business Automation',
        'title' => ['Smarter Customer Communication', 'Through WhatsApp Automation'],
        'text' => 'A business communication automation solution designed to streamline customer interactions and connect WhatsApp communication with structured business workflows, helping businesses manage conversations more efficiently.',
        'chips' => ['WhatsApp Automation', 'Business Communication', 'Workflow Automation', 'System Integration'],
        'href' => '/case-studies',
        'mock' => 'chat',
        'mockLabel' => 'Sample chat: an enquiry answered automatically, booked, and added to the CRM',
    ],
];
$swOrder = [];
$swExpanded = '';
try {
    $swSavedRaw = $pdo->query("SELECT `value` FROM settings WHERE `key`='home10_selected_work'")->fetchColumn();
    $swSaved = is_string($swSavedRaw) ? json_decode($swSavedRaw, true) : null;
    if (is_array($swSaved)) {
        $swOrder = isset($swSaved['order']) && is_array($swSaved['order']) ? $swSaved['order'] : [];
        $swExpanded = isset($swSaved['expanded']) && is_string($swSaved['expanded']) ? $swSaved['expanded'] : '';
    }
} catch (PDOException $e) {
    // Keep the default order and first card if settings are unavailable.
}
$swById = [];
foreach ($swProjects as $project) $swById[$project['id']] = $project;
$swOrdered = [];
foreach ($swOrder as $projectId) {
    if (is_string($projectId) && isset($swById[$projectId])) {
        $swOrdered[] = $swById[$projectId];
        unset($swById[$projectId]);
    }
}
$swProjects = array_merge($swOrdered, array_values($swById));
if (!isset($swById[$swExpanded]) && !in_array($swExpanded, array_column($swProjects, 'id'), true)) $swExpanded = '';
if ($swExpanded === '' && isset($swProjects[0])) $swExpanded = $swProjects[0]['id'];
$swIc = fn (string $id): string => '<svg class="ic" aria-hidden="true" focusable="false"><use href="#' . $id . '"/></svg>';
?>
<section class="sw sw--work" id="case-studies">
  <div class="wrap">
    <header class="sw-head">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Selected Work. Proven Implementation</span>
      <h2 class="sw-h2 reveal" style="--d:.06s">We Understood. We Engineered. We Made It Work</h2>
      <p class="sw-sub reveal" style="--d:.12s">We work closely with businesses to understand their challenges, engineer tailored technology solutions and implement systems that make everyday operations more connected, efficient and scalable.</p>
      <p class="sw-nda reveal" style="--d:.16s"><?= $swIc('i-lock') ?>Client identities remain confidential where protected by NDA</p>
    </header>
  </div>
  <div class="wrap swx-wrap">
    <div class="swx reveal" data-swx style="--n:<?= count($swProjects) - 1 ?>">
<?php foreach ($swProjects as $i => $p): $n = sprintf('%02d', $i + 1); $isOpen = $p['id'] === $swExpanded; ?>
      <article class="swx-p<?= $isOpen ? ' is-open' : '' ?>" data-accent="<?= h($p['accent']) ?>">
        <img class="swx-tex" src="<?= h($p['photo']) ?>" alt="" width="1000" height="1000" loading="lazy" decoding="async">
        <button type="button" class="swx-tab" aria-expanded="<?= $isOpen ? 'true' : 'false' ?>" aria-controls="swx-<?= $i + 1 ?>">
          <span class="swx-ic" aria-hidden="true"><?= $swIc($p['icon']) ?></span>
          <span class="sw-cat"><b><?= $n ?></b><?= h($p['category']) ?></span>
          <span class="swx-plus" aria-hidden="true"></span>
        </button>
        <div class="swx-in" id="swx-<?= $i + 1 ?>"><div class="swx-body">
        <div class="swx-text">
          <h3 class="sw-h3"><?= h($p['title'][0]) ?><br><span class="sw-u"><?= h($p['title'][1]) ?></span></h3>
          <p class="sw-p"><?= h($p['text']) ?></p>
          <a class="sw-cta" href="<?= h($p['href']) ?>"><span>Read Case Study</span><span class="sw-cta__ic"><?= $swIc('i-arrow') ?></span></a>
          <ul class="sw-chips" role="list"><?php foreach ($p['chips'] as $c): ?><li><?= h($c) ?></li><?php endforeach; ?></ul>
        </div>
        <div class="swx-media">
          <img src="<?= h($p['photo']) ?>" alt="<?= h($p['photoAlt']) ?>" width="1000" height="1000" loading="lazy" decoding="async">
          <div class="sw-media swx-mock" role="img" aria-label="<?= h($p['mockLabel']) ?>">
<?php if ($p['mock'] === 'profit'): ?>
          <div class="sw-card" aria-hidden="true">
            <div class="sw-card__hd"><div><b>Project profitability</b><small>Active projects · this financial year</small></div><span class="sw-tabs"><i class="is-on">Margin</i><i>Cost</i><i>Cash</i></span></div>
            <div class="sw-kpis sw-kpis--3"><div class="sw-kpi"><small>Contract</small><b>₹3.85 Cr</b></div><div class="sw-kpi"><small>Cost</small><b>₹2.36 Cr</b></div><div class="sw-kpi"><small>Margin</small><b class="sw-acc">12.4%</b></div></div>
            <ul class="sw-list">
              <li><div><b>Residential Tower A</b><small>Contract ₹1.40 Cr · Cost ₹1.09 Cr</small></div><span class="sw-bar"><i class="pos" style="--w:.88"></i></span><em class="pos">22%</em></li>
              <li><div><b>Villa Community B</b><small>Contract ₹1.10 Cr · Cost ₹0.90 Cr</small></div><span class="sw-bar"><i class="pos" style="--w:.72"></i></span><em class="pos">18%</em></li>
              <li><div><b>Commercial Block C</b><small>Contract ₹0.85 Cr · Cost ₹0.77 Cr</small></div><span class="sw-bar"><i class="warn" style="--w:.36"></i></span><em class="warn">9%</em></li>
              <li><div><b>Interior Fit-out D</b><small>Contract ₹0.50 Cr · Cost ₹0.52 Cr</small></div><span class="sw-bar"><i class="neg" style="--w:.12"></i></span><em class="neg">−3%</em></li>
            </ul>
            <div class="sw-split"><div class="sw-split__hd"><b>Residential Tower A · cost split</b><span>₹1.09 Cr</span></div><span class="sw-stack"><i style="--w:.52;background:var(--accent-500)"></i><i style="--w:.31;background:var(--accent-700)"></i><i style="--w:.04;background:#E0A100"></i><i style="--w:.13;background:#C9CCD6"></i></span><span class="sw-legend"><i style="--c:var(--accent-500)">Material 52%</i><i style="--c:var(--accent-700)">Labour 31%</i><i style="--c:#E0A100">Petty cash 4%</i><i style="--c:#C9CCD6">Other 13%</i></span></div>
          </div>
<?php elseif ($p['mock'] === 'store'): ?>
          <div class="sw-card" aria-hidden="true">
            <div class="sw-card__hd"><div><b>Store performance</b><small>Last 30 days</small></div><span class="sw-tabs"><i class="is-on">30D</i><i>7D</i></span></div>
            <div class="sw-kpis sw-kpis--2">
              <div class="sw-kpi"><small>Revenue</small><b>₹4.8 L <span class="sw-up">+12%</span></b><svg class="sw-spark" viewBox="0 0 120 32" preserveAspectRatio="none"><path class="sw-spark__a" d="M0 26 L15 22 L30 24 L45 17 L60 19 L75 12 L90 14 L105 7 L120 4 V32 H0Z"/><path class="sw-spark__l" d="M0 26 L15 22 L30 24 L45 17 L60 19 L75 12 L90 14 L105 7 L120 4"/></svg></div>
              <div class="sw-kpi"><small>Conversion</small><b>3.9%</b><span class="sw-kpi__note">Store average</span></div>
            </div>
            <div class="sw-list__hd"><span>Top product pages</span><span>Conv.</span></div>
            <ul class="sw-list">
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-gem') ?></span><div><b>Handwoven Throw</b><small>2,140 visits</small></div><span class="sw-bar"><i style="--w:.92"></i></span><em>5.6%</em></li>
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-cart') ?></span><div><b>Ceramic Mug Set</b><small>1,860 visits</small></div><span class="sw-bar"><i style="--w:.78"></i></span><em>4.7%</em></li>
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-heart') ?></span><div><b>Cotton Kurta</b><small>1,520 visits</small></div><span class="sw-bar"><i style="--w:.62"></i></span><em>3.8%</em></li>
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-briefcase') ?></span><div><b>Leather Wallet</b><small>1,190 visits</small></div><span class="sw-bar"><i style="--w:.5"></i></span><em>3.0%</em></li>
            </ul>
          </div>
<?php elseif ($p['mock'] === 'branches'): ?>
          <div class="sw-card" aria-hidden="true">
            <div class="sw-card__hd"><div><b>Branch overview</b><small>All branches · this month</small></div><span class="sw-tabs"><i class="is-on">Month</i><i>Week</i></span></div>
            <div class="sw-kpis sw-kpis--2"><div class="sw-kpi"><small>Open enquiries</small><b>38</b></div><div class="sw-kpi"><small>Bookings</small><b class="sw-acc">142</b></div></div>
            <div class="sw-list__hd"><span>Enquiries → bookings</span><span>Booked</span></div>
            <ul class="sw-list">
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-building') ?></span><div><b>Branch 01</b><small>52 enquiries → 41 bookings</small></div><span class="sw-bar sw-bar--two"><i style="--w:.79"></i></span><em>41</em></li>
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-building') ?></span><div><b>Branch 02</b><small>44 enquiries → 37 bookings</small></div><span class="sw-bar sw-bar--two"><i style="--w:.84"></i></span><em>37</em></li>
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-building') ?></span><div><b>Branch 03</b><small>39 enquiries → 34 bookings</small></div><span class="sw-bar sw-bar--two"><i style="--w:.87"></i></span><em>34</em></li>
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-building') ?></span><div><b>Branch 04</b><small>36 enquiries → 30 bookings</small></div><span class="sw-bar sw-bar--two"><i style="--w:.83"></i></span><em>30</em></li>
            </ul>
            <div class="sw-alert"><span><b>12 enquiries</b> awaiting follow-up</span><span class="sw-alert__btn">Assign</span></div>
          </div>
<?php else: ?>
          <div class="sw-chat" aria-hidden="true">
            <div class="sw-phone">
              <div class="sw-phone__bar"><span class="sw-avatar">CS</span><div><b>Customer Support</b><small>Auto-replies on</small></div></div>
              <div class="sw-phone__body">
                <span class="sw-bub sw-bub--in">Hi, do you have a slot for a demo this week?</span>
                <span class="sw-bub sw-bub--out"><small>Auto-reply</small>Thanks for reaching out! Open slots: Tue 11:00 AM or Thu 3:00 PM.</span>
                <span class="sw-bub sw-bub--in">Thursday 3 PM works.</span>
                <span class="sw-bub sw-bub--out sw-bub--card"><span class="sw-bub__ok"><?= $swIc('i-check') ?></span><span><b>Booking confirmed</b>Thu · 3:00 PM · Demo call</span></span>
              </div>
            </div>
            <div class="sw-card sw-flow">
              <b class="sw-flow__t">Automation flow</b>
              <ol class="sw-steps">
                <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-chat') ?></span><span>WhatsApp message</span></li>
                <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-sparkle') ?></span><span>Qualify lead</span></li>
                <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-db') ?></span><span>Add to CRM</span></li>
              </ol>
            </div>
          </div>
<?php endif; ?>
          </div>
        </div>
        </div></div>
      </article>
<?php endforeach; ?>
    </div>
    <div class="swx-foot">
      <p class="sw-foot">Product screens use sample data.</p>
      <div class="swx-nav" role="group" aria-label="Browse the case studies">
        <button type="button" class="swx-arrow" data-swx-step="-1" aria-label="Previous case study"><?= $swIc('i-left') ?></button>
        <button type="button" class="swx-arrow" data-swx-step="1" aria-label="Next case study"><?= $swIc('i-right') ?></button>
      </div>
    </div>
  </div>
  <div class="wrap">
    <div class="more reveal">
      <div><span class="pill pill--mint"><span class="pill__dot" aria-hidden="true"></span>Explore More</span><h3>Technology for Every Stage of Your Business.</h3>
      <p>From custom business systems and software engineering to digital commerce, AI implementation, automation and marketing, discover the complete range of solutions we build to help businesses move forward.</p></div>
      <div class="cta-stub"><a class="h10-btn h10-btn--green" href="#solutions"><span>Explore Our Service Portfolio</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a></div>
    </div>
  </div>
</section>
<section class="appr" id="approach">
  <div class="wrap">
    <div class="appr__head">
      <div>
        <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Working With Your Team. Building for Your Business.</span>
        <h2 class="h2 reveal" style="--d:.06s">Your Business Has Its Own Way of Working. Your Technology Should Too.</h2>
      </div>
      <div class="appr__side reveal" style="--d:.12s">
        <p class="lede">We work directly with your teams to understand how your business operates, identify challenges and uncover opportunities. Our engineers collaborate with your people to design, build and implement technology that fits your actual workflows — from custom software and system integrations to AI and automation.</p>
        <a class="h10-btn h10-btn--dark" href="#program"><span>Explore Our Engineering Approach</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
      </div>
    </div>
    <div class="flow" data-flow>
      <div class="flow__rail" aria-hidden="true"><i class="flow__fill"></i><i class="flow__dot"></i></div>
      <ol class="steps" role="list">
      <li class="step reveal" style="--d:0.0s">
        <div class="step__card tk">
          <div class="tk-a">
            <div class="tk-top"><span class="tk-cls">Discover</span><span class="tk-adm">Admit · Your Team</span></div>
            <div class="step__art il"><svg class="il-svg" viewBox="0 0 200 140" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="hw-discover" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="200" height="140"/><rect width="200" height="140" fill="url(#hw-discover)"/><path class="ln" d="M100 40 V64 M100 64 H52 V84 M100 64 H148 V84" /><rect class="c" x="78" y="22" width="44" height="22" rx="8" /><rect class="g" x="88" y="30" width="24" height="6" rx="3.0" /><rect class="c" x="30" y="84" width="44" height="22" rx="8" /><rect class="k2" x="40" y="92" width="24" height="6" rx="3.0" /><rect class="c" x="126" y="84" width="44" height="22" rx="8" /><rect class="k2" x="136" y="92" width="24" height="6" rx="3.0" /><g class="scan"><circle cx="112" cy="84" r="26" class="lens"/><path class="gl" d="M131 103 L152 124" stroke-width="6"/></g></svg></div>
          </div>
          <div class="tk-b">
            <h3 class="tk-h">Discover the Business</h3>
            <p>We study your existing processes, teams, tools, workflows and operational challenges to identify where technology can create meaningful value.</p>
          </div>
        </div>
      </li>
      <li class="step reveal" style="--d:0.1s">
        <div class="step__card tk">
          <div class="tk-a">
            <div class="tk-top"><span class="tk-cls">Engineer</span><span class="tk-adm">Admit · Your Team</span></div>
            <div class="step__art il"><svg class="il-svg" viewBox="0 0 200 140" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="hw-engineer" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="200" height="140"/><rect width="200" height="140" fill="url(#hw-engineer)"/><rect class="c" x="22" y="18" width="156" height="104" rx="14" /><path class="ln faint" d="M22 44 H178 M22 70 H178 M22 96 H178 M56 18 V122 M100 18 V122 M144 18 V122" /><rect class="gs2" x="36" y="28" width="34" height="22" rx="6" /><rect class="g" x="130" y="28" width="34" height="22" rx="6" /><rect class="gs2" x="84" y="84" width="34" height="22" rx="6" /><path class="gl flow" d="M70 39 H130 M147 50 V66 H101 V84" stroke-dasharray="4 5"/><circle class="g" cx="147" cy="66" r="4" /></svg></div>
          </div>
          <div class="tk-b">
            <h3 class="tk-h">Engineer the Workflows</h3>
            <p>We map processes, redesign workflows and architect the right combination of custom software, existing platforms and AI capabilities.</p>
          </div>
        </div>
      </li>
      <li class="step reveal" style="--d:0.2s">
        <div class="step__card tk">
          <div class="tk-a">
            <div class="tk-top"><span class="tk-cls">Build</span><span class="tk-adm">Admit · Your Team</span></div>
            <div class="step__art il"><svg class="il-svg" viewBox="0 0 200 140" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="hw-build" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="200" height="140"/><rect width="200" height="140" fill="url(#hw-build)"/><rect class="c" x="26" y="18" width="120" height="96" rx="14" /><circle class="k2" cx="42" cy="32" r="3.5" /><circle class="k2" cx="54" cy="32" r="3.5" /><circle class="k2" cx="66" cy="32" r="3.5" /><rect class="g typeln" x="40" y="46" width="40" height="6" rx="3.0" style="--d:0.0s"/><rect class="k2 typeln" x="48" y="58" width="60" height="6" rx="3.0" style="--d:0.25s"/><rect class="k2 typeln" x="48" y="70" width="32" height="6" rx="3.0" style="--d:0.5s"/><rect class="g typeln" x="40" y="82" width="52" height="6" rx="3.0" style="--d:0.75s"/><rect class="k2 typeln" x="48" y="94" width="70" height="6" rx="3.0" style="--d:1.0s"/><g class="float" style="--d:0.3s"><rect class="c" x="128" y="66" width="54" height="40" rx="12" /><svg x="142" y="74" width="26" height="26" style="color:var(--green)" ><use href="#i-plug"/></svg></g></svg></div>
          </div>
          <div class="tk-b">
            <h3 class="tk-h">Build & Integrate</h3>
            <p>We build custom applications, integrate existing systems and implement AI-powered workflows that solve practical business problems.</p>
          </div>
        </div>
      </li>
      <li class="step reveal" style="--d:0.30000000000000004s">
        <div class="step__card tk">
          <div class="tk-a">
            <div class="tk-top"><span class="tk-cls">Deploy</span><span class="tk-adm">Admit · Your Team</span></div>
            <div class="step__art il"><svg class="il-svg" viewBox="0 0 200 140" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="hw-deploy" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="200" height="140"/><rect width="200" height="140" fill="url(#hw-deploy)"/><circle cx="100" cy="70" r="42" class="ring spin" fill="none"/><circle cx="100" cy="70" r="30" class="g"/><svg x="84" y="54" width="32" height="32" style="color:#fff" ><use href="#i-rocket"/></svg><g class="float" style="--d:0.2s"><rect class="c" x="24" y="92" width="44" height="28" rx="8" /><rect class="g rise" x="32" y="106" width="6" height="6" rx="2" style="--d:0.0s"/><rect class="g rise" x="42" y="100" width="6" height="12" rx="2" style="--d:0.15s"/><rect class="g rise" x="52" y="94" width="6" height="18" rx="2" style="--d:0.3s"/></g><g class="float" style="--d:0.7s"><rect class="c" x="134" y="22" width="44" height="26" rx="13" /><circle class="g blink" cx="148" cy="35" r="4" /><rect class="k" x="156" y="32" width="16" height="6" rx="3.0" /></g></svg></div>
          </div>
          <div class="tk-b">
            <h3 class="tk-h">Deploy & Evolve</h3>
            <p>We deploy solutions, work with your teams, support adoption and continuously improve systems based on real operational feedback.</p>
          </div>
        </div>
      </li></ol>
    </div>
  </div>
</section>
<?php
$pgWeeks = [
  ['Discover & Audit', 'Understand Your Business from the Inside.', ['Existing Workflows', 'Organizational Hierarchy', 'Work Culture', 'Existing Systems & Software'], ''],
  ['Design & Engineer', 'Design Technology Around Your Business.', ['Workflow Mapping', 'Process Redesign', 'System Architecture', 'Solution Planning'], ''],
  ['Build & Configure', 'Build Solutions Around Real Business Needs.', ['Custom Development', 'System Configuration', 'Feature Engineering', 'Application Development'], ''],
  ['Integrate & Prepare', 'Connect Systems and Prepare for Implementation.', ['API Integration', 'Data Structuring', 'System Testing', 'Deployment Preparation'], ''],
  ['Live Data Integration', 'Bring Your Business Data into the System.', ['Lead Migration', 'Customer Records', 'Project Data', 'Financial Records'], ''],
  ['Workflow Implementation', 'Connect Your Teams, Systems and Processes.', ['Department Integration', 'Workflow Automation', 'Task Coordination', 'Process Activation'], ''],
  ['Team Deployment & Training', 'Prepare Your People to Work with the New Systems.', ['Employee Onboarding', 'Role Assignment', 'Hands-on Training', 'User Enablement'], ''],
  ['Go Live & Optimization', 'Run Your Business Through Connected Systems.', [], 'A connected business environment where everyday operations, from leads to profit, are tracked through structured workflows.'],
];
$pgTints = ['#DAF1E5', '#DFEAF9', '#E8E2F5', '#DCEFF0', '#F8E6D6', '#E3E5F6', '#F5E1E9', '#EDF0D7'];
$pgPhases = [0 => ['Phase 01', 'Engineer & Build'], 4 => ['Phase 02', 'Implement & Go Live']];
?>
<section class="prog" id="program">
  <div class="wrap">
    <div class="pg-head">
      <div class="pg-head__l">
        <span class="pg-pill reveal"><span class="pill__dot" aria-hidden="true"></span>A Structured Business Implementation Program</span>
        <h2 class="pg-h2 reveal" style="--d:.06s">Transform the Way Your Business Works. In 8 Weeks.</h2>
      </div>
      <div class="pg-lede reveal" style="--d:.12s">
        <p>Move beyond <b>disconnected tools</b>, manual processes and operational bottlenecks.</p>
        <p>Our structured 8-week transformation program helps businesses understand their challenges, <b>redesign workflows, implement intelligent technology</b> and establish connected business operations.</p>
      </div>
    </div>
    <div class="pg" data-pg>
      <div class="pg-list">
<?php foreach ($pgWeeks as $i => [$stage, $title, $tags, $body]): $n = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT); ?>
<?php if (isset($pgPhases[$i])): ?>
        <div class="pg-phase reveal"><span class="pg-phase__k"><?= h($pgPhases[$i][0]) ?></span><span class="pg-phase__line" aria-hidden="true"></span><span class="pg-phase__t"><?= h($pgPhases[$i][1]) ?></span></div>
<?php endif; ?>
        <div class="pg-week reveal" data-stage="<?= h($stage) ?>" style="--t:<?= $pgTints[$i] ?>">
          <span class="pg-wk">Week <?= $n ?></span>
          <article class="pg-card">
            <span class="pg-card__k"><?= h($stage) ?></span>
            <h3 class="pg-card__h"><?= h($title) ?></h3>
<?php if ($tags): ?>
            <p class="pg-card__p"><?= h(implode(' · ', $tags)) ?></p>
<?php else: ?>
            <p class="pg-card__p"><?= h($body) ?></p>
<?php endif; ?>
          </article>
        </div>
<?php endforeach; ?>
      </div>
      <div class="pg-aside" aria-hidden="true">
        <div class="pg-stage" style="--t:<?= $pgTints[0] ?>">
          <div class="pg-cal">
            <span class="pg-cal__ring pg-cal__ring--l"></span><span class="pg-cal__ring pg-cal__ring--r"></span>
            <div class="pg-cal__frame">
              <div class="pg-cal__pad">
                <span class="pg-cal__sheet pg-cal__sheet--3"></span>
                <span class="pg-cal__sheet pg-cal__sheet--2"></span>
                <div class="pg-cal__page"><span class="pg-cal__grid"><?php foreach (["Mon","Tue","Wed","Thu","Fri","Sat","Sun"] as $dn): ?><span><?= $dn ?></span><?php endforeach; ?><?php for ($c = 0; $c < 35; $c++): ?><i></i><?php endfor; ?></span><b data-cal-n>1</b></div>
              </div>
              <div class="pg-cal__label" data-cal-l>Week</div>
            </div>
          </div>
          <div class="pg-cap"><span data-cal-k>Week 01</span><b data-cal-t>Discover &amp; Audit</b></div>
          <ol class="pg-dots"><?php for ($d = 0; $d < 8; $d++): ?><li></li><?php endfor; ?></ol>
        </div>
      </div>
    </div>
    <div class="pg-cta reveal">
      <a class="cx-btn" href="/book" data-book>Begin Your 8-Week Implementation Journey<svg class="ic" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
      <p class="fine">The 8-week timeline is based on agreed scope, system complexity, data readiness and client participation. Additional integrations or larger requirements may require an extended implementation period.</p>
    </div>
  </div>
</section>
<section class="data" id="data">
  <div class="wrap">
    <div class="data__head center">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>From Scattered Information to Smarter Decisions</span>
      <h2 class="h2 reveal" style="--d:.06s">Your Business Generates Data.<br>We Turn It Into Intelligence!</h2>
      <ul class="ticker reveal" role="list" style="--d:.1s"><li>Scattered Data</li><li>Missed Activity Logs</li><li>Disconnected Customer Records</li></ul>
      <p class="lede reveal" style="--d:.12s">We bring scattered business data together from multiple platforms, conversations, spreadsheets and existing systems. By structuring, connecting and analysing this information, we help businesses gain clear visibility, track activities and make smarter decisions.</p>
    </div>
    <div class="data__grid">
      <div class="data__viz il reveal" data-viz><svg class="il-svg" viewBox="0 0 680 560" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><filter id="dv-sh" x="-20%" y="-20%" width="140%" height="150%"><feDropShadow dx="0" dy="1" stdDeviation="1" flood-color="#0f2a1c" flood-opacity=".06"/><feDropShadow dx="0" dy="10" stdDeviation="12" flood-color="#0f2a1c" flood-opacity=".09"/></filter><pattern id="dv-d" width="18" height="18" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="680" height="560"/><rect width="680" height="560" fill="url(#dv-d)"/><g class="stream dv-s1" data-stream="1"><path class="ln flow" d="M198 82 C 236 82, 228 280, 260 280" style="--d:0s"/><circle r="3.5" class="g"><animateMotion dur="2.6s" begin="0s" repeatCount="indefinite" path="M198 82 C 236 82, 228 280, 260 280"/></circle></g><g class="stream dv-s2" data-stream="2"><path class="ln flow" d="M198 214 C 236 214, 232 280, 260 280" style="--d:.2s"/><circle r="3.5" class="g"><animateMotion dur="2.4s" begin="-.6s" repeatCount="indefinite" path="M198 214 C 236 214, 232 280, 260 280"/></circle></g><g class="stream dv-s3" data-stream="3"><path class="ln flow" d="M198 346 C 236 346, 232 280, 260 280" style="--d:.4s"/><circle r="3.5" class="g"><animateMotion dur="2.4s" begin="-1.2s" repeatCount="indefinite" path="M198 346 C 236 346, 232 280, 260 280"/></circle></g><g class="stream dv-s4" data-stream="4"><path class="ln flow" d="M198 478 C 236 478, 228 280, 260 280" style="--d:.6s"/><circle r="3.5" class="g"><animateMotion dur="2.6s" begin="-1.8s" repeatCount="indefinite" path="M198 478 C 236 478, 228 280, 260 280"/></circle></g><path class="ln flow" d="M320 280 H 360" style="--d:.3s"/><circle r="3.5" class="g"><animateMotion dur="1.4s" repeatCount="indefinite" path="M320 280 H 360"/></circle><g class="src" data-src="1"><rect class="dv-card" x="18" y="30" width="180" height="104" rx="16"/><rect x="18" y="50" width="3.5" height="64" rx="1.75" fill="#3F6FC4"/><rect x="32" y="44" width="30" height="30" rx="9" fill="#DFEAF9"/><path class="dv-gl" d="M42 66c0-3.3 2.2-5 5-5s5 1.7 5 5M47 58.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><text class="dv-t" x="72" y="56">Customer &amp; Sales</text><text class="dv-s" x="72" y="69">CRM · WhatsApp · Email</text><text class="dv-n" x="32" y="106">1,284</text><text class="dv-s" x="32" y="121">leads this month</text><rect x="142" y="94" width="42" height="18" rx="9" fill="#DAF1E5"/><text class="dv-up" x="163" y="106.5" text-anchor="middle">+18%</text></g><g class="src" data-src="2"><rect class="dv-card" x="18" y="162" width="180" height="104" rx="16"/><rect x="18" y="182" width="3.5" height="64" rx="1.75" fill="#C77A3E"/><rect x="32" y="176" width="30" height="30" rx="9" fill="#F8E6D6"/><path class="dv-gl" d="M40 188h4l8-5v16l-8-5h-4zM44 194l1.5 5"/><text class="dv-t" x="72" y="188">Marketing</text><text class="dv-s" x="72" y="201">Meta · Google · Website</text><text class="dv-n" x="32" y="238">4.2x</text><text class="dv-s" x="32" y="253">return on ad spend</text><rect x="142" y="226" width="42" height="18" rx="9" fill="#DAF1E5"/><text class="dv-up" x="163" y="238.5" text-anchor="middle">+0.6x</text></g><g class="src" data-src="3"><rect class="dv-card" x="18" y="294" width="180" height="104" rx="16"/><rect x="18" y="314" width="3.5" height="64" rx="1.75" fill="#7656C2"/><rect x="32" y="308" width="30" height="30" rx="9" fill="#E8E2F5"/><path class="dv-gl" d="M40 316h6v6h-6zM50 326h6v6h-6zM43 322v7h7"/><text class="dv-t" x="72" y="320">Operations</text><text class="dv-s" x="72" y="333">Tasks · Teams · Inventory</text><text class="dv-n" x="32" y="370">96%</text><text class="dv-s" x="32" y="385">tasks done on time</text><rect x="142" y="358" width="42" height="18" rx="9" fill="#DAF1E5"/><text class="dv-up" x="163" y="370.5" text-anchor="middle">+4%</text></g><g class="src" data-src="4"><rect class="dv-card" x="18" y="426" width="180" height="104" rx="16"/><rect x="18" y="446" width="3.5" height="64" rx="1.75" fill="#7E8F2E"/><rect x="32" y="440" width="30" height="30" rx="9" fill="#EDF0D7"/><path class="dv-gl" d="M40 464v-6M45 464v-10M50 464v-4M55 464v-12M38 464h20"/><text class="dv-t" x="72" y="452">Finance</text><text class="dv-s" x="72" y="465">Invoices · Payments · Costs</text><text class="dv-n" x="32" y="502">₹18.6L</text><text class="dv-s" x="32" y="517">revenue this month</text><rect x="142" y="490" width="42" height="18" rx="9" fill="#DAF1E5"/><text class="dv-up" x="163" y="502.5" text-anchor="middle">+12%</text></g><circle cx="290" cy="280" r="52" class="dv-orbit"/><g><animateTransform attributeName="transform" type="rotate" from="0 290 280" to="360 290 280" dur="14s" repeatCount="indefinite"/><circle cx="290" cy="228" r="3.5" fill="#3F6FC4"/><circle cx="342" cy="280" r="3" fill="#C77A3E"/><circle cx="290" cy="332" r="3.5" fill="#7656C2"/><circle cx="238" cy="280" r="3" fill="#7E8F2E"/></g><circle cx="290" cy="280" r="40" class="dv-ring"/><circle cx="290" cy="280" r="30" class="dv-hub"/><circle cx="290" cy="280" r="30" class="dv-hub-in"/><path d="M290 266l3.2 8.8 8.8 3.2-8.8 3.2-3.2 8.8-3.2-8.8-8.8-3.2 8.8-3.2z" fill="#fff"/><rect x="238" y="342" width="104" height="22" rx="11" fill="#0f3a24"/><text class="dv-tw" x="290" y="356.5" text-anchor="middle">AI + Automation</text><text class="dv-s" x="290" y="378" text-anchor="middle">clean · connect · predict</text><rect class="dv-card" x="360" y="24" width="300" height="512" rx="18"/><rect x="376" y="36" width="22" height="22" rx="7" fill="#0f3a24"/><text class="dv-tw" x="387" y="51" text-anchor="middle">D</text><text class="dv-h" x="406" y="46">Business Dashboard</text><text class="dv-ax" x="406" y="57" text-anchor="start">Drawlead OS · Nov 2026</text><rect x="604" y="38" width="42" height="18" rx="9" fill="#DAF1E5"/><circle cx="614" cy="47" r="3" fill="#1d7a49" class="blink"/><text class="dv-up" x="621" y="50.5">Live</text><rect class="dv-tile" x="376" y="66" width="84" height="60" rx="10"/><text class="dv-s" x="386" y="84">Revenue</text><text class="dv-v" x="386" y="103">₹18.6L</text><text class="dv-up" x="386" y="117">▲ 12%</text><polyline class="dv-spark" points="426,116 432,112 438,114 444,107 450,103"/><rect class="dv-tile" x="468" y="66" width="84" height="60" rx="10"/><text class="dv-s" x="478" y="84">New leads</text><text class="dv-v" x="478" y="103">1,284</text><text class="dv-up" x="478" y="117">▲ 18%</text><polyline class="dv-spark" points="518,115 524,113 530,109 536,111 542,104"/><rect class="dv-tile" x="560" y="66" width="84" height="60" rx="10"/><text class="dv-s" x="570" y="84">Orders</text><text class="dv-v" x="570" y="103">642</text><text class="dv-up" x="570" y="117">▲ 9%</text><polyline class="dv-spark" points="610,114 616,115 622,110 628,108 634,105"/><rect class="dv-tile" x="376" y="138" width="268" height="136" rx="12"/><text class="dv-h" x="390" y="158">Revenue trend</text><text class="dv-s" x="630" y="158" text-anchor="end">₹ lakh · last 6 months</text><path d="M392 246H628M392 223H628M392 199H628" class="dv-grid"/><polygon points="392,236.7 439,227.3 486,221.9 534,208.7 581,192.3 628,179.1 628,246 392,246" fill="#1d7a49" opacity=".1"/><polyline points="392,236.7 439,227.3 486,221.9 534,208.7 581,192.3 628,179.1" class="dv-line"/><path d="M628 184V246" class="dv-guide"/><circle cx="628" cy="179.1" r="5" fill="#1d7a49" stroke="#fff" stroke-width="2.5"/><rect x="548" y="166" width="70" height="22" rx="7" fill="#0f3a24"/><text class="dv-tw" x="583" y="180.5" text-anchor="middle">Nov · ₹18.6L</text><text class="dv-ax" x="392" y="263" text-anchor="middle">Jun</text><text class="dv-ax" x="439" y="263" text-anchor="middle">Jul</text><text class="dv-ax" x="486" y="263" text-anchor="middle">Aug</text><text class="dv-ax" x="534" y="263" text-anchor="middle">Sep</text><text class="dv-ax" x="581" y="263" text-anchor="middle">Oct</text><text class="dv-ax" x="628" y="263" text-anchor="middle">Nov</text><rect class="dv-tile" x="376" y="284" width="150" height="122" rx="12"/><text class="dv-h" x="390" y="303">Leads by source</text><rect x="392" y="326" width="20" height="60" rx="4" fill="#1d7a49"/><rect x="424" y="340" width="20" height="46" rx="4" fill="#5bb98a"/><rect x="456" y="344" width="20" height="42" rx="4" fill="#5bb98a"/><rect x="488" y="352" width="20" height="34" rx="4" fill="#9fd3b6"/><text class="dv-ax" x="402" y="321" text-anchor="middle">412</text><text class="dv-ax" x="434" y="335" text-anchor="middle">318</text><text class="dv-ax" x="466" y="339" text-anchor="middle">290</text><text class="dv-ax" x="498" y="347" text-anchor="middle">236</text><text class="dv-ax" x="402" y="398" text-anchor="middle">Meta</text><text class="dv-ax" x="434" y="398" text-anchor="middle">Google</text><text class="dv-ax" x="466" y="398" text-anchor="middle">WA</text><text class="dv-ax" x="498" y="398" text-anchor="middle">Web</text><rect class="dv-tile" x="534" y="284" width="110" height="122" rx="12"/><text class="dv-h" x="548" y="303">On-time tasks</text><circle cx="589" cy="352" r="28" fill="none" stroke="#e3ebe6" stroke-width="10"/><circle cx="589" cy="352" r="28" fill="none" stroke="#1d7a49" stroke-width="10" stroke-linecap="round" stroke-dasharray="168.9 175.9" transform="rotate(-90 589 352)"/><text class="dv-v" x="589" y="357" text-anchor="middle">96%</text><text class="dv-ax" x="589" y="398" text-anchor="middle">312 of 325 tasks</text><rect class="dv-tile" x="376" y="416" width="268" height="104" rx="12"/><text class="dv-h" x="390" y="435">Today's follow-ups</text><text class="dv-r" x="390" y="458">Ravi K. · Construction</text><rect x="564" y="447" width="66" height="17" rx="8.5" fill="#DFEAF9"/><text class="dv-pl" x="597" y="459" text-anchor="middle">Call 11:30</text><text class="dv-r" x="390" y="482">Priya Foods · D2C</text><rect x="564" y="471" width="66" height="17" rx="8.5" fill="#F8E6D6"/><text class="dv-pl" x="597" y="483" text-anchor="middle">Proposal</text><text class="dv-r" x="390" y="506">City Clinic · Healthcare</text><rect x="564" y="495" width="66" height="17" rx="8.5" fill="#DAF1E5"/><text class="dv-pl" x="597" y="507" text-anchor="middle">Won ✓</text></svg></div>
      <div class="data__cards">
      <article class="dcard spot reveal" data-src="1" style="--d:0.0s" tabindex="0">
        <div class="dcard__top"><span class="dcard__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-users"/></svg></span><h3>01. Customer & Sales Data</h3></div>
        <p>Bring every customer interaction and sales record together.</p>
        <ul class="dlist" role="list"><li>Customer Profiles &amp; Contact Details</li><li>Leads &amp; Enquiries</li><li>WhatsApp &amp; Email Conversations</li><li>Sales Pipeline &amp; Follow-ups</li><li>Customer Purchase History</li><li>Call &amp; Interaction Records</li></ul>
      </article>
      <article class="dcard spot reveal" data-src="2" style="--d:0.06s" tabindex="0">
        <div class="dcard__top"><span class="dcard__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-megaphone"/></svg></span><h3>02. Marketing & Campaign Data</h3></div>
        <p>Connect marketing information from multiple channels to understand campaign performance.</p>
        <ul class="dlist" role="list"><li>Meta &amp; Google Ads Data</li><li>Website Traffic &amp; Analytics</li><li>Social Media Engagement</li><li>Campaign Performance</li><li>Lead Sources &amp; Conversion Data</li><li>SEO &amp; Search Performance</li></ul>
      </article>
      <article class="dcard spot reveal" data-src="3" style="--d:0.12s" tabindex="0">
        <div class="dcard__top"><span class="dcard__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-workflow"/></svg></span><h3>03. Business Operations Data</h3></div>
        <p>Bring operational information from departments, teams and business systems into one connected view.</p>
        <ul class="dlist" role="list"><li>Project &amp; Task Records</li><li>Employee &amp; Team Activities</li><li>Workflow &amp; Process Logs</li><li>Department-Wise Reports</li><li>Inventory &amp; Resource Data</li><li>Vendor &amp; Procurement Records</li></ul>
      </article>
      <article class="dcard spot reveal" data-src="4" style="--d:0.18s" tabindex="0">
        <div class="dcard__top"><span class="dcard__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-wallet"/></svg></span><h3>Financial & Performance Data</h3></div>
        <p>Connect financial records and business performance metrics to understand revenue, costs and profitability.</p>
        <ul class="dlist" role="list"><li>Sales &amp; Revenue Records</li><li>Expenses &amp; Transactions</li><li>Invoices &amp; Payments</li><li>Budget &amp; Cash Flow</li><li>Project-Wise Costs</li><li>Profitability &amp; Financial Reports</li></ul>
      </article></div>
    </div>
    <div class="databanner reveal">
      <div><h3>Stop Looking at Scattered Reports.<br>Start Understanding Your Business.</h3><p>Connect your business data and turn information into actionable intelligence.</p></div>
      <div class="cta-stub"><a class="h10-btn h10-btn--green" href="#contact"><span>Explore Data Intelligence</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a></div>
    </div>
  </div>
</section>
<?php
// Industries. One entry per row, in page order, with the mockup on the left for
// 01, 03 and 05. The mockups share their parts (.sw-card and so on) with
// Selected Work above.
// 'title' is the question in two parts; the second part is underlined.
// 'accent' is one of indigo, rose, green, amber, cyan.
$indRows = [
    [
        'accent' => 'indigo',
        'category' => 'Construction & Real Estate',
        'title' => ['Are your projects, site teams and materials', 'difficult to manage?'],
        'text' => 'Connect project management, site operations, procurement, materials, contractors and financial workflows through integrated construction business systems.',
        'chips' => ['Construction ERP', 'Project Management', 'Procurement Management', 'Site Operations', 'Business Automation'],
        'cta' => 'Stop Profit Leakage in Your Projects',
        'mock' => 'sites',
        'mockLabel' => 'Sample dashboard: progress, crew and materials across four construction sites',
    ],
    [
        'accent' => 'rose',
        'category' => 'Healthcare',
        'title' => ['Are disconnected systems making patient and clinic', 'management harder?'],
        'text' => 'Simplify clinic and hospital operations with digital systems that connect patients, appointments, branches, teams and business workflows.',
        'chips' => ['Healthcare Management Systems', 'Clinic Management', 'Patient Workflows', 'Appointment Management', 'Automation'],
        'cta' => 'Stop Losing Revenue Through Operational Gaps',
        'mock' => 'appointments',
        'mockLabel' => 'Sample dashboard: appointments for the day across clinic branches',
    ],
    [
        'accent' => 'green',
        'category' => 'Ecommerce & D2C',
        'title' => ['Are you struggling to turn website visitors', 'into paying customers?'],
        'text' => 'Build high-performing ecommerce platforms that connect storefronts, products, orders, inventory, customer experiences and marketing.',
        'chips' => ['Shopify Development', 'Custom Ecommerce', 'Conversion Optimization', 'Ecommerce Integrations', 'Performance Marketing'],
        'cta' => 'Turn More Visitors into Paying Customers',
        'mock' => 'funnel',
        'mockLabel' => 'Sample dashboard: conversion funnel from store visit to purchase',
    ],
    [
        'accent' => 'amber',
        'category' => 'Jewellery & Retail',
        'title' => ['Are inventory, customer data and sales spread across', 'different systems?'],
        'text' => 'Improve retail operations through connected digital commerce, inventory visibility, customer management and business automation.',
        'chips' => ['Retail Management', 'Inventory Systems', 'Shopify', 'Customer Management', 'Automation'],
        'cta' => 'Stop Losing Sales Through Inventory Gaps',
        'mock' => 'stock',
        'mockLabel' => 'Sample dashboard: showroom and online stock beside a customer profile',
    ],
    [
        'accent' => 'cyan',
        'category' => 'Manufacturing & Distribution',
        'title' => ['Are production, inventory and supply chain operations', 'difficult to coordinate?'],
        'text' => 'Improve operational visibility by connecting inventory, purchasing, production workflows, vendors and business processes.',
        'chips' => ['Manufacturing ERP', 'Inventory Management', 'Procurement', 'Vendor Management', 'Operations Automation'],
        'cta' => 'Reduce Production Waste. Protect Your Profits',
        'mock' => 'plant',
        'mockLabel' => 'Sample dashboard: weekly production output and orders at each stage',
    ],
];
?>
<section class="sw sw--ind" id="industries">
  <div class="wrap">
    <header class="sw-head">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Same Industry ≠ Same Business Challenges</span>
      <h2 class="sw-h2 reveal" style="--d:.06s">We Don't Build for Industries.<br>We Build for Businesses.</h2>
      <div class="sw-lead reveal" style="--d:.12s">
        <p class="sw-sub">Every business operates differently. Even two companies in the same industry can have completely different business models, workflows, team structures and operational challenges.</p>
        <p class="sw-sub">A construction company managing multiple sites may need an entirely different system from another construction company focused on property sales and customer management.</p>
        <p class="sw-sub sw-sub--strong">That's why we don't believe in one-size-fits-all software.</p>
        <p class="sw-sub">We understand your industry, study how your business operates and engineer technology around your specific processes, people and business requirements.</p>
      </div>
    </header>
    <div class="sw-rows">
<?php foreach ($indRows as $i => $p): $n = sprintf('%02d', $i + 1); ?>
      <article class="sw-row<?= $i % 2 === 0 ? ' sw-row--media-first' : '' ?>" data-accent="<?= h($p['accent']) ?>">
        <div class="sw-text reveal">
          <span class="sw-cat"><b><?= $n ?></b><?= h($p['category']) ?></span>
          <h3 class="sw-h3"><?= h($p['title'][0]) ?> <span class="sw-u"><?= h($p['title'][1]) ?></span></h3>
          <p class="sw-p"><?= h($p['text']) ?></p>
          <b class="sw-chips__l">Solutions:</b>
          <ul class="sw-chips" role="list"><?php foreach ($p['chips'] as $c): ?><li><?= h($c) ?></li><?php endforeach; ?></ul>
          <a class="sw-cta" href="/book" data-book><span><?= h($p['cta']) ?></span><span class="sw-cta__ic"><?= $swIc('i-arrow') ?></span></a>
        </div>
        <div class="sw-media reveal" style="--d:.08s" role="img" aria-label="<?= h($p['mockLabel']) ?>">
<?php if ($p['mock'] === 'sites'): ?>
          <div class="sw-card" aria-hidden="true">
            <div class="sw-card__hd"><div><b>Site operations</b><small>4 active sites · today</small></div><span class="sw-tabs"><i class="is-on">Sites</i><i>Materials</i><i>Crew</i></span></div>
            <div class="sw-kpis sw-kpis--3"><div class="sw-kpi"><small>Active sites</small><b>4</b></div><div class="sw-kpi"><small>Crew on site</small><b>86</b></div><div class="sw-kpi"><small>Deliveries</small><b class="sw-acc">5</b></div></div>
            <ul class="sw-list">
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-building') ?></span><div><b>Residential Site A</b><small>Slab work · 28 on site</small></div><span class="sw-bar"><i style="--w:.72"></i></span><em>72%</em></li>
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-building') ?></span><div><b>Villa Site B</b><small>Finishing · 22 on site</small></div><span class="sw-bar"><i style="--w:.88"></i></span><em>88%</em></li>
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-building') ?></span><div><b>Commercial Site C</b><small>Foundation · 24 on site</small></div><span class="sw-bar"><i style="--w:.35"></i></span><em>35%</em></li>
              <li><span class="sw-tile sw-tile--sm"><?= $swIc('i-building') ?></span><div><b>Interior Site D</b><small>Fit-out · 12 on site</small></div><span class="sw-bar"><i style="--w:.54"></i></span><em>54%</em></li>
            </ul>
            <div class="sw-alert"><span><b>Cement running low</b> at Site C</span><span class="sw-alert__btn">Reorder</span></div>
          </div>
          <div class="sw-evt" aria-hidden="true"><span class="sw-tile"><?= $swIc('i-check') ?></span><div><b>Material received</b><small>Villa Site B · 200 bags</small></div></div>
<?php elseif ($p['mock'] === 'appointments'): ?>
          <div class="sw-card" aria-hidden="true">
            <div class="sw-card__hd"><div><b>Appointments</b><small>All branches · today</small></div><span class="sw-tabs"><i class="is-on">Day</i><i>Week</i></span></div>
            <div class="sw-days"><i><small>Mon</small>12</i><i><small>Tue</small>13</i><i class="is-on"><small>Wed</small>14</i><i><small>Thu</small>15</i><i><small>Fri</small>16</i><i><small>Sat</small>17</i></div>
            <div class="sw-kpis sw-kpis--3"><div class="sw-kpi"><small>Booked</small><b>64</b></div><div class="sw-kpi"><small>Checked in</small><b class="sw-acc">41</b></div><div class="sw-kpi"><small>Waiting</small><b>9</b></div></div>
            <ul class="sw-list">
              <li><span class="sw-time">09:30</span><div><b>General consultation</b><small>Branch 01 · Room 2</small></div><span class="sw-st sw-st--ok">Checked in</span></li>
              <li><span class="sw-time">10:15</span><div><b>Follow-up visit</b><small>Branch 03 · Room 1</small></div><span class="sw-st sw-st--wait">Waiting</span></li>
              <li><span class="sw-time">11:00</span><div><b>Physiotherapy session</b><small>Branch 02 · Room 4</small></div><span class="sw-st">Confirmed</span></li>
            </ul>
          </div>
          <div class="sw-evt" aria-hidden="true"><span class="sw-tile"><?= $swIc('i-calendar') ?></span><div><b>Reminder sent</b><small>Branch 02 · 5:30 PM visit</small></div></div>
<?php elseif ($p['mock'] === 'funnel'): ?>
          <div class="sw-card" aria-hidden="true">
            <div class="sw-card__hd"><div><b>Conversion funnel</b><small>Last 30 days</small></div><span class="sw-tabs"><i class="is-on">Funnel</i><i>Orders</i></span></div>
            <div class="sw-kpis sw-kpis--2"><div class="sw-kpi"><small>Orders</small><b>967 <span class="sw-up">+18%</span></b></div><div class="sw-kpi"><small>Conversion</small><b class="sw-acc">3.9%</b></div></div>
            <ul class="sw-fun">
              <li><span>Visited store</span><span class="sw-fun__t"><i style="--w:1"></i></span><em>24,800</em></li>
              <li><span>Viewed product</span><span class="sw-fun__t"><i style="--w:.55"></i></span><em>13,640</em></li>
              <li><span>Added to cart</span><span class="sw-fun__t"><i style="--w:.17"></i></span><em>4,216</em></li>
              <li><span>Checkout</span><span class="sw-fun__t"><i style="--w:.09"></i></span><em>2,232</em></li>
              <li class="is-end"><span>Purchased</span><span class="sw-fun__t"><i style="--w:.039"></i></span><em>967</em></li>
            </ul>
            <div class="sw-sync"><b>In sync</b><span><?= $swIc('i-check') ?>Storefront</span><span><?= $swIc('i-check') ?>Inventory</span><span><?= $swIc('i-check') ?>Marketing</span></div>
          </div>
          <div class="sw-evt" aria-hidden="true"><span class="sw-tile"><?= $swIc('i-cart') ?></span><div><b>Cart recovered</b><small>Order 1048 · ₹2,450</small></div></div>
<?php elseif ($p['mock'] === 'stock'): ?>
          <div class="sw-card" aria-hidden="true">
            <div class="sw-card__hd"><div><b>Stock &amp; sales</b><small>Showroom and online</small></div><span class="sw-tabs"><i class="is-on">Stock</i><i>Customers</i></span></div>
            <div class="sw-kpis sw-kpis--2"><div class="sw-kpi"><small>Pieces in stock</small><b>1,284</b></div><div class="sw-kpi"><small>Sold this week</small><b class="sw-acc">96</b></div></div>
            <div class="sw-list__hd"><span class="sw-key"><i style="--c:var(--accent-700)">Showroom</i><i style="--c:var(--accent-500)">Online</i></span><span>Pieces</span></div>
            <ul class="sw-list">
              <li><div><b>Rings</b></div><span class="sw-mix"><i style="--w:.65"></i><i style="--w:.35"></i></span><em>412</em></li>
              <li><div><b>Bangles</b></div><span class="sw-mix"><i style="--w:.58"></i><i style="--w:.24"></i></span><em>338</em></li>
              <li><div><b>Necklaces</b></div><span class="sw-mix"><i style="--w:.46"></i><i style="--w:.26"></i></span><em>296</em></li>
              <li><div><b>Earrings</b></div><span class="sw-mix"><i style="--w:.29"></i><i style="--w:.29"></i></span><em>238</em></li>
            </ul>
            <div class="sw-cust"><span class="sw-tile sw-tile--sm"><?= $swIc('i-users') ?></span><div><b>Returning customer</b><small>4 purchases · anniversary in 9 days</small></div><span class="sw-alert__btn">Remind</span></div>
          </div>
          <div class="sw-evt" aria-hidden="true"><span class="sw-tile"><?= $swIc('i-gem') ?></span><div><b>Low stock alert</b><small>Gold bangles · 3 left</small></div></div>
<?php else: ?>
          <div class="sw-card" aria-hidden="true">
            <div class="sw-card__hd"><div><b>Production overview</b><small>Plant 1 · this week</small></div><span class="sw-tabs"><i class="is-on">Output</i><i>Stock</i></span></div>
            <div class="sw-kpis sw-kpis--3"><div class="sw-kpi"><small>Units made</small><b>6,970</b></div><div class="sw-kpi"><small>On time</small><b class="sw-acc">94%</b></div><div class="sw-kpi"><small>Vendors</small><b>14</b></div></div>
            <div class="sw-list__hd"><span>Units per day</span></div>
            <div class="sw-cols"><span style="--h:.67"><i></i><small>Mon</small></span><span style="--h:.85"><i></i><small>Tue</small></span><span style="--h:.59"><i></i><small>Wed</small></span><span style="--h:.9"><i></i><small>Thu</small></span><span style="--h:.77"><i></i><small>Fri</small></span><span class="is-top" style="--h:1"><b>1,460</b><i></i><small>Sat</small></span></div>
            <div class="sw-list__hd"><span>Open orders by stage</span></div>
            <ol class="sw-pipe"><li><b>12</b><small>Purchase</small></li><li><b>18</b><small>Production</small></li><li><b>5</b><small>Quality</small></li><li><b>9</b><small>Dispatch</small></li></ol>
          </div>
          <div class="sw-evt" aria-hidden="true"><span class="sw-tile"><?= $swIc('i-factory') ?></span><div><b>Raw material received</b><small>Vendor 04 · 2.4 tonnes</small></div></div>
<?php endif; ?>
        </div>
      </article>
<?php endforeach; ?>
    </div>
    <p class="sw-foot">Product screens use sample data.</p>
  </div>
</section>
<?php
// Client testimonials. Client names stay out where an NDA applies, so each card
// is credited by role and industry. The phrase in [[...]] gets the marker highlight.
$tsItems = [
  ['Construction & Real Estate', 'i-hardhat', '#F8E6D6', 'Founder & CEO',
    'We needed more than just software. We needed a system that could connect our projects, teams and day-to-day operations. Drawlead understood our requirements and [[built a solution around how our business actually works.]]'],
  ['Ecommerce & D2C', 'i-bag', '#DFEAF9', 'Founder',
    'We wanted an ecommerce experience that truly represented our brand, not just another standard online store. Drawlead understood our vision and [[developed a customized shopping experience around our products and customers.]]'],
  ['Healthcare', 'i-stetho', '#DAF1E5', 'Founder',
    'Managing multiple operations while maintaining a smooth patient experience was challenging. Drawlead helped us bring our processes together through [[technology designed around our healthcare operations.]]'],
];
$tsMark = fn(string $q) => preg_replace('/\[\[(.+?)\]\]/', '<mark>$1</mark>', h($q));
?>
<section class="test" id="testimonials">
  <div class="wrap">
    <div class="ts-head">
      <div>
        <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Client Experiences</span>
        <h2 class="ts-h2 reveal" style="--d:.06s">Built on Trust.<br>Strengthened by Results.</h2>
      </div>
      <div class="ts-lede reveal" style="--d:.12s">
        <p>Great technology is built through great partnerships. We work closely with our clients to understand their business, solve complex challenges and deliver solutions that create lasting value.</p>
        <p><b>Here's what our clients have to say about working with Drawlead.</b></p>
      </div>
    </div>
    <div class="ts-grid">
<?php foreach ($tsItems as $i => [$ind, $icon, $tint, $role, $quote]): ?>
      <figure class="ts-card<?= $i === 0 ? ' ts-card--lead' : '' ?> reveal" style="--t:<?= $tint ?>;--d:<?= $i * .08 ?>s">
        <div class="ts-top"><span class="ts-no">Testimonial <?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span><span class="ts-ind"><?= h($ind) ?></span></div>
        <svg class="ts-q" viewBox="0 0 48 36" aria-hidden="true" focusable="false"><path d="M0 36V22C0 9.5 6.4 2.2 19 0l1.8 5.2C14 7 10.6 10.8 10 16h9v20H0zm27 0V22C27 9.5 33.4 2.2 46 0l1.8 5.2C41 7 37.6 10.8 37 16h9v20H27z"/></svg>
        <blockquote><p><?= $tsMark($quote) ?></p></blockquote>
        <figcaption class="ts-by"><span class="ts-av" aria-hidden="true"><svg class="ic" aria-hidden="true" focusable="false"><use href="#<?= $icon ?>"/></svg></span><span><b><?= h($role) ?></b><span><?= h($ind) ?></span></span></figcaption>
      </figure>
<?php endforeach; ?>
    </div>
    <div class="bcta bcta--light reveal">
      <div><h3>Your Business Could Be Our Next Success Story.</h3><p>Let's build something meaningful together.</p></div>
      <a class="h10-btn h10-btn--dark" href="/book" data-book><span>Start a Conversation</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
    </div>
  </div>
</section>
<section class="final final--card" id="contact">
  <div class="wrap">
    <div class="final__card">
      <div class="final__in">
        <span class="pill pill--mint reveal"><span class="pill__dot" aria-hidden="true"></span>Let's Build Something Better</span>
        <h2 class="h1x reveal" style="--d:.06s">Your Next Business Transformation <span class="grn">Starts Here.</span></h2>
        <p class="lede lede--light reveal" style="--d:.1s">Whether you're looking to build a custom business system, develop a high-performing ecommerce platform, implement AI-powered automation or accelerate digital growth, we're ready to understand your requirements and engineer the right solution.</p>
        <p class="final__line reveal" style="--d:.14s">Let's turn your business challenges into technology opportunities.</p>
        <div class="final__ctas reveal" style="--d:.18s"><a class="h10-btn h10-btn--green" href="/book" data-book><span>Book a Consultation</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a><a class="h10-btn h10-btn--ghost" href="#solutions"><span>Explore Our Services</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a></div>
      </div>
    </div>
  </div>
</section>
</main><footer class="foot">
  <?php $signClass = 'wrap'; include __DIR__ . '/partials/footer-signoff.php'; ?>
  <div class="wrap foot__in">
    <a href="#top" aria-label="Drawlead – back to top"><img src="/assets/home10/logos/drawlead-white.webp" alt="Drawlead" width="153" height="30" loading="lazy"></a>
    <p>Business Systems. Digital Commerce. Intelligent Growth.</p>
    <?php if ($footLinks = footer_menu_links($pdo, 'links')): ?><nav aria-label="Footer"><?= $footLinks ?></nav><?php endif; ?>
  </div>
  <div class="wrap foot__base">
    <span>official@drawlead.co.in &nbsp;·&nbsp; www.drawlead.co.in &nbsp;·&nbsp; Chennai, Tamil Nadu, IN</span>
    <span>© <?= date('Y') ?> Drawlead. All rights reserved.</span>
    <?php if ($footLegal = footer_menu_links($pdo, 'legal')): ?><nav class="foot__legal" aria-label="Legal"><?= $footLegal ?></nav><?php endif; ?>
  </div>
</footer>
</div>

<script src="<?= asset_url('/assets/home10.js') ?>" defer></script>
