<?php
/**
 * Home 10: the homepage from the drawlead-website design, served as a
 * DB-managed page (Admin -> Pages; migration 032 adds it and puts it at /).
 *
 * Ported from drawlead-website/index.html. This file is now the source for
 * the live page: edit it here, or in Admin -> Pages -> Edit -> Page Content.
 *
 * Styling is /assets/home10.css, with every rule scoped under .h10 so neither
 * this design nor the site-wide stylesheet (partials/style.php) leaks into the
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
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs><symbol id="i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></symbol><symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol><symbol id="i-sparkle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z" fill="currentColor" stroke="none"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z" fill="currentColor" stroke="none"/></symbol><symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2.2 2.2L15 10"/></symbol><symbol id="i-layers" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 12.5l9 5 9-5"/><path d="M3 17l9 5 9-5" opacity=".55"/></symbol><symbol id="i-briefcase" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M9 7V5.5A1.5 1.5 0 0110.5 4h3A1.5 1.5 0 0115 5.5V7M3 13h18"/></symbol><symbol id="i-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 10h18M8 3v4M16 3v4M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></symbol><symbol id="i-building" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="10" height="18" rx="1.5"/><path d="M15 9h4v12h-4M8 7h4M8 11h4M8 15h4"/></symbol><symbol id="i-cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2.2l2.4 11h10.4L20 8H6.3"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></symbol><symbol id="i-bot" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="11" rx="3.5"/><path d="M12 8V5M9.5 13v1.6M14.5 13v1.6M2 13v3M22 13v3"/><circle cx="12" cy="4" r="1"/></symbol><symbol id="i-megaphone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10v4l12 5V5z"/><path d="M16 9.5a3 3 0 010 5M7 14.5V19"/></symbol><symbol id="i-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M17 14c2.8 0 5 2.2 5 5"/></symbol><symbol id="i-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2.5"/><path d="M8 11V8a4 4 0 018 0v3"/></symbol><symbol id="i-db" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.7 3.1 3 7 3s7-1.3 7-3V6M5 12v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/></symbol><symbol id="i-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5A1.5 1.5 0 015.5 4h13A1.5 1.5 0 0120 5.5v9a1.5 1.5 0 01-1.5 1.5H10l-4.5 4v-4h0A1.5 1.5 0 014 14.5z"/><path d="M8 9h8M8 12h5"/></symbol><symbol id="i-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V4M4 20h16"/><path d="M8 16v-4M12 16V8M16 16v-6"/></symbol><symbol id="i-workflow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="6.5" height="6.5" rx="1.8"/><rect x="14.5" y="13.5" width="6.5" height="6.5" rx="1.8"/><path d="M9.5 7.2H12a3 3 0 013 3v3.3"/></symbol><symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></symbol><symbol id="i-code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 8l-4 4 4 4M16 8l4 4-4 4M13.5 5l-3 14"/></symbol><symbol id="i-rocket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c3 2 5 5 5 9l-2.5 3h-5L7 12c0-4 2-7 5-9z"/><circle cx="12" cy="10" r="1.6"/><path d="M9.5 15L7 19M14.5 15L17 19M12 15v5"/></symbol><symbol id="i-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></symbol><symbol id="i-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M4 12h16M4 17h16"/></symbol><symbol id="i-hierarchy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="3" width="6" height="5" rx="1.2"/><rect x="3" y="16" width="6" height="5" rx="1.2"/><rect x="15" y="16" width="6" height="5" rx="1.2"/><path d="M12 8v4M6 16v-4h12v4"/></symbol><symbol id="i-heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-8-4.6-8-10.2A4.3 4.3 0 0112 7.2a4.3 4.3 0 018 2.6C20 15.4 12 20 12 20z"/><path d="M7 12.2h2.6l1.4-2.4 2 4.4 1.4-2H17"/></symbol><symbol id="i-gem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h12l4 5-10 11L2 9z"/><path d="M2 9h20M9 4L7 9l5 11M15 4l2 5-5 11"/></symbol><symbol id="i-factory" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V10l6 3.5V10l6 3.5V5h4v16z"/><path d="M8 17h2M13 17h2"/></symbol><symbol id="i-plug" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3v5M15 3v5M6 8h12v3a6 6 0 01-12 0zM12 17v4"/></symbol><symbol id="i-gear" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3.2"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/></symbol><symbol id="i-target" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r=".6"/></symbol><symbol id="i-quote" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h4v4.2c0 3-1.5 5-4 6M15 7h4v4.2c0 3-1.5 5-4 6"/></symbol><symbol id="i-pause" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5v14M15 5v14"/></symbol><symbol id="i-play" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 5l11 7-11 7z" fill="currentColor"/></symbol><symbol id="i-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></symbol><symbol id="i-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></symbol><symbol id="i-up" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V6M6 12l6-6 6 6"/></symbol><symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3.5 7l8.5 6 8.5-6"/></symbol></defs></svg>
<a class="skip" href="#top">Skip to content</a>
<div class="progress" aria-hidden="true"><i></i></div><main>
<section class="hero" id="top">
  <div class="hero__bg" aria-hidden="true"><i class="blob b1"></i><i class="blob b2"></i><i class="blob b3"></i><i class="grid"></i></div>
  <div class="wrap">
    <div class="hero__top">
      <div class="hero__copy">
        <span class="pill pill--outline reveal"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-sparkle"/></svg>Turning Business Data into Intelligent AI &amp; Automation Solutions</span>
        <h1 class="hero__title">
          <span class="strike reveal" style="--d:.08s"><s>Stop Managing Business Complexity.</s></span>
          <span class="hero__start reveal" style="--d:.2s">Start Building <em>Smarter Business Solutions.</em></span>
        </h1>
      </div>
      <div class="hero__art il reveal" style="--d:.25s" aria-hidden="true"><div class="hero__art-d"><svg class="il-svg" viewBox="0 0 560 520" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><radialGradient id="hn-glow"><stop offset="0" stop-color="#32B46F" stop-opacity=".22"/><stop offset="1" stop-color="#32B46F" stop-opacity="0"/></radialGradient></defs><circle cx="280" cy="260" r="250" fill="url(#hn-glow)"/><circle class="ring spin" cx="280" cy="260" r="150" fill="none"/><circle class="ring" cx="280" cy="260" r="205" fill="none" style="opacity:.5"/><path class="ln flow" d="M363.4 86.3 L280 260" style="--d:0.00s"/><path class="ln flow" d="M481.4 188.1 L280 260" style="--d:0.18s"/><path class="ln flow" d="M481.4 331.9 L280 260" style="--d:0.36s"/><path class="ln flow" d="M363.4 433.7 L280 260" style="--d:0.54s"/><path class="ln flow" d="M196.6 433.7 L280 260" style="--d:0.72s"/><path class="ln flow" d="M78.6 331.9 L280 260" style="--d:0.90s"/><path class="ln flow" d="M78.6 188.1 L280 260" style="--d:1.08s"/><path class="ln flow" d="M196.6 86.3 L280 260" style="--d:1.26s"/><circle r="3.2" class="g"><animateMotion dur="3.20s" begin="-0.0s" repeatCount="indefinite" path="M363.4 86.3 L280 260"/></circle><circle r="3.2" class="g"><animateMotion dur="3.35s" begin="-0.4s" repeatCount="indefinite" path="M481.4 188.1 L280 260"/></circle><circle r="3.2" class="g"><animateMotion dur="3.50s" begin="-0.8s" repeatCount="indefinite" path="M481.4 331.9 L280 260"/></circle><circle r="3.2" class="g"><animateMotion dur="3.65s" begin="-1.2s" repeatCount="indefinite" path="M363.4 433.7 L280 260"/></circle><circle r="3.2" class="g"><animateMotion dur="3.80s" begin="-1.6s" repeatCount="indefinite" path="M196.6 433.7 L280 260"/></circle><circle r="3.2" class="g"><animateMotion dur="3.95s" begin="-2.0s" repeatCount="indefinite" path="M78.6 331.9 L280 260"/></circle><circle r="3.2" class="g"><animateMotion dur="4.10s" begin="-2.4s" repeatCount="indefinite" path="M78.6 188.1 L280 260"/></circle><circle r="3.2" class="g"><animateMotion dur="4.25s" begin="-2.8s" repeatCount="indefinite" path="M196.6 86.3 L280 260"/></circle><g transform="translate(224 204)"><rect class="pulse" x="0" y="0" width="112" height="112" rx="32" fill="none" stroke="var(--green)" stroke-width="2" style="--d:0s"/><rect class="pulse" x="0" y="0" width="112" height="112" rx="32" fill="none" stroke="var(--green)" stroke-width="2" style="--d:1.2s"/><rect class="c coreshadow" x="0" y="0" width="112" height="112" rx="32"/><image href="/assets/home10/logos/drawlead-mark.webp" x="22" y="22" width="68" height="68" preserveAspectRatio="xMidYMid meet"/></g><g class="float node" style="--d:0.0s"><g transform="translate(307.4 64.3)"><rect class="c" x="0" y="0" width="112" height="44" rx="14" /><circle class="gs" cx="24" cy="22" r="14" /><svg x="15" y="13" width="18" height="18" style="color:var(--green)" ><use href="#i-layers"/></svg><text class="lbl" x="46" y="26" text-anchor="start" >ERP</text></g></g><g class="float node" style="--d:0.5s"><g transform="translate(425.4 166.1)"><rect class="c" x="0" y="0" width="112" height="44" rx="14" /><circle class="gs" cx="24" cy="22" r="14" /><svg x="15" y="13" width="18" height="18" style="color:var(--green)" ><use href="#i-users"/></svg><text class="lbl" x="46" y="26" text-anchor="start" >CRM</text></g></g><g class="float node" style="--d:1.0s"><g transform="translate(425.4 309.9)"><rect class="c" x="0" y="0" width="112" height="44" rx="14" /><circle class="gs" cx="24" cy="22" r="14" /><svg x="15" y="13" width="18" height="18" style="color:var(--green)" ><use href="#i-bot"/></svg><text class="lbl" x="46" y="26" text-anchor="start" >AI Agents</text></g></g><g class="float node" style="--d:1.5s"><g transform="translate(297.4 411.7)"><rect class="c" x="0" y="0" width="132" height="44" rx="14" /><circle class="gs" cx="24" cy="22" r="14" /><svg x="15" y="13" width="18" height="18" style="color:var(--green)" ><use href="#i-workflow"/></svg><text class="lbl" x="46" y="26" text-anchor="start" >Automation</text></g></g><g class="float node" style="--d:2.0s"><g transform="translate(130.6 411.7)"><rect class="c" x="0" y="0" width="132" height="44" rx="14" /><circle class="gs" cx="24" cy="22" r="14" /><svg x="15" y="13" width="18" height="18" style="color:var(--green)" ><use href="#i-chat"/></svg><text class="lbl" x="46" y="26" text-anchor="start" >WhatsApp API</text></g></g><g class="float node" style="--d:2.5s"><g transform="translate(22.6 309.9)"><rect class="c" x="0" y="0" width="112" height="44" rx="14" /><circle class="gs" cx="24" cy="22" r="14" /><svg x="15" y="13" width="18" height="18" style="color:var(--green)" ><use href="#i-db"/></svg><text class="lbl" x="46" y="26" text-anchor="start" >Data</text></g></g><g class="float node" style="--d:3.0s"><g transform="translate(12.6 166.1)"><rect class="c" x="0" y="0" width="132" height="44" rx="14" /><circle class="gs" cx="24" cy="22" r="14" /><svg x="15" y="13" width="18" height="18" style="color:var(--green)" ><use href="#i-cart"/></svg><text class="lbl" x="46" y="26" text-anchor="start" >E-Commerce</text></g></g><g class="float node" style="--d:3.5s"><g transform="translate(140.6 64.3)"><rect class="c" x="0" y="0" width="112" height="44" rx="14" /><circle class="gs" cx="24" cy="22" r="14" /><svg x="15" y="13" width="18" height="18" style="color:var(--green)" ><use href="#i-shield"/></svg><text class="lbl" x="46" y="26" text-anchor="start" >Secure</text></g></g></svg></div><div class="hero__art-m"><svg class="il-svg" viewBox="0 0 400 400" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><radialGradient id="hm-glow"><stop offset="0" stop-color="#32B46F" stop-opacity=".22"/><stop offset="1" stop-color="#32B46F" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="190" fill="url(#hm-glow)"/><circle class="ring spin" cx="200" cy="200" r="120" fill="none"/><path class="ln flow" d="M200.0 50.0 L200 200" style="--d:0.00s"/><path class="ln flow" d="M306.1 93.9 L200 200" style="--d:0.18s"/><path class="ln flow" d="M350.0 200.0 L200 200" style="--d:0.36s"/><path class="ln flow" d="M306.1 306.1 L200 200" style="--d:0.54s"/><path class="ln flow" d="M200.0 350.0 L200 200" style="--d:0.72s"/><path class="ln flow" d="M93.9 306.1 L200 200" style="--d:0.90s"/><path class="ln flow" d="M50.0 200.0 L200 200" style="--d:1.08s"/><path class="ln flow" d="M93.9 93.9 L200 200" style="--d:1.26s"/><circle r="3" class="g"><animateMotion dur="3.00s" begin="-0.0s" repeatCount="indefinite" path="M200.0 50.0 L200 200"/></circle><circle r="3" class="g"><animateMotion dur="3.15s" begin="-0.4s" repeatCount="indefinite" path="M306.1 93.9 L200 200"/></circle><circle r="3" class="g"><animateMotion dur="3.30s" begin="-0.8s" repeatCount="indefinite" path="M350.0 200.0 L200 200"/></circle><circle r="3" class="g"><animateMotion dur="3.45s" begin="-1.2s" repeatCount="indefinite" path="M306.1 306.1 L200 200"/></circle><circle r="3" class="g"><animateMotion dur="3.60s" begin="-1.6s" repeatCount="indefinite" path="M200.0 350.0 L200 200"/></circle><circle r="3" class="g"><animateMotion dur="3.75s" begin="-2.0s" repeatCount="indefinite" path="M93.9 306.1 L200 200"/></circle><circle r="3" class="g"><animateMotion dur="3.90s" begin="-2.4s" repeatCount="indefinite" path="M50.0 200.0 L200 200"/></circle><circle r="3" class="g"><animateMotion dur="4.05s" begin="-2.8s" repeatCount="indefinite" path="M93.9 93.9 L200 200"/></circle><g transform="translate(154 154)"><rect class="pulse" width="92" height="92" rx="26" fill="none" stroke="var(--green)" stroke-width="2"/><rect class="c coreshadow" width="92" height="92" rx="26"/><image href="/assets/home10/logos/drawlead-mark.webp" x="18" y="18" width="56" height="56"/></g><g class="float" style="--d:0.0s"><g transform="translate(173.0 23.0)"><rect class="c" x="0" y="0" width="54" height="54" rx="18" /><svg x="13" y="13" width="28" height="28" style="color:var(--green)" ><use href="#i-layers"/></svg></g></g><g class="float" style="--d:0.5s"><g transform="translate(279.1 66.9)"><rect class="c" x="0" y="0" width="54" height="54" rx="18" /><svg x="13" y="13" width="28" height="28" style="color:var(--green)" ><use href="#i-users"/></svg></g></g><g class="float" style="--d:1.0s"><g transform="translate(323.0 173.0)"><rect class="c" x="0" y="0" width="54" height="54" rx="18" /><svg x="13" y="13" width="28" height="28" style="color:var(--green)" ><use href="#i-bot"/></svg></g></g><g class="float" style="--d:1.5s"><g transform="translate(279.1 279.1)"><rect class="c" x="0" y="0" width="54" height="54" rx="18" /><svg x="13" y="13" width="28" height="28" style="color:var(--green)" ><use href="#i-workflow"/></svg></g></g><g class="float" style="--d:2.0s"><g transform="translate(173.0 323.0)"><rect class="c" x="0" y="0" width="54" height="54" rx="18" /><svg x="13" y="13" width="28" height="28" style="color:var(--green)" ><use href="#i-chat"/></svg></g></g><g class="float" style="--d:2.5s"><g transform="translate(66.9 279.1)"><rect class="c" x="0" y="0" width="54" height="54" rx="18" /><svg x="13" y="13" width="28" height="28" style="color:var(--green)" ><use href="#i-db"/></svg></g></g><g class="float" style="--d:3.0s"><g transform="translate(23.0 173.0)"><rect class="c" x="0" y="0" width="54" height="54" rx="18" /><svg x="13" y="13" width="28" height="28" style="color:var(--green)" ><use href="#i-cart"/></svg></g></g><g class="float" style="--d:3.5s"><g transform="translate(66.9 66.9)"><rect class="c" x="0" y="0" width="54" height="54" rx="18" /><svg x="13" y="13" width="28" height="28" style="color:var(--green)" ><use href="#i-shield"/></svg></g></g></svg></div></div>
    </div>
    <div class="hero__mid">
      <div class="ctacard reveal" style="--d:.1s">
        <p>Looking for a structured business transformation?</p>
        <a class="h10-btn h10-btn--green" href="#program"><span>Explore Our 8-Week Program</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
      </div>
      <p class="hero__lede reveal" style="--d:.18s">We work closely with your teams to understand your business, identify challenges and build technology around your needs. From custom business systems and software to AI, automation and digital commerce, we create connected solutions that simplify operations and drive growth.</p>
    </div>
    <ul class="pillars" role="list"><li class="reveal" style="--d:0.0s"><span class="pillar__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-layers"/></svg></span><h3>No Generic Software</h3><p>Solutions engineered around your business.</p></li><li class="reveal" style="--d:0.05s"><span class="pillar__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-target"/></svg></span><h3>No One-Size-Fits-All</h3><p>Every business gets a tailored approach.</p></li><li class="reveal" style="--d:0.1s"><span class="pillar__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-hierarchy"/></svg></span><h3>Hierarchy-Based Systems</h3><p>Structured roles, permissions and workflows.</p></li><li class="reveal" style="--d:0.15000000000000002s"><span class="pillar__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-shield"/></svg></span><h3>Security-First Engineering</h3><p>Security considered throughout implementation.</p></li></ul>
  </div>
  <div class="strip" aria-label="Services">
    <div class="marquee marquee--strip"><ul class="marquee__track" role="list"><li>ERP Development</li><li>CRM Solutions</li><li>AI Agents</li><li>Workflow Automation</li><li>AI Integration</li><li>WhatsApp API Integration</li><li>Data Integration</li><li>E-Commerce for Conversions</li><li>Secure Engineering</li><li aria-hidden="true">ERP Development</li><li aria-hidden="true">CRM Solutions</li><li aria-hidden="true">AI Agents</li><li aria-hidden="true">Workflow Automation</li><li aria-hidden="true">AI Integration</li><li aria-hidden="true">WhatsApp API Integration</li><li aria-hidden="true">Data Integration</li><li aria-hidden="true">E-Commerce for Conversions</li><li aria-hidden="true">Secure Engineering</li><li aria-hidden="true">ERP Development</li><li aria-hidden="true">CRM Solutions</li><li aria-hidden="true">AI Agents</li><li aria-hidden="true">Workflow Automation</li><li aria-hidden="true">AI Integration</li><li aria-hidden="true">WhatsApp API Integration</li><li aria-hidden="true">Data Integration</li><li aria-hidden="true">E-Commerce for Conversions</li><li aria-hidden="true">Secure Engineering</li><li aria-hidden="true">ERP Development</li><li aria-hidden="true">CRM Solutions</li><li aria-hidden="true">AI Agents</li><li aria-hidden="true">Workflow Automation</li><li aria-hidden="true">AI Integration</li><li aria-hidden="true">WhatsApp API Integration</li><li aria-hidden="true">Data Integration</li><li aria-hidden="true">E-Commerce for Conversions</li><li aria-hidden="true">Secure Engineering</li></ul></div>
  </div>
</section>
<section class="cred" id="credibility">
  <div class="wrap">
    <div class="cred__head">
      <div>
        <span class="pill reveal"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-shield"/></svg>Trusted Technology Partner</span>
        <h2 class="h2 reveal" style="--d:.06s">Engineering Partnerships.<br><span class="hl">Building Business Solutions.</span></h2>
      </div>
      <ul class="stats" role="list"><li class="stat reveal" style="--d:0.0s"><span class="stat__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-layers"/></svg></span><div><b class="stat__n"><span data-count="20">20</span>+</b><span class="stat__l">Industries</span></div></li><li class="stat reveal" style="--d:0.08s"><span class="stat__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-briefcase"/></svg></span><div><b class="stat__n"><span data-count="50">50</span>+</b><span class="stat__l">Projects Delivered</span></div></li><li class="stat reveal" style="--d:0.16s"><span class="stat__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-calendar"/></svg></span><div><b class="stat__n"><span data-count="8">8</span> Weeks</b><span class="stat__l">Implementation Program</span></div></li></ul>
    </div>
  </div>
  <div class="logorows reveal" style="--d:.1s" aria-label="Client logos">
    <div class="marquee marquee--logos"><ul class="marquee__track" role="list"><li class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li><li class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li aria-hidden="true" class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li aria-hidden="true" class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li></ul></div>
    <div class="marquee marquee--logos marquee--rev"><ul class="marquee__track" role="list"><li class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li><li class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li aria-hidden="true" class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/palagarakoodai.webp" alt="" height="38" loading="lazy" style="height:38px;width:auto"><span>Palagarakoodai</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/vaahaa.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Vaahaa</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/goat-media.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Goat Media</span></li><li aria-hidden="true" class="h10-logo h10-logo--text"><span>Kenney Alpay</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/venkateshwara.webp" alt="" height="36" loading="lazy" style="height:36px;width:auto"><span>Venkateshwara Hospital</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/baskin-robbins.webp" alt="" height="20" loading="lazy" style="height:20px;width:auto"><span>Baskin Robbins</span></li><li aria-hidden="true" class="h10-logo"><img src="/assets/home10/logos/avm.webp" alt="" height="32" loading="lazy" style="height:32px;width:auto"><span>Dr. Velumani</span></li></ul></div>
  </div>
  <p class="cred__note reveal">Client identities remain confidential where protected by NDA</p>
</section>
<section class="caps" id="solutions">
  <div class="wrap"><div class="cx-shell">
    <span class="cx-pill reveal"><span class="cx-pill__dot" aria-hidden="true"></span>Our Core Capabilities</span>
    <h2 class="cx-h2 reveal" style="--d:.06s">Better Systems. Smoother<br>Operations. Stronger Business Growth.</h2>
    <p class="cx-sub reveal" style="--d:.12s">We empower business growth through intelligent solutions, automation, &amp; strategic marketing.</p>
    <div class="cx reveal" style="--d:.18s">
      <div class="cx-tabs" aria-label="Our core capabilities">
        <button type="button" class="cx-tab" id="cxt-1" aria-controls="cxp-1" aria-current="true" style="--ac:#DAF1E5;--pt:#DFEAF9"><span class="cx-tab__n">01</span><span class="cx-tab__t"><b>Business Systems</b><span>Custom software &amp; operating systems</span></span></button>
        <button type="button" class="cx-tab" id="cxt-2" aria-controls="cxp-2" aria-current="false" style="--ac:#E8E2F5;--pt:#DCEFF0"><span class="cx-tab__n">02</span><span class="cx-tab__t"><b>Digital Commerce</b><span>High-performing ecommerce platforms and connected stores</span></span></button>
        <button type="button" class="cx-tab" id="cxt-3" aria-controls="cxp-3" aria-current="false" style="--ac:#F8E6D6;--pt:#E3E5F6"><span class="cx-tab__n">03</span><span class="cx-tab__t"><b>AI &amp; Automation</b><span>Intelligent workflows that reduce manual work</span></span></button>
        <button type="button" class="cx-tab" id="cxt-4" aria-controls="cxp-4" aria-current="false" style="--ac:#F5E1E9;--pt:#EDF0D7"><span class="cx-tab__n">04</span><span class="cx-tab__t"><b>Marketing &amp; Growth</b><span>Data-driven digital marketing strategies</span></span></button>
      </div>
      <div class="cx-panels">
        <div class="cx-panel" role="region" id="cxp-1" aria-labelledby="cxt-1" style="--ac:#DAF1E5;--pt:#DFEAF9">
          <svg class="cx-ic" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><ellipse cx="32" cy="32" rx="28" ry="11"/><ellipse cx="32" cy="32" rx="28" ry="11" transform="rotate(60 32 32)"/><ellipse cx="32" cy="32" rx="28" ry="11" transform="rotate(120 32 32)"/></svg>
          <span class="cx-tag">Next-Gen Business OS</span>
          <h3 class="cx-h">Still Running Your Growing Business On <span class="cx-mk"><svg class="cx-hi" viewBox="0 0 20 24" aria-hidden="true" focusable="false"><path d="M3 1h10l6 6v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2z" fill="#23A566"/><path d="M13 1v6h6z" fill="#8ED1AE"/><path d="M5 11h10v8H5zM5 15h10M10 11v8" fill="none" stroke="#fff" stroke-width="1.4"/></svg>Spreadsheets,</span> <span class="cx-mk"><svg class="cx-hi" viewBox="-2 0 28 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="5.6" fill="#E5484D"/><path d="M12 9v3.4M12 15v.1" stroke="#fff" stroke-width="1.9" stroke-linecap="round"/><path d="M7 7q-3.6 5 0 10M17 7q3.6 5 0 10M3.6 4q-5.6 8 0 16M20.4 4q5.6 8 0 16" fill="none" stroke="#E5484D" stroke-width="1.7" stroke-linecap="round"/></svg>Disconnected Tools?</span></h3>
          <p class="cx-p">Unify your departments, projects, people and processes with a custom-built Business OS designed around your workflows</p>
          <ul class="cx-checks" role="list"><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Custom ERP &amp; Business OS</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Project &amp; Operations Management</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>CRM &amp; Workflow Management</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Department-Wise Systems Automation</li></ul>
          <span class="cx-stack__l">Technologies we engineer with</span>
          <div class="marquee marquee--tech cx-tech"><ul class="marquee__track" role="list"><li class="cx-logo"><img src="/assets/home10/tech/azure.webp" alt="Microsoft Azure" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/aws.webp" alt="AWS" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/mongodb.webp" alt="MongoDB" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/supabase.webp" alt="Supabase" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/react.webp" alt="React" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/nodejs.webp" alt="Node.js" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/python.webp" alt="Python" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/docker.webp" alt="Docker" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/github.webp" alt="GitHub" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/postgresql.webp" alt="PostgreSQL" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/redis.webp" alt="Redis" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/cloudflare.webp" alt="Cloudflare" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/azure.webp" alt="Microsoft Azure" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/aws.webp" alt="AWS" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/mongodb.webp" alt="MongoDB" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/supabase.webp" alt="Supabase" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/react.webp" alt="React" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/nodejs.webp" alt="Node.js" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/python.webp" alt="Python" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/docker.webp" alt="Docker" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/github.webp" alt="GitHub" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/postgresql.webp" alt="PostgreSQL" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/redis.webp" alt="Redis" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/cloudflare.webp" alt="Cloudflare" loading="lazy"></li></ul></div>
          <a class="cx-btn" href="/book" data-book>Let&#x27;s Build a Solution, Not Just Software<svg class="ic" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
        </div>
        <div class="cx-panel" role="region" id="cxp-2" aria-labelledby="cxt-2" style="--ac:#E8E2F5;--pt:#DCEFF0">
          <svg class="cx-ic" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><path d="M6 10h8l7 30h28l6-20H17"/><path d="M23 32h30"/><circle cx="25" cy="50" r="4"/><circle cx="46" cy="50" r="4"/></svg>
          <span class="cx-tag">High-Performance E-Commerce</span>
          <h3 class="cx-h">Ready To Turn Your Ecommerce Vision Into A <span class="cx-mk"><svg class="cx-hi" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 10V6.5a3 3 0 0 1 6 0V10" fill="none" stroke="#4F46C8" stroke-width="1.9" stroke-linecap="round"/><path d="M5 8h14l-1.2 12.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8z" fill="#5B5BD6"/><path d="M9.4 15.2l2 2 3.6-3.6" fill="none" stroke="#fff" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>High-Performing Online Store?</span></h3>
          <p class="cx-p">Create conversion-focused ecommerce experiences around your brand story, with custom storefronts and customer journeys designed to increase sales and scale.</p>
          <ul class="cx-checks" role="list"><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Brand Story-Driven Storefronts</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Custom Ecommerce Development</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>App-Free Functionality</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Customer-Centric &amp; Scalable Experiences</li></ul>
          <span class="cx-stack__l">Technologies we engineer with</span>
          <div class="marquee marquee--tech cx-tech"><ul class="marquee__track" role="list"><li class="cx-logo"><img src="/assets/home10/tech/shopify.webp" alt="Shopify" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/razorpay.webp" alt="Razorpay" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/stripe.webp" alt="Stripe" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/shiprocket.webp" alt="Shiprocket" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/shopify.webp" alt="Shopify" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/razorpay.webp" alt="Razorpay" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/stripe.webp" alt="Stripe" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/shiprocket.webp" alt="Shiprocket" loading="lazy"></li></ul></div>
          <a class="cx-btn" href="/book" data-book>Let&#x27;s Build a High-Converting Ecommerce Store<svg class="ic" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
        </div>
        <div class="cx-panel" role="region" id="cxp-3" aria-labelledby="cxt-3" style="--ac:#F8E6D6;--pt:#E3E5F6">
          <svg class="cx-ic" viewBox="0 0 64 64" aria-hidden="true" focusable="false"><rect x="16" y="16" width="32" height="32" rx="6"/><path d="M24 8v8M32 8v8M40 8v8M24 48v8M32 48v8M40 48v8M8 24h8M8 32h8M8 40h8M48 24h8M48 32h8M48 40h8"/><path d="M32 22l2.6 7.4L42 32l-7.4 2.6L32 42l-2.6-7.4L22 32l7.4-2.6z"/></svg>
          <span class="cx-tag">AI-Powered Business Solutions</span>
          <h3 class="cx-h">Are <span class="cx-mk"><svg class="cx-hi" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9.5" fill="#E8833A"/><path d="M12 6.8V12l3.6 2.2" fill="none" stroke="#fff" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>Manual Tasks</span> And <span class="cx-mk"><svg class="cx-hi" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 11a7.5 7.5 0 0 1 12.8-5.2L18.5 7.5M20 13a7.5 7.5 0 0 1-12.8 5.2L5.5 16.5" fill="none" stroke="#6E56CF" stroke-width="2.2" stroke-linecap="round"/><path d="M18.5 3v4.5H14M5.5 21v-4.5H10" fill="none" stroke="#6E56CF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>Repetitive Workflows</span> Slowing Your Business Growth?</h3>
          <p class="cx-p">Transform repetitive business processes with intelligent AI solutions and automation designed to reduce manual work, improve efficiency and scale operations.</p>
          <ul class="cx-checks" role="list"><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>AI-Powered Business Solutions</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Intelligent Workflow Automation</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>WhatsApp &amp; Communication Automation</li><li><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Custom AI Applications &amp; Agents</li></ul>
          <span class="cx-stack__l">Technologies we engineer with</span>
          <div class="marquee marquee--tech cx-tech"><ul class="marquee__track" role="list"><li class="cx-logo"><img src="/assets/home10/tech/openai.webp" alt="OpenAI" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/claude.webp" alt="Claude" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/gemini.webp" alt="Gemini" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/elevenlabs.webp" alt="ElevenLabs" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/n8n.webp" alt="n8n" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/zapier.webp" alt="Zapier" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/make.webp" alt="Make" loading="lazy"></li><li class="cx-logo"><img src="/assets/home10/tech/power-automate.webp" alt="Microsoft Power Automate" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/openai.webp" alt="OpenAI" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/claude.webp" alt="Claude" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/gemini.webp" alt="Gemini" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/elevenlabs.webp" alt="ElevenLabs" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/n8n.webp" alt="n8n" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/zapier.webp" alt="Zapier" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/make.webp" alt="Make" loading="lazy"></li><li aria-hidden="true" class="cx-logo"><img src="/assets/home10/tech/power-automate.webp" alt="Microsoft Power Automate" loading="lazy"></li></ul></div>
          <a class="cx-btn" href="/book" data-book>Let&#x27;s Automate the Way You Work<svg class="ic" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
        </div>
        <div class="cx-panel" role="region" id="cxp-4" aria-labelledby="cxt-4" style="--ac:#F5E1E9;--pt:#EDF0D7">
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
$swIc = fn (string $id): string => '<svg class="ic" aria-hidden="true" focusable="false"><use href="#' . $id . '"/></svg>';
?>
<section class="sw sw--work" id="case-studies">
  <div class="wrap">
    <header class="sw-head">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Selected Work. Proven Implementation</span>
      <h2 class="sw-h2 reveal" style="--d:.06s">We Understood. We Engineered. We Made It Work</h2>
      <p class="sw-sub reveal" style="--d:.12s">We work closely with businesses to understand their challenges, engineer tailored technology solutions and implement systems that make everyday operations more connected, efficient and scalable.</p>
      <p class="sw-nda reveal" style="--d:.16s"><?= $swIc('i-lock') ?>Client names and identities remain confidential, as every engagement is governed by a NDA.</p>
    </header>
  </div>
  <div class="wrap swx-wrap">
    <div class="swx reveal" data-swx style="--n:<?= count($swProjects) - 1 ?>">
<?php foreach ($swProjects as $i => $p): $n = sprintf('%02d', $i + 1); ?>
      <article class="swx-p<?= $i === 0 ? ' is-open' : '' ?>" data-accent="<?= h($p['accent']) ?>">
        <img class="swx-tex" src="<?= h($p['photo']) ?>" alt="" width="1000" height="1000" loading="lazy" decoding="async">
        <button type="button" class="swx-tab" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="swx-<?= $i + 1 ?>">
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
      <a class="h10-btn h10-btn--green" href="#solutions"><span>Explore Our Service Portfolio</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
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
      <li class="step spot reveal" style="--d:0.0s">
        <span class="step__node" aria-hidden="true"><b>01</b></span>
        <div class="step__card">
          <div class="step__art il"><svg class="il-svg" viewBox="0 0 200 140" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="hw-discover" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="200" height="140"/><rect width="200" height="140" fill="url(#hw-discover)"/><path class="ln" d="M100 40 V64 M100 64 H52 V84 M100 64 H148 V84" /><rect class="c" x="78" y="22" width="44" height="22" rx="8" /><rect class="g" x="88" y="30" width="24" height="6" rx="3.0" /><rect class="c" x="30" y="84" width="44" height="22" rx="8" /><rect class="k2" x="40" y="92" width="24" height="6" rx="3.0" /><rect class="c" x="126" y="84" width="44" height="22" rx="8" /><rect class="k2" x="136" y="92" width="24" height="6" rx="3.0" /><g class="scan"><circle cx="112" cy="84" r="26" class="lens"/><path class="gl" d="M131 103 L152 124" stroke-width="6"/></g></svg></div>
          <span class="step__k">01 — Discover the Business</span>
          <h3>Understand how your business actually operates.</h3>
          <p>We study your existing processes, teams, tools, workflows and operational challenges to identify where technology can create meaningful value.</p>
          <ul class="tags"><li>Business Discovery</li><li>Process Analysis</li><li>Workflow Mapping</li></ul>
        </div>
      </li>
      <li class="step spot reveal" style="--d:0.1s">
        <span class="step__node" aria-hidden="true"><b>02</b></span>
        <div class="step__card">
          <div class="step__art il"><svg class="il-svg" viewBox="0 0 200 140" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="hw-engineer" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="200" height="140"/><rect width="200" height="140" fill="url(#hw-engineer)"/><rect class="c" x="22" y="18" width="156" height="104" rx="14" /><path class="ln faint" d="M22 44 H178 M22 70 H178 M22 96 H178 M56 18 V122 M100 18 V122 M144 18 V122" /><rect class="gs2" x="36" y="28" width="34" height="22" rx="6" /><rect class="g" x="130" y="28" width="34" height="22" rx="6" /><rect class="gs2" x="84" y="84" width="34" height="22" rx="6" /><path class="gl flow" d="M70 39 H130 M147 50 V66 H101 V84" stroke-dasharray="4 5"/><circle class="g" cx="147" cy="66" r="4" /></svg></div>
          <span class="step__k">02 — Engineer the Workflows</span>
          <h3>Design technology around real business requirements.</h3>
          <p>We map processes, redesign workflows and architect the right combination of custom software, existing platforms and AI capabilities.</p>
          <ul class="tags"><li>Workflow Engineering</li><li>System Architecture</li><li>Solution Design</li></ul>
        </div>
      </li>
      <li class="step spot reveal" style="--d:0.2s">
        <span class="step__node" aria-hidden="true"><b>03</b></span>
        <div class="step__card">
          <div class="step__art il"><svg class="il-svg" viewBox="0 0 200 140" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="hw-build" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="200" height="140"/><rect width="200" height="140" fill="url(#hw-build)"/><rect class="c" x="26" y="18" width="120" height="96" rx="14" /><circle class="k2" cx="42" cy="32" r="3.5" /><circle class="k2" cx="54" cy="32" r="3.5" /><circle class="k2" cx="66" cy="32" r="3.5" /><rect class="g typeln" x="40" y="46" width="40" height="6" rx="3.0" style="--d:0.0s"/><rect class="k2 typeln" x="48" y="58" width="60" height="6" rx="3.0" style="--d:0.25s"/><rect class="k2 typeln" x="48" y="70" width="32" height="6" rx="3.0" style="--d:0.5s"/><rect class="g typeln" x="40" y="82" width="52" height="6" rx="3.0" style="--d:0.75s"/><rect class="k2 typeln" x="48" y="94" width="70" height="6" rx="3.0" style="--d:1.0s"/><g class="float" style="--d:0.3s"><rect class="c" x="128" y="66" width="54" height="40" rx="12" /><svg x="142" y="74" width="26" height="26" style="color:var(--green)" ><use href="#i-plug"/></svg></g></svg></div>
          <span class="step__k">03 — Build & Integrate</span>
          <h3>Develop and connect technology within your business environment.</h3>
          <p>We build custom applications, integrate existing systems and implement AI-powered workflows that solve practical business problems.</p>
          <ul class="tags"><li>Custom Development</li><li>AI Implementation</li><li>API Integration</li></ul>
        </div>
      </li>
      <li class="step spot reveal" style="--d:0.30000000000000004s">
        <span class="step__node" aria-hidden="true"><b>04</b></span>
        <div class="step__card">
          <div class="step__art il"><svg class="il-svg" viewBox="0 0 200 140" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="hw-deploy" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="200" height="140"/><rect width="200" height="140" fill="url(#hw-deploy)"/><circle cx="100" cy="70" r="42" class="ring spin" fill="none"/><circle cx="100" cy="70" r="30" class="g"/><svg x="84" y="54" width="32" height="32" style="color:#fff" ><use href="#i-rocket"/></svg><g class="float" style="--d:0.2s"><rect class="c" x="24" y="92" width="44" height="28" rx="8" /><rect class="g rise" x="32" y="106" width="6" height="6" rx="2" style="--d:0.0s"/><rect class="g rise" x="42" y="100" width="6" height="12" rx="2" style="--d:0.15s"/><rect class="g rise" x="52" y="94" width="6" height="18" rx="2" style="--d:0.3s"/></g><g class="float" style="--d:0.7s"><rect class="c" x="134" y="22" width="44" height="26" rx="13" /><circle class="g blink" cx="148" cy="35" r="4" /><rect class="k" x="156" y="32" width="16" height="6" rx="3.0" /></g></svg></div>
          <span class="step__k">04 — Deploy & Evolve</span>
          <h3>Make technology work in everyday business operations.</h3>
          <p>We deploy solutions, work with your teams, support adoption and continuously improve systems based on real operational feedback.</p>
          <ul class="tags"><li>Deployment</li><li>Team Enablement</li><li>Optimization</li><li>Continuous Improvement</li></ul>
        </div>
      </li></ol>
    </div>
  </div>
</section>
<section class="prog" id="program">
  <div class="prog__glow" aria-hidden="true"></div>
  <div class="wrap">
    <div class="prog__head">
      <span class="pill pill--mint reveal"><span class="pill__dot" aria-hidden="true"></span>A Structured Business Implementation Program</span>
      <h2 class="h2 h2--light reveal" style="--d:.06s">Transform the Way Your Business Works.<br><span class="grn">In 8 Weeks.</span></h2>
      <p class="lede lede--light reveal" style="--d:.1s">Move beyond disconnected tools, manual processes and operational bottlenecks.</p>
      <p class="lede lede--light reveal" style="--d:.14s">Our structured 8-week transformation program helps businesses understand their challenges, redesign workflows, implement intelligent technology and establish connected business operations.</p>
    </div>
    <div class="timeline reveal" data-timeline>
      <div class="phases"><div class="phase" data-phase="0"><span class="phase__k">Phase 01 · Week 01–04</span><b>Engineer & Build</b>
      <span class="phase__t">Understand. Design. Build.</span><span class="phase__d"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg><em>Deliverable</em> Business Systems Ready for Implementation</span></div><div class="phase" data-phase="1"><span class="phase__k">Phase 02 · Week 05–08</span><b>Implement & Go Live</b>
      <span class="phase__t">Connect. Deploy. Train.</span><span class="phase__d"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-check"/></svg><em>Deliverable</em> Live Systems & Connected Workflows</span></div></div>
      <div class="wkrail" role="tablist" aria-label="8-week program timeline"><button type="button" role="tab" class="wk" id="wk-tab-1" aria-controls="wk-panel-1" aria-selected="true" tabindex="0" data-i="0">
        <span class="wk__n">01</span><span class="wk__l">Week 01</span><i class="wk__bar"><u></u></i></button><button type="button" role="tab" class="wk" id="wk-tab-2" aria-controls="wk-panel-2" aria-selected="false" tabindex="-1" data-i="1">
        <span class="wk__n">02</span><span class="wk__l">Week 02</span><i class="wk__bar"><u></u></i></button><button type="button" role="tab" class="wk" id="wk-tab-3" aria-controls="wk-panel-3" aria-selected="false" tabindex="-1" data-i="2">
        <span class="wk__n">03</span><span class="wk__l">Week 03</span><i class="wk__bar"><u></u></i></button><button type="button" role="tab" class="wk" id="wk-tab-4" aria-controls="wk-panel-4" aria-selected="false" tabindex="-1" data-i="3">
        <span class="wk__n">04</span><span class="wk__l">Week 04</span><i class="wk__bar"><u></u></i></button><button type="button" role="tab" class="wk" id="wk-tab-5" aria-controls="wk-panel-5" aria-selected="false" tabindex="-1" data-i="4">
        <span class="wk__n">05</span><span class="wk__l">Week 05</span><i class="wk__bar"><u></u></i></button><button type="button" role="tab" class="wk" id="wk-tab-6" aria-controls="wk-panel-6" aria-selected="false" tabindex="-1" data-i="5">
        <span class="wk__n">06</span><span class="wk__l">Week 06</span><i class="wk__bar"><u></u></i></button><button type="button" role="tab" class="wk" id="wk-tab-7" aria-controls="wk-panel-7" aria-selected="false" tabindex="-1" data-i="6">
        <span class="wk__n">07</span><span class="wk__l">Week 07</span><i class="wk__bar"><u></u></i></button><button type="button" role="tab" class="wk" id="wk-tab-8" aria-controls="wk-panel-8" aria-selected="false" tabindex="-1" data-i="7">
        <span class="wk__n">08</span><span class="wk__l">Week 08</span><i class="wk__bar"><u></u></i></button><i class="wkrail__line" aria-hidden="true"><u></u></i></div>
      <div class="wkstage">
      <div class="wkp" role="tabpanel" id="wk-panel-1" aria-labelledby="wk-tab-1"  tabindex="0">
        <div class="wkp__copy">
          <span class="wkp__big" aria-hidden="true">01</span>
          <span class="wkp__k">01 — Week 01 <i>|</i> Discover & Audit</span>
          <h3>Understand Your Business from the Inside.</h3>
          <ul class="tags tags--dark"><li>Existing Workflows</li><li>Organizational Hierarchy</li><li>Work Culture</li><li>Existing Systems &amp; Software</li></ul>
          <span class="wkp__phase">Phase 01 — Engineer & Build</span>
        </div>
        <div class="wkp__art il on-dark"><svg class="il-svg" viewBox="0 0 320 200" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="wk1" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="320" height="200"/><rect width="320" height="200" fill="url(#wk1)"/><path class="ln" d="M160 56 V84 M160 84 H80 V108 M160 84 H240 V108 M160 84 V108" /><rect class="c" x="130" y="30" width="60" height="28" rx="9" /><rect class="g" x="142" y="40" width="36" height="7" rx="3.5" /><rect class="c" x="50" y="108" width="60" height="28" rx="9" /><rect class="k2" x="62" y="118" width="36" height="7" rx="3.5" /><rect class="c" x="130" y="108" width="60" height="28" rx="9" /><rect class="k2" x="142" y="118" width="36" height="7" rx="3.5" /><rect class="c" x="210" y="108" width="60" height="28" rx="9" /><rect class="k2" x="222" y="118" width="36" height="7" rx="3.5" /><g class="scan"><circle cx="196" cy="128" r="30" class="lens"/><path class="gl" d="M218 150 L244 176" stroke-width="7"/></g></svg></div>
      </div>
      <div class="wkp" role="tabpanel" id="wk-panel-2" aria-labelledby="wk-tab-2" hidden tabindex="0">
        <div class="wkp__copy">
          <span class="wkp__big" aria-hidden="true">02</span>
          <span class="wkp__k">02 — Week 02 <i>|</i> Design & Engineer</span>
          <h3>Design Technology Around Your Business.</h3>
          <ul class="tags tags--dark"><li>Workflow Mapping</li><li>Process Redesign</li><li>System Architecture</li><li>Solution Planning</li></ul>
          <span class="wkp__phase">Phase 01 — Engineer & Build</span>
        </div>
        <div class="wkp__art il on-dark"><svg class="il-svg" viewBox="0 0 320 200" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="wk2" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="320" height="200"/><rect width="320" height="200" fill="url(#wk2)"/><rect class="c" x="30" y="40" width="70" height="34" rx="10" /><rect class="k2" x="44" y="54" width="42" height="7" rx="3.5" /><rect class="c" x="124" y="40" width="70" height="34" rx="10" /><rect class="k2" x="138" y="54" width="42" height="7" rx="3.5" /><path class="ln" d="M100 57 H124" /><rect class="c g-outline" x="30" y="122" width="70" height="34" rx="10" /><rect class="g" x="44" y="136" width="42" height="7" rx="3.5" /><rect class="c g-outline" x="124" y="122" width="70" height="34" rx="10" /><rect class="g" x="138" y="136" width="42" height="7" rx="3.5" /><rect class="c g-outline" x="218" y="82" width="70" height="34" rx="10" /><rect class="g" x="232" y="96" width="42" height="7" rx="3.5" /><path class="gl flow" d="M100 139 H124 M194 139 C210 139 206 99 218 99" stroke-dasharray="4 5"/><path class="ln" d="M194 57 C230 57 230 70 253 82" /><svg x="240" y="36" width="28" height="28" style="color:var(--green)" ><use href="#i-workflow"/></svg></svg></div>
      </div>
      <div class="wkp" role="tabpanel" id="wk-panel-3" aria-labelledby="wk-tab-3" hidden tabindex="0">
        <div class="wkp__copy">
          <span class="wkp__big" aria-hidden="true">03</span>
          <span class="wkp__k">03 — Week 03 <i>|</i> Build & Configure</span>
          <h3>Build Solutions Around Real Business Needs.</h3>
          <ul class="tags tags--dark"><li>Custom Development</li><li>System Configuration</li><li>Feature Engineering</li><li>Application Development</li></ul>
          <span class="wkp__phase">Phase 01 — Engineer & Build</span>
        </div>
        <div class="wkp__art il on-dark"><svg class="il-svg" viewBox="0 0 320 200" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="wk3" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="320" height="200"/><rect width="320" height="200" fill="url(#wk3)"/><rect class="c" x="32" y="28" width="170" height="144" rx="16" /><circle class="k2" cx="50" cy="44" r="4" /><circle class="k2" cx="63" cy="44" r="4" /><circle class="k2" cx="76" cy="44" r="4" /><rect class="g typeln" x="48" y="64" width="50" height="7" rx="3.5" style="--d:0.0s"/><rect class="k2 typeln" x="60" y="80" width="86" height="7" rx="3.5" style="--d:0.2s"/><rect class="k2 typeln" x="60" y="96" width="60" height="7" rx="3.5" style="--d:0.4s"/><rect class="g typeln" x="48" y="112" width="74" height="7" rx="3.5" style="--d:0.6000000000000001s"/><rect class="k2 typeln" x="60" y="128" width="98" height="7" rx="3.5" style="--d:0.8s"/><rect class="k2 typeln" x="60" y="144" width="44" height="7" rx="3.5" style="--d:1.0s"/><g class="float" style="--d:0.3s"><rect class="c" x="220" y="74" width="72" height="74" rx="16" /><g class="spin"><svg x="236" y="90" width="40" height="40" style="color:var(--green)" ><use href="#i-gear"/></svg></g></g></svg></div>
      </div>
      <div class="wkp" role="tabpanel" id="wk-panel-4" aria-labelledby="wk-tab-4" hidden tabindex="0">
        <div class="wkp__copy">
          <span class="wkp__big" aria-hidden="true">04</span>
          <span class="wkp__k">04 — Week 04 <i>|</i> Integrate & Prepare</span>
          <h3>Connect Systems and Prepare for Implementation.</h3>
          <ul class="tags tags--dark"><li>API Integration</li><li>Data Structuring</li><li>System Testing</li><li>Deployment Preparation</li></ul>
          <span class="wkp__phase">Phase 01 — Engineer & Build</span>
        </div>
        <div class="wkp__art il on-dark"><svg class="il-svg" viewBox="0 0 320 200" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="wk4" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="320" height="200"/><rect width="320" height="200" fill="url(#wk4)"/><rect class="c" x="26" y="50" width="96" height="100" rx="14" /><rect class="k" x="40" y="66" width="56" height="7" rx="3.5" /><rect class="k2" x="40" y="82" width="40" height="6" rx="3.0" /><rect class="gs" x="40" y="104" width="68" height="26" rx="8" /><rect class="c" x="198" y="50" width="96" height="100" rx="14" /><rect class="k" x="212" y="66" width="56" height="7" rx="3.5" /><rect class="k2" x="212" y="82" width="40" height="6" rx="3.0" /><rect class="gs" x="212" y="104" width="68" height="26" rx="8" /><path class="gl flow" d="M122 100 H198" stroke-dasharray="4 5"/><circle class="g" cx="160" cy="100" r="22" /><svg x="148" y="88" width="24" height="24" style="color:#fff" ><use href="#i-plug"/></svg><circle class="g" cx="280" cy="56" r="9" /><path class="tick" d="M275.5 56l3 3 5.5-6" /></svg></div>
      </div>
      <div class="wkp" role="tabpanel" id="wk-panel-5" aria-labelledby="wk-tab-5" hidden tabindex="0">
        <div class="wkp__copy">
          <span class="wkp__big" aria-hidden="true">05</span>
          <span class="wkp__k">05 — Week 05 <i>|</i> Live Data Integration</span>
          <h3>Bring Your Business Data into the System.</h3>
          <ul class="tags tags--dark"><li>Lead Migration</li><li>Customer Records</li><li>Project Data</li><li>Financial Records</li></ul>
          <span class="wkp__phase">Phase 02 — Implement & Go Live</span>
        </div>
        <div class="wkp__art il on-dark"><svg class="il-svg" viewBox="0 0 320 200" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="wk5" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="320" height="200"/><rect width="320" height="200" fill="url(#wk5)"/><rect class="c" x="30" y="40" width="80" height="120" rx="14" /><rect class="k" x="42" y="56" width="56" height="7" rx="3.5" /><rect class="k2" x="42" y="76" width="56" height="7" rx="3.5" /><rect class="k" x="42" y="96" width="56" height="7" rx="3.5" /><rect class="k2" x="42" y="116" width="56" height="7" rx="3.5" /><rect class="k" x="42" y="136" width="56" height="7" rx="3.5" /><rect class="g moverow" x="130" y="70" width="40" height="8" rx="4" style="--d:0.0s"/><rect class="g moverow" x="130" y="96" width="40" height="8" rx="4" style="--d:0.35s"/><rect class="g moverow" x="130" y="122" width="40" height="8" rx="4" style="--d:0.7s"/><path class="ln" d="M118 100 H200" stroke-dasharray="3 5"/><g class="float" style="--d:0.2s"><ellipse class="c" cx="250" cy="58" rx="46" ry="16"/><path class="c" d="M204 58v70c0 9 21 16 46 16s46-7 46-16V58"/><path class="ln" d="M204 93c0 9 21 16 46 16s46-7 46-16"/><ellipse class="gs" cx="250" cy="58" rx="46" ry="16" style="opacity:.6"/><circle class="g" cx="250" cy="124" r="6" /></g></svg></div>
      </div>
      <div class="wkp" role="tabpanel" id="wk-panel-6" aria-labelledby="wk-tab-6" hidden tabindex="0">
        <div class="wkp__copy">
          <span class="wkp__big" aria-hidden="true">06</span>
          <span class="wkp__k">06 — Week 06 <i>|</i> Workflow Implementation</span>
          <h3>Connect Your Teams, Systems and Processes.</h3>
          <ul class="tags tags--dark"><li>Department Integration</li><li>Workflow Automation</li><li>Task Coordination</li><li>Process Activation</li></ul>
          <span class="wkp__phase">Phase 02 — Implement & Go Live</span>
        </div>
        <div class="wkp__art il on-dark"><svg class="il-svg" viewBox="0 0 320 200" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="wk6" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="320" height="200"/><rect width="320" height="200" fill="url(#wk6)"/><circle class="ring spin" cx="160" cy="100" r="62" fill="none"/><path class="ln flow" d="M160 40 L258 100" style="--d:0.0s"/><path class="ln flow" d="M258 100 L160 160" style="--d:0.2s"/><path class="ln flow" d="M160 160 L62 100" style="--d:0.4s"/><path class="ln flow" d="M62 100 L160 40" style="--d:0.6000000000000001s"/><g class="float" style="--d:0.0s"><rect class="c" x="130" y="24" width="60" height="32" rx="10" /><rect class="g" x="142" y="37" width="36" height="7" rx="3.5" /></g><g class="float" style="--d:0.3s"><rect class="c" x="228" y="84" width="60" height="32" rx="10" /><rect class="k2" x="240" y="97" width="36" height="7" rx="3.5" /></g><g class="float" style="--d:0.6s"><rect class="c" x="130" y="144" width="60" height="32" rx="10" /><rect class="k2" x="142" y="157" width="36" height="7" rx="3.5" /></g><g class="float" style="--d:0.8999999999999999s"><rect class="c" x="32" y="84" width="60" height="32" rx="10" /><rect class="k2" x="44" y="97" width="36" height="7" rx="3.5" /></g><circle class="g" cx="160" cy="100" r="22" /><svg x="148" y="88" width="24" height="24" style="color:#fff" ><use href="#i-gear"/></svg></svg></div>
      </div>
      <div class="wkp" role="tabpanel" id="wk-panel-7" aria-labelledby="wk-tab-7" hidden tabindex="0">
        <div class="wkp__copy">
          <span class="wkp__big" aria-hidden="true">07</span>
          <span class="wkp__k">07 — Week 07 <i>|</i> Team Deployment & Training</span>
          <h3>Prepare Your People to Work with the New Systems.</h3>
          <ul class="tags tags--dark"><li>Employee Onboarding</li><li>Role Assignment</li><li>Hands-on Training</li><li>User Enablement</li></ul>
          <span class="wkp__phase">Phase 02 — Implement & Go Live</span>
        </div>
        <div class="wkp__art il on-dark"><svg class="il-svg" viewBox="0 0 320 200" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="wk7" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="320" height="200"/><rect width="320" height="200" fill="url(#wk7)"/><rect class="c" x="80" y="34" width="160" height="96" rx="14" /><rect class="g" x="96" y="50" width="80" height="8" rx="4.0" /><rect class="k" x="96" y="68" width="128" height="6" rx="3.0" /><rect class="k2" x="96" y="82" width="100" height="6" rx="3.0" /><rect class="gs" x="96" y="98" width="50" height="18" rx="9" /><path class="ln" d="M160 130 V148 M130 148 H190" /><g class="float" style="--d:0.0s"><circle class="gs" cx="60" cy="172" r="12" /><circle class="g" cx="60" cy="168" r="5" /><path class="g" d="M51 180 a9 7 0 0 1 18 0z"/></g><g class="float" style="--d:0.35s"><circle class="gs" cx="160" cy="172" r="12" /><circle class="g" cx="160" cy="168" r="5" /><path class="g" d="M151 180 a9 7 0 0 1 18 0z"/></g><g class="float" style="--d:0.7s"><circle class="gs" cx="260" cy="172" r="12" /><circle class="g" cx="260" cy="168" r="5" /><path class="g" d="M251 180 a9 7 0 0 1 18 0z"/></g><rect class="g" x="226" y="20" width="62" height="22" rx="11" /><svg x="236" y="23" width="16" height="16" style="color:#fff" ><use href="#i-check"/></svg><rect class="wh" x="256" y="28" width="24" height="6" rx="3.0" /></svg></div>
      </div>
      <div class="wkp" role="tabpanel" id="wk-panel-8" aria-labelledby="wk-tab-8" hidden tabindex="0">
        <div class="wkp__copy">
          <span class="wkp__big" aria-hidden="true">08</span>
          <span class="wkp__k">08 — Week 08 <i>|</i> Go Live & Optimization</span>
          <h3>Run Your Business Through Connected Systems.</h3>
          <p class="wkp__body">A connected business environment where everyday operations, from leads to profit, are tracked through structured workflows.</p>
          <span class="wkp__phase">Phase 02 — Implement & Go Live</span>
        </div>
        <div class="wkp__art il on-dark"><svg class="il-svg" viewBox="0 0 320 200" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="wk8" width="14" height="14" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern></defs><rect class="pn" width="320" height="200"/><rect width="320" height="200" fill="url(#wk8)"/><g class="float" style="--d:0.0s"><rect class="c" x="28" y="56" width="84" height="66" rx="14" /><text class="lbl" x="70" y="84" text-anchor="middle" >Leads</text><rect class="g rise" x="44" y="104" width="8" height="8" rx="2" style="--d:0.0s;opacity:0.35"/><rect class="g rise" x="58" y="98" width="8" height="14" rx="2" style="--d:0.08s;opacity:0.55"/><rect class="g rise" x="72" y="92" width="8" height="20" rx="2" style="--d:0.16s;opacity:0.75"/><rect class="g rise" x="86" y="86" width="8" height="26" rx="2" style="--d:0.24s;opacity:0.9500000000000001"/></g><g class="float" style="--d:0.3s"><rect class="c" x="118" y="56" width="84" height="66" rx="14" /><text class="lbl" x="160" y="84" text-anchor="middle" >Projects</text><rect class="g rise" x="134" y="104" width="8" height="8" rx="2" style="--d:0.24s;opacity:0.35"/><rect class="g rise" x="148" y="98" width="8" height="14" rx="2" style="--d:0.32s;opacity:0.55"/><rect class="g rise" x="162" y="92" width="8" height="20" rx="2" style="--d:0.4s;opacity:0.75"/><rect class="g rise" x="176" y="86" width="8" height="26" rx="2" style="--d:0.48s;opacity:0.9500000000000001"/></g><g class="float" style="--d:0.6s"><rect class="c" x="208" y="56" width="84" height="66" rx="14" /><text class="lbl" x="250" y="84" text-anchor="middle" >Profit</text><rect class="g rise" x="224" y="104" width="8" height="8" rx="2" style="--d:0.48s;opacity:0.35"/><rect class="g rise" x="238" y="98" width="8" height="14" rx="2" style="--d:0.56s;opacity:0.55"/><rect class="g rise" x="252" y="92" width="8" height="20" rx="2" style="--d:0.64s;opacity:0.75"/><rect class="g rise" x="266" y="86" width="8" height="26" rx="2" style="--d:0.72s;opacity:0.9500000000000001"/></g><path class="gl flow" d="M112 89 H118 M202 89 H208" /><rect class="g" x="104" y="18" width="112" height="26" rx="13" /><circle class="wh blink" cx="122" cy="31" r="4" /><text class="lbl wht" x="134" y="35" text-anchor="start" >Live</text><path class="gl draw" d="M40 156 C90 150 120 138 160 140 S250 128 288 112" pathLength="1"/></svg></div>
      </div></div>
      <div class="wkctl">
        <button type="button" class="iconbtn" data-prev aria-label="Previous week"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-left"/></svg></button>
        <button type="button" class="iconbtn" data-play aria-label="Pause automatic progression" aria-pressed="false"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-pause"/></svg></button>
        <button type="button" class="iconbtn" data-next aria-label="Next week"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-right"/></svg></button>
      </div>
    </div>
    <div class="prog__cta reveal">
      <a class="h10-btn h10-btn--green" href="/book" data-book><span>Begin Your 8-Week Implementation Journey</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
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
      <div class="data__viz il reveal" data-viz><svg class="il-svg" viewBox="0 0 680 560" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="dv-g" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#46cf8a"/><stop offset="1" stop-color="#239a5c"/></linearGradient><pattern id="dv-d" width="18" height="18" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern><linearGradient id="dv-area" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#32B46F" stop-opacity=".35"/><stop offset="1" stop-color="#32B46F" stop-opacity="0"/></linearGradient></defs><rect class="pn" width="680" height="560"/><rect width="680" height="560" fill="url(#dv-d)"/><g class="stream" data-stream="1"><path class="ln flow" d="M196 84 C 280 84, 280 280, 332 280" style="--d:0.0s"/><circle r="4" class="g"><animateMotion dur="2.6s" begin="-0.0s" repeatCount="indefinite" path="M196 84 C 280 84, 280 280, 332 280"/></circle><circle r="4" class="g" opacity=".6"><animateMotion dur="2.6s" begin="-1.3s" repeatCount="indefinite" path="M196 84 C 280 84, 280 280, 332 280"/></circle></g><g class="stream" data-stream="2"><path class="ln flow" d="M196 214 C 280 214, 280 280, 332 280" style="--d:0.2s"/><circle r="4" class="g"><animateMotion dur="2.9s" begin="-0.45s" repeatCount="indefinite" path="M196 214 C 280 214, 280 280, 332 280"/></circle><circle r="4" class="g" opacity=".6"><animateMotion dur="2.9s" begin="-1.75s" repeatCount="indefinite" path="M196 214 C 280 214, 280 280, 332 280"/></circle></g><g class="stream" data-stream="3"><path class="ln flow" d="M196 344 C 280 344, 280 280, 332 280" style="--d:0.4s"/><circle r="4" class="g"><animateMotion dur="3.2s" begin="-0.9s" repeatCount="indefinite" path="M196 344 C 280 344, 280 280, 332 280"/></circle><circle r="4" class="g" opacity=".6"><animateMotion dur="3.2s" begin="-2.2s" repeatCount="indefinite" path="M196 344 C 280 344, 280 280, 332 280"/></circle></g><g class="stream" data-stream="4"><path class="ln flow" d="M196 474 C 280 474, 280 280, 332 280" style="--d:0.6000000000000001s"/><circle r="4" class="g"><animateMotion dur="3.5s" begin="-1.35s" repeatCount="indefinite" path="M196 474 C 280 474, 280 280, 332 280"/></circle><circle r="4" class="g" opacity=".6"><animateMotion dur="3.5s" begin="-2.6500000000000004s" repeatCount="indefinite" path="M196 474 C 280 474, 280 280, 332 280"/></circle></g><path class="ln flow thick" d="M412 280 H452"/><g class="src" data-src="1"><rect class="c" x="16" y="38" width="180" height="92" rx="18" /><circle class="gs" cx="44" cy="66" r="15" /><svg x="35" y="57" width="18" height="18" style="color:var(--green)" ><use href="#i-users"/></svg><text class="lbl sm" x="68" y="71" text-anchor="start" >Customer & Sales</text><rect class="k" x="52" y="100" width="96" height="5" rx="2.5" /><rect class="k2" x="52" y="111" width="70" height="5" rx="2.5" /><rect class="g srcdot" x="150" y="100" width="32" height="16" rx="8" /></g><g class="src" data-src="2"><rect class="c" x="16" y="168" width="180" height="92" rx="18" /><circle class="gs" cx="44" cy="196" r="15" /><svg x="35" y="187" width="18" height="18" style="color:var(--green)" ><use href="#i-megaphone"/></svg><text class="lbl sm" x="68" y="201" text-anchor="start" >Marketing & Campaign</text><rect class="k" x="52" y="230" width="96" height="5" rx="2.5" /><rect class="k2" x="52" y="241" width="70" height="5" rx="2.5" /><rect class="g srcdot" x="150" y="230" width="32" height="16" rx="8" /></g><g class="src" data-src="3"><rect class="c" x="16" y="298" width="180" height="92" rx="18" /><circle class="gs" cx="44" cy="326" r="15" /><svg x="35" y="317" width="18" height="18" style="color:var(--green)" ><use href="#i-workflow"/></svg><text class="lbl sm" x="68" y="331" text-anchor="start" >Business Operations</text><rect class="k" x="52" y="360" width="96" height="5" rx="2.5" /><rect class="k2" x="52" y="371" width="70" height="5" rx="2.5" /><rect class="g srcdot" x="150" y="360" width="32" height="16" rx="8" /></g><g class="src" data-src="4"><rect class="c" x="16" y="428" width="180" height="92" rx="18" /><circle class="gs" cx="44" cy="456" r="15" /><svg x="35" y="447" width="18" height="18" style="color:var(--green)" ><use href="#i-chart"/></svg><text class="lbl sm" x="68" y="461" text-anchor="start" >Financial & Performance</text><rect class="k" x="52" y="490" width="96" height="5" rx="2.5" /><rect class="k2" x="52" y="501" width="70" height="5" rx="2.5" /><rect class="g srcdot" x="150" y="490" width="32" height="16" rx="8" /></g><circle class="pulse" cx="372" cy="280" r="42" fill="none" stroke="var(--green)" stroke-width="2"/><circle class="pulse" cx="372" cy="280" r="42" fill="none" stroke="var(--green)" stroke-width="2" style="--d:1.2s"/><circle cx="372" cy="280" r="38" fill="url(#dv-g)" class="coreshadow"/><svg x="354" y="262" width="36" height="36" style="color:#fff" ><use href="#i-sparkle"/></svg><rect class="c" x="452" y="40" width="212" height="480" rx="22" /><circle class="k2" cx="474" cy="64" r="4" /><circle class="k2" cx="486" cy="64" r="4" /><circle class="k2" cx="498" cy="64" r="4" /><rect class="k" x="572" y="60" width="72" height="8" rx="4.0" /><rect class="c2" x="468" y="88" width="58" height="52" rx="12" /><rect class="k2" x="476" y="98" width="28" height="5" rx="2.5" /><rect class="g" x="476" y="110" width="38" height="9" rx="4.5" style="opacity:.9"/><path class="gl" d="M476 132 l10 -6 l8 3 l10 -9 l8 4" style="stroke-width:1.6"/><rect class="c2" x="532" y="88" width="58" height="52" rx="12" /><rect class="k2" x="540" y="98" width="28" height="5" rx="2.5" /><rect class="k" x="540" y="110" width="38" height="9" rx="4.5" style="opacity:.9"/><path class="gl" d="M540 132 l10 -6 l8 3 l10 -9 l8 4" style="stroke-width:1.6"/><rect class="c2" x="596" y="88" width="58" height="52" rx="12" /><rect class="k2" x="604" y="98" width="28" height="5" rx="2.5" /><rect class="k" x="604" y="110" width="38" height="9" rx="4.5" style="opacity:.9"/><path class="gl" d="M604 132 l10 -6 l8 3 l10 -9 l8 4" style="stroke-width:1.6"/><rect class="c2" x="468" y="152" width="180" height="120" rx="14" /><rect class="k2" x="480" y="164" width="54" height="6" rx="3.0" /><path d="M480 250 C502 244 516 226 538 222 S570 200 592 192 S620 176 636 170 V258 H480Z" fill="url(#dv-area)"/><path class="gl draw" d="M480 250 C502 244 516 226 538 222 S570 200 592 192 S620 176 636 170" pathLength="1"/><circle class="g" cx="636" cy="170" r="4.5" /><rect class="c2" x="468" y="284" width="100" height="110" rx="14" /><rect class="k2" x="480" y="296" width="40" height="6" rx="3.0" /><rect class="g rise" x="480" y="350" width="10" height="30" rx="3" style="--d:0.0s;opacity:0.4"/><rect class="g rise" x="496" y="328" width="10" height="52" rx="3" style="--d:0.1s;opacity:0.52"/><rect class="g rise" x="512" y="340" width="10" height="40" rx="3" style="--d:0.2s;opacity:0.64"/><rect class="g rise" x="528" y="312" width="10" height="68" rx="3" style="--d:0.30000000000000004s;opacity:0.76"/><rect class="g rise" x="544" y="332" width="10" height="48" rx="3" style="--d:0.4s;opacity:0.88"/><rect class="c2" x="576" y="284" width="72" height="110" rx="14" /><circle cx="612" cy="339" r="22" fill="none" class="donut-bg" stroke-width="9"/><circle class="donut" cx="612" cy="339" r="22" fill="none" stroke="var(--green)" stroke-width="9" stroke-linecap="round" pathLength="100" stroke-dasharray="68 100" transform="rotate(-90 612 339)"/><rect class="c2" x="468" y="406" width="180" height="98" rx="14" /><rect class="k2" x="480" y="420" width="60" height="6" rx="3.0" /><rect class="k" x="556" y="420" width="36" height="6" rx="3.0" /><rect class="g" x="612" y="417" width="24" height="12" rx="6" /><rect class="k2" x="480" y="440" width="60" height="6" rx="3.0" /><rect class="k" x="556" y="440" width="52" height="6" rx="3.0" /><rect class="gs" x="612" y="437" width="24" height="12" rx="6" /><rect class="k2" x="480" y="460" width="60" height="6" rx="3.0" /><rect class="k" x="556" y="460" width="36" height="6" rx="3.0" /><rect class="gs" x="612" y="457" width="24" height="12" rx="6" /><rect class="k2" x="480" y="480" width="60" height="6" rx="3.0" /><rect class="k" x="556" y="480" width="52" height="6" rx="3.0" /><rect class="gs" x="612" y="477" width="24" height="12" rx="6" /></svg></div>
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
        <div class="dcard__top"><span class="dcard__ic"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-chart"/></svg></span><h3>Financial & Performance Data</h3></div>
        <p>Connect financial records and business performance metrics to understand revenue, costs and profitability.</p>
        <ul class="dlist" role="list"><li>Sales &amp; Revenue Records</li><li>Expenses &amp; Transactions</li><li>Invoices &amp; Payments</li><li>Budget &amp; Cash Flow</li><li>Project-Wise Costs</li><li>Profitability &amp; Financial Reports</li></ul>
      </article></div>
    </div>
    <div class="databanner reveal">
      <div><h3>Stop Looking at Scattered Reports.<br>Start Understanding Your Business.</h3><p>Connect your business data and turn information into actionable intelligence.</p></div>
      <a class="h10-btn h10-btn--green" href="#contact"><span>Explore Data Intelligence</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
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
<section class="indf" id="industry-focus">
  <div class="wrap">
    <div class="indf__head">
      <div>
        <span class="pill pill--mint reveal"><span class="pill__dot" aria-hidden="true"></span>Industry-Focused Engineering</span>
        <h2 class="h2 h2--light reveal" style="--d:.06s">Different Industries. Different Challenges. Technology Built to Fit.</h2>
      </div>
      <div class="reveal" style="--d:.1s">
        <p class="lede lede--light">Every industry has its own way of operating. From managing construction projects and healthcare operations to scaling ecommerce and manufacturing businesses, we understand that one-size-fits-all software doesn't solve every business challenge.</p>
        <p class="lede lede--light">Drawlead combines business understanding, custom engineering, AI and digital technology to build solutions around the unique requirements of each industry.</p>
      </div>
    </div>
    <div class="igrid">
      <article class="icard spot reveal" style="--d:0.0s">
        <div class="icard__art il on-dark"><svg class="il-svg" viewBox="0 0 480 300" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="in-construction-1" width="16" height="16" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern><linearGradient id="in-constructiong-2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#46cf8a"/><stop offset="1" stop-color="#239a5c"/></linearGradient></defs><rect class="pn" width="480" height="300"/><rect width="480" height="300" fill="url(#in-construction-1)"/><path class="ln" d="M40 262 H300" stroke-width="2"/><rect class="c" x="70" y="230" width="120" height="34" rx="6" /><rect class="k" x="82" y="238" width="22" height="16" rx="3" /><rect class="gs" x="116" y="238" width="22" height="16" rx="3" /><rect class="k" x="150" y="238" width="22" height="16" rx="3" /><rect class="c" x="70" y="192" width="120" height="34" rx="6" /><rect class="gs" x="82" y="200" width="22" height="16" rx="3" /><rect class="k" x="116" y="200" width="22" height="16" rx="3" /><rect class="gs" x="150" y="200" width="22" height="16" rx="3" /><rect class="c" x="70" y="154" width="120" height="34" rx="6" /><rect class="k" x="82" y="162" width="22" height="16" rx="3" /><rect class="gs" x="116" y="162" width="22" height="16" rx="3" /><rect class="k" x="150" y="162" width="22" height="16" rx="3" /><rect class="c" x="70" y="116" width="120" height="34" rx="6" /><rect class="gs" x="82" y="124" width="22" height="16" rx="3" /><rect class="k" x="116" y="124" width="22" height="16" rx="3" /><rect class="gs" x="150" y="124" width="22" height="16" rx="3" /><rect class="gs2" x="70" y="78" width="120" height="34" rx="6" style="stroke-dasharray:4 4"/><path class="ln crane" d="M232 262 V54 M232 54 H330 M232 54 L204 78 M300 54 V96" stroke-width="4"/><rect class="g" x="222" y="40" width="20" height="14" rx="3" /><rect class="g" x="292" y="96" width="18" height="14" rx="3" /><g class="swing"><path class="ln" d="M300 54 V110" stroke-width="1.6"/><rect class="g" x="284" y="104" width="32" height="18" rx="3" /></g><g class="float hovlift" style="--d:0.2s"><rect class="c" x="330" y="120" width="130" height="74" rx="14" /><text class="lbl" x="344" y="144" text-anchor="start" >Site</text><rect class="k" x="344" y="156" width="84" height="6" rx="3.0" /><rect class="k2" x="344" y="170" width="56" height="6" rx="3.0" /><rect class="g" x="420" y="160" width="26" height="14" rx="7" /></g><g class="float hovlift" style="--d:0.7s"><rect class="c" x="330" y="206" width="130" height="64" rx="14" /><text class="lbl sm" x="344" y="230" text-anchor="start" >Profit / project</text><rect class="gs2" x="344" y="242" width="100" height="8" rx="4.0" /><rect class="g" x="344" y="242" width="70" height="8" rx="4.0" /></g><g class="float" style="--d:0.4s"><rect class="c" x="330" y="28" width="130" height="76" rx="14" /><rect class="g rise" x="346" y="76" width="12" height="16" rx="3" style="--d:0.0s;opacity:0.4"/><rect class="g rise" x="366" y="64" width="12" height="28" rx="3" style="--d:0.12s;opacity:0.5"/><rect class="g rise" x="386" y="70" width="12" height="22" rx="3" style="--d:0.24s;opacity:0.6000000000000001"/><rect class="g rise" x="406" y="52" width="12" height="40" rx="3" style="--d:0.36s;opacity:0.7000000000000001"/><rect class="g rise" x="426" y="40" width="12" height="52" rx="3" style="--d:0.48s;opacity:0.8"/></g></svg></div>
        <div class="icard__body">
          <span class="icard__k">01 — Construction & Real Estate</span>
          <h3>Bring Every Project. Every Team. Every Operation Together.</h3>
          <p>Connect project management, site operations, procurement, materials, contractors and financial workflows through integrated construction business systems.</p>
          <div class="sol"><b>Solutions:</b><ul class="tags tags--dark"><li>Construction ERP</li><li>Project Management</li><li>Procurement Management</li><li>Site Operations</li><li>Business Automation</li></ul></div>
        </div>
      </article>
      <article class="icard spot reveal" style="--d:0.06s">
        <div class="icard__art il on-dark"><svg class="il-svg" viewBox="0 0 480 300" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="in-healthcare-3" width="16" height="16" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern><linearGradient id="in-healthcareg-4" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#46cf8a"/><stop offset="1" stop-color="#239a5c"/></linearGradient></defs><rect class="pn" width="480" height="300"/><rect width="480" height="300" fill="url(#in-healthcare-3)"/><rect class="c" x="40" y="84" width="150" height="180" rx="16" /><rect class="g" x="96" y="48" width="38" height="38" rx="10" /><path class="tick" d="M115 56v22M104 67h22" stroke-width="4"/><rect class="gs" x="58" y="112" width="50" height="30" rx="7" /><rect class="k" x="124" y="112" width="50" height="30" rx="7" /><rect class="k" x="58" y="156" width="50" height="30" rx="7" /><rect class="gs" x="124" y="156" width="50" height="30" rx="7" /><rect class="gs" x="58" y="200" width="50" height="30" rx="7" /><rect class="k" x="124" y="200" width="50" height="30" rx="7" /><rect class="g2" x="105" y="232" width="30" height="32" rx="6" /><g class="float" style="--d:0.1s"><rect class="c" x="220" y="34" width="240" height="96" rx="16" /><path class="gl heartline" d="M236 96 H282 L296 66 L318 110 L336 78 L350 96 H440" pathLength="1"/><text class="lbl" x="236" y="56" text-anchor="start" >Patients</text><rect class="gs" x="398" y="42" width="46" height="18" rx="9" /></g><g class="float hovlift" style="--d:0.5s"><rect class="c" x="220" y="146" width="116" height="120" rx="16" /><text class="lbl sm" x="236" y="170" text-anchor="start" >Appointments</text><rect class="k" x="236" y="184" width="22" height="20" rx="6" /><rect class="g" x="264" y="184" width="22" height="20" rx="6" /><rect class="k" x="292" y="184" width="22" height="20" rx="6" /><rect class="k" x="236" y="210" width="22" height="20" rx="6" /><rect class="g" x="264" y="210" width="22" height="20" rx="6" /><rect class="k" x="292" y="210" width="22" height="20" rx="6" /><rect class="k2" x="236" y="244" width="70" height="6" rx="3.0" /></g><g class="float hovlift" style="--d:0.9s"><rect class="c" x="350" y="146" width="110" height="120" rx="16" /><text class="lbl sm" x="364" y="170" text-anchor="start" >Branches</text><circle class="g" cx="380" cy="210" r="6" /><circle class="g" cx="430" cy="196" r="6" /><circle class="g" cx="412" cy="240" r="6" /><path class="ln" d="M380 210 L430 196 L412 240 Z" /><circle class="k2" cx="412" cy="214" r="3.5" /></g></svg></div>
        <div class="icard__body">
          <span class="icard__k">02 — Healthcare</span>
          <h3>Build Better Connected Healthcare Operations.</h3>
          <p>Simplify clinic and hospital operations with digital systems that connect patients, appointments, branches, teams and business workflows.</p>
          <div class="sol"><b>Solutions:</b><ul class="tags tags--dark"><li>Healthcare Management Systems</li><li>Clinic Management</li><li>Patient Workflows</li><li>Appointment Management</li><li>Automation</li></ul></div>
        </div>
      </article>
      <article class="icard spot reveal" style="--d:0.12s">
        <div class="icard__art il on-dark"><svg class="il-svg" viewBox="0 0 480 300" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="in-ecommerce-5" width="16" height="16" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern><linearGradient id="in-ecommerceg-6" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#46cf8a"/><stop offset="1" stop-color="#239a5c"/></linearGradient></defs><rect class="pn" width="480" height="300"/><rect width="480" height="300" fill="url(#in-ecommerce-5)"/><rect class="c" x="40" y="40" width="250" height="224" rx="18" /><circle class="k2" cx="60" cy="58" r="4" /><circle class="k2" cx="73" cy="58" r="4" /><circle class="k2" cx="86" cy="58" r="4" /><rect class="k" x="106" y="51" width="130" height="14" rx="7" /><rect x="56" y="78" width="218" height="54" rx="10" fill="#d9f5e6" class="mintfill"/><rect class="g" x="70" y="92" width="90" height="8" rx="4.0" /><rect class="k2" x="70" y="108" width="60" height="6" rx="3.0" /><g class="float hovlift" style="--d:0.0s"><rect class="c2" x="56" y="144" width="66" height="106" rx="10" /><rect class="gs" x="62" y="150" width="54" height="46" rx="7" /><rect class="k" x="62" y="204" width="40" height="6" rx="3.0" /><rect class="g" x="62" y="222" width="28" height="14" rx="7" /></g><g class="float hovlift" style="--d:0.4s"><rect class="c2" x="130" y="144" width="66" height="106" rx="10" /><rect class="gs" x="136" y="150" width="54" height="46" rx="7" /><rect class="k" x="136" y="204" width="40" height="6" rx="3.0" /><rect class="g" x="136" y="222" width="28" height="14" rx="7" /></g><g class="float hovlift" style="--d:0.8s"><rect class="c2" x="204" y="144" width="66" height="106" rx="10" /><rect class="gs" x="210" y="150" width="54" height="46" rx="7" /><rect class="k" x="210" y="204" width="40" height="6" rx="3.0" /><rect class="g" x="210" y="222" width="28" height="14" rx="7" /></g><g class="float" style="--d:0.3s"><rect class="c" x="310" y="40" width="150" height="100" rx="16" /><text class="lbl" x="326" y="66" text-anchor="start" >Orders</text><rect class="g" x="326" y="78" width="96" height="6" rx="3.0" /><rect class="k2" x="326" y="94" width="86" height="6" rx="3.0" /><rect class="k2" x="326" y="110" width="76" height="6" rx="3.0" /></g><g class="float" style="--d:0.8s"><rect class="c" x="310" y="156" width="150" height="108" rx="16" /><text class="lbl" x="326" y="182" text-anchor="start" >Conversion</text><path d="M326 238 L360 222 L394 228 L430 200 L444 192" class="gl draw" pathLength="1"/><circle class="g" cx="444" cy="192" r="4" /></g></svg></div>
        <div class="icard__body">
          <span class="icard__k">03 — Ecommerce & D2C</span>
          <h3>Create Commerce Experiences Built for Growth.</h3>
          <p>Build high-performing ecommerce platforms that connect storefronts, products, orders, inventory, customer experiences and marketing.</p>
          <div class="sol"><b>Solutions:</b><ul class="tags tags--dark"><li>Shopify Development</li><li>Custom Ecommerce</li><li>Conversion Optimization</li><li>Ecommerce Integrations</li><li>Performance Marketing</li></ul></div>
        </div>
      </article>
      <article class="icard spot reveal" style="--d:0.0s">
        <div class="icard__art il on-dark"><svg class="il-svg" viewBox="0 0 480 300" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="in-jewellery-7" width="16" height="16" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern><linearGradient id="in-jewelleryg-8" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#46cf8a"/><stop offset="1" stop-color="#239a5c"/></linearGradient></defs><rect class="pn" width="480" height="300"/><rect width="480" height="300" fill="url(#in-jewellery-7)"/><g class="float" style="--d:0s"><path d="M110 70 H210 L250 118 L160 220 L70 118 Z" class="gem"/><path d="M70 118 H250 M110 70 L128 118 L160 220 M210 70 L192 118 L160 220 M128 118 L160 70 L192 118" class="ln gemln"/><path d="M110 70 H210 L250 118 H70Z" class="g" opacity=".75"/></g><g class="sparkle"><path d="M258 62 l4 12 l12 4 l-12 4 l-4 12 l-4 -12 l-12 -4 l12 -4z" class="g"/></g><g class="float hovlift" style="--d:0.3s"><rect class="c" x="300" y="34" width="160" height="96" rx="16" /><text class="lbl" x="316" y="60" text-anchor="start" >Inventory</text><rect class="gs" x="316" y="74" width="28" height="40" rx="6" /><rect class="gs" x="352" y="74" width="28" height="40" rx="6" /><rect class="g" x="388" y="74" width="28" height="40" rx="6" /><rect class="gs" x="424" y="74" width="28" height="40" rx="6" /></g><g class="float hovlift" style="--d:0.6s"><rect class="c" x="300" y="146" width="160" height="56" rx="16" /><circle class="gs" cx="326" cy="174" r="13" /><svg x="318" y="166" width="16" height="16" style="color:var(--green)" ><use href="#i-users"/></svg><rect class="k" x="348" y="166" width="80" height="6" rx="3.0" /><rect class="k2" x="348" y="180" width="54" height="6" rx="3.0" /></g><g class="float" style="--d:0.9s"><rect class="c" x="300" y="214" width="160" height="56" rx="16" /><path d="M316 252 L346 238 L376 246 L410 226 L444 230" class="gl draw" pathLength="1"/></g><g class="float" style="--d:0.5s"><rect class="c" x="40" y="238" width="150" height="36" rx="12" /><rect class="k2" x="54" y="252" width="100" height="8" rx="4.0" /><rect class="g" x="160" y="246" width="22" height="20" rx="6" /></g></svg></div>
        <div class="icard__body">
          <span class="icard__k">04 — Jewellery & Retail</span>
          <h3>Connect Your Retail Business from Inventory to Customer.</h3>
          <p>Improve retail operations through connected digital commerce, inventory visibility, customer management and business automation.</p>
          <div class="sol"><b>Solutions:</b><ul class="tags tags--dark"><li>Retail Management</li><li>Inventory Systems</li><li>Shopify</li><li>Customer Management</li><li>Automation</li></ul></div>
        </div>
      </article>
      <article class="icard spot reveal" style="--d:0.06s">
        <div class="icard__art il on-dark"><svg class="il-svg" viewBox="0 0 480 300" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="in-manufacturing-9" width="16" height="16" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern><linearGradient id="in-manufacturingg-10" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#46cf8a"/><stop offset="1" stop-color="#239a5c"/></linearGradient></defs><rect class="pn" width="480" height="300"/><rect width="480" height="300" fill="url(#in-manufacturing-9)"/><path class="ln" d="M30 244 H450" stroke-width="2"/><rect class="c" x="40" y="108" width="160" height="136" rx="8" /><path class="c" d="M40 108 l40 -26 v26 l40 -26 v26 l40 -26 v26"/><rect class="k2" x="160" y="54" width="26" height="54" rx="4" /><rect class="k2" x="120" y="76" width="22" height="32" rx="4" /><rect class="k" x="58" y="138" width="22" height="26" rx="5" /><rect class="gs" x="92" y="138" width="22" height="26" rx="5" /><rect class="k" x="126" y="138" width="22" height="26" rx="5" /><rect class="gs" x="160" y="138" width="22" height="26" rx="5" /><rect class="g2" x="90" y="196" width="36" height="48" rx="4" /><circle class="smoke" cx="173" cy="40" r="8"/><circle class="smoke" cx="182" cy="26" r="6" style="--d:1s"/><rect class="c" x="222" y="206" width="238" height="22" rx="11" /><rect class="g belt" x="236" y="176" width="30" height="26" rx="5" style="--d:0.0s"/><rect class="g belt" x="286" y="176" width="30" height="26" rx="5" style="--d:0.5s"/><rect class="g belt" x="336" y="176" width="30" height="26" rx="5" style="--d:1.0s"/><rect class="g belt" x="386" y="176" width="30" height="26" rx="5" style="--d:1.5s"/><circle class="k2" cx="240" cy="217" r="6" /><circle class="k2" cx="330" cy="217" r="6" /><circle class="k2" cx="420" cy="217" r="6" /><g class="float hovlift" style="--d:0.2s"><rect class="c" x="222" y="44" width="114" height="110" rx="16" /><text class="lbl sm" x="238" y="70" text-anchor="start" >Inventory</text><rect class="g rise" x="238" y="120" width="12" height="20" rx="3" style="--d:0.0s;opacity:0.4"/><rect class="g rise" x="256" y="104" width="12" height="36" rx="3" style="--d:0.12s;opacity:0.5"/><rect class="g rise" x="274" y="112" width="12" height="28" rx="3" style="--d:0.24s;opacity:0.6000000000000001"/><rect class="g rise" x="292" y="92" width="12" height="48" rx="3" style="--d:0.36s;opacity:0.7000000000000001"/><rect class="g rise" x="310" y="106" width="12" height="34" rx="3" style="--d:0.48s;opacity:0.8"/></g><g class="float hovlift" style="--d:0.7s"><rect class="c" x="350" y="44" width="110" height="110" rx="16" /><text class="lbl sm" x="366" y="70" text-anchor="start" >Vendors</text><circle class="gs" cx="380" cy="112" r="8" /><circle class="gs" cx="424" cy="96" r="8" /><circle class="gs" cx="424" cy="130" r="8" /><path class="ln" d="M388 112 L416 98 M388 112 L416 128" /><circle class="g" cx="380" cy="112" r="4" /></g></svg></div>
        <div class="icard__body">
          <span class="icard__k">05 — Manufacturing & Distribution</span>
          <h3>Connect Production, Procurement and Business Operations.</h3>
          <p>Improve operational visibility by connecting inventory, purchasing, production workflows, vendors and business processes.</p>
          <div class="sol"><b>Solutions:</b><ul class="tags tags--dark"><li>Manufacturing ERP</li><li>Inventory Management</li><li>Procurement</li><li>Vendor Management</li><li>Operations Automation</li></ul></div>
        </div>
      </article>
      <article class="icard spot reveal" style="--d:0.12s">
        <div class="icard__art il on-dark"><svg class="il-svg" viewBox="0 0 480 300" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="in-professional" width="16" height="16" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1" class="dotp"/></pattern><linearGradient id="in-professionalg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#46cf8a"/><stop offset="1" stop-color="#239a5c"/></linearGradient></defs><rect class="pn" width="480" height="300"/><rect width="480" height="300" fill="url(#in-professional)"/><g class="float" style="--d:0.0s"><rect class="c" x="40" y="40" width="128" height="220" rx="16" /><text class="lbl" x="54" y="66" text-anchor="start" >Lead</text><rect class="c2" x="52" y="80" width="104" height="44" rx="10" /><rect class="k" x="62" y="92" width="60" height="6" rx="3.0" /><rect class="k2" x="62" y="106" width="40" height="5" rx="2.5" /><rect class="c2" x="52" y="134" width="104" height="44" rx="10" /><rect class="k" x="62" y="146" width="70" height="6" rx="3.0" /><rect class="k2" x="62" y="160" width="44" height="5" rx="2.5" /></g><g class="float" style="--d:0.4s"><rect class="c" x="176" y="40" width="128" height="220" rx="16" /><text class="lbl" x="190" y="66" text-anchor="start" >Proposal</text><rect class="c2" x="188" y="80" width="104" height="44" rx="10" /><rect class="k" x="198" y="92" width="60" height="6" rx="3.0" /><rect class="k2" x="198" y="106" width="40" height="5" rx="2.5" /><rect class="c2" x="188" y="134" width="104" height="44" rx="10" /><rect class="k" x="198" y="146" width="70" height="6" rx="3.0" /><rect class="k2" x="198" y="160" width="44" height="5" rx="2.5" /><rect class="gs" x="188" y="188" width="104" height="44" rx="10" /><rect class="g" x="198" y="200" width="52" height="6" rx="3.0" /></g><g class="float" style="--d:0.8s"><rect class="c" x="312" y="40" width="128" height="220" rx="16" /><text class="lbl" x="326" y="66" text-anchor="start" >Delivery</text><rect class="c2" x="324" y="80" width="104" height="44" rx="10" /><rect class="k" x="334" y="92" width="60" height="6" rx="3.0" /><rect class="k2" x="334" y="106" width="40" height="5" rx="2.5" /></g><g class="drift"><rect class="c g-outline" x="52" y="134" width="104" height="44" rx="10" /><rect class="g" x="62" y="146" width="70" height="6" rx="3.0" /></g><g class="float" style="--d:0.9s"><rect class="c" x="342" y="144" width="118" height="116" rx="16" /><path d="M356 232 L384 214 L410 220 L440 188" class="gl draw" pathLength="1"/><circle class="g" cx="440" cy="188" r="4" /></g></svg></div>
        <div class="icard__body">
          <span class="icard__k">06 — Professional Services</span>
          <h3>Build More Structured and Efficient Service Businesses.</h3>
          <p>Connect customer acquisition, sales pipelines, projects, teams, delivery workflows and business performance.</p>
          <div class="sol"><b>Solutions:</b><ul class="tags tags--dark"><li>CRM</li><li>Project Management</li><li>Business Automation</li><li>Workflow Management</li><li>Business Intelligence</li></ul></div>
        </div>
      </article></div>
    <div class="bcta reveal">
      <div><h3>Your Industry. Your Challenges.<br>Our Engineering.</h3><p>Let's understand your business and identify the technology solutions that will move it forward.</p></div>
      <a class="h10-btn h10-btn--green" href="/book" data-book><span>Discuss Your Industry Requirements</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
    </div>
  </div>
</section>
<section class="test" id="testimonials">
  <div class="wrap">
    <div class="center">
      <span class="pill reveal"><span class="pill__dot" aria-hidden="true"></span>Client Experiences</span>
      <h2 class="h2 reveal" style="--d:.06s">Built on Trust.<br>Strengthened by Results.</h2>
      <p class="lede reveal" style="--d:.1s">Great technology is built through great partnerships. We work closely with our clients to understand their business, solve complex challenges and deliver solutions that create lasting value.</p>
      <p class="lede reveal" style="--d:.14s">Here's what our clients have to say about working with Drawlead.</p>
    </div>
    <div class="tgrid">
      <figure class="tcard spot reveal" style="--d:0.0s" data-testimonial-slot="1">
        <span class="tcard__q"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-quote"/></svg></span>
        <figcaption class="tcard__who"><span class="tcard__av" aria-hidden="true"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-users"/></svg></span><b>[Client Name / Business Name]</b></figcaption>
        <blockquote>“[Insert the client's actual feedback about their experience working with Drawlead.]”</blockquote>
        <p class="tcard__by">— [Client Name], [Designation]</p>
      </figure>
      <figure class="tcard spot reveal" style="--d:0.08s" data-testimonial-slot="2">
        <span class="tcard__q"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-quote"/></svg></span>
        <figcaption class="tcard__who"><span class="tcard__av" aria-hidden="true"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-users"/></svg></span><b>[Client Name / Business Name]</b></figcaption>
        <blockquote>“[Insert genuine feedback about the solution, collaboration or project delivery.]”</blockquote>
        <p class="tcard__by">— [Client Name], [Designation]</p>
      </figure>
      <figure class="tcard spot reveal" style="--d:0.16s" data-testimonial-slot="3">
        <span class="tcard__q"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-quote"/></svg></span>
        <figcaption class="tcard__who"><span class="tcard__av" aria-hidden="true"><svg class="ic" aria-hidden="true" focusable="false"><use href="#i-users"/></svg></span><b>[Client Name / Business Name]</b></figcaption>
        <blockquote>“[Insert genuine feedback about the business impact or overall working relationship.]”</blockquote>
        <p class="tcard__by">— [Client Name], [Designation]</p>
      </figure></div>
    <div class="bcta bcta--light reveal">
      <div><h3>Your Business Could Be Our Next Success Story.</h3><p>Let's build something meaningful together.</p></div>
      <a class="h10-btn h10-btn--dark" href="/book" data-book><span>Start a Conversation</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a>
    </div>
  </div>
</section>
<section class="final" id="contact">
  <div class="final__bg" aria-hidden="true"><i class="orb o1"></i><i class="orb o2"></i><i class="grid"></i></div>
  <div class="wrap final__in">
    <span class="pill pill--mint reveal"><span class="pill__dot" aria-hidden="true"></span>Let's Build Something Better</span>
    <h2 class="h1x reveal" style="--d:.06s">Your Next Business Transformation <span class="grn">Starts Here.</span></h2>
    <p class="lede lede--light reveal" style="--d:.1s">Whether you're looking to build a custom business system, develop a high-performing ecommerce platform, implement AI-powered automation or accelerate digital growth, we're ready to understand your requirements and engineer the right solution.</p>
    <p class="final__line reveal" style="--d:.14s">Let's turn your business challenges into technology opportunities.</p>
    <div class="final__ctas reveal" style="--d:.18s"><a class="h10-btn h10-btn--green" href="/book" data-book><span>Book a Consultation</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a><a class="h10-btn h10-btn--ghost" href="#solutions"><span>Explore Our Services</span><svg class="ic h10-btn__arr" aria-hidden="true" focusable="false"><use href="#i-arrow"/></svg></a></div>
  </div>
</section>
</main><footer class="foot">
  <div class="wrap foot__in">
    <a href="#top" aria-label="Drawlead – back to top"><img src="/assets/home10/logos/drawlead-white.webp" alt="Drawlead" width="153" height="30" loading="lazy"></a>
    <p>Business Systems. Digital Commerce. Intelligent Growth.</p>
    <nav aria-label="Footer"><a href="#solutions">Solutions</a><a href="#program">8-Week Program</a><a href="/case-studies">Case Studies</a><a href="#industry-focus">Industries</a><a href="/about-us">About Us</a><a href="/blog">Blog</a></nav>
  </div>
  <div class="wrap foot__base">
    <span>official@drawlead.co.in &nbsp;·&nbsp; www.drawlead.co.in &nbsp;·&nbsp; Chennai, Tamil Nadu, IN</span>
    <span>© <?= date('Y') ?> Drawlead. All rights reserved.</span>
  </div>
</footer>
</div>

<script src="<?= asset_url('/assets/home10.js') ?>" defer></script>
