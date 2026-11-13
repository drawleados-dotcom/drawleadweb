<script>
// Home 9: keep GSAP ScrollTrigger's pin (the closing CTA's letter train,
// #cta-intro) where it belongs.
//
// 1. Refresh without smooth scrolling. To measure, ScrollTrigger.refresh()
//    moves the page's scroll position and puts it back; html has
//    scroll-behavior:smooth (home7.css), which turns those jumps into
//    animations, so a refresh run while scrolled down the page measured
//    mid-animation and set the pin's start thousands of pixels early (seen
//    at 6723 and 2523 against a real 16300), laying the train over How We
//    Work and Platform Dashboards. Smooth scrolling is switched off for the
//    length of each refresh (refreshInit .. refresh) and back on after, so
//    in-page links still glide. The
//    switch is read back at once (getComputedStyle), or Chrome keeps the old
//    smooth value for the jumps that follow. Registered as soon as the page has parsed,
//    before the train's ScrollTrigger exists, so every refresh is covered.
// 2. Refresh when the page's height changes. This page's own scripts change
//    section heights after ScrollTrigger has measured (Solutions switching
//    its pinned mode at 640px tall, Case Studies' and Industries' runways
//    following the window, the hero's headline rotating between one height
//    and another), which would leave the pin's positions stale. So the
//    page's height is watched, and when it differs from the height at the
//    last refresh, ScrollTrigger refreshes (debounced). A refresh that
//    changes nothing leaves the height as it was, so this cannot loop.
// tools/sync-home9.pl places this after the last section.
(function(){
 const html = document.documentElement;
 let lastH = -1, timer = 0, started = false;
 function start(){
  const ST = window.ScrollTrigger;
  if(started || !ST) return;
  started = true;
  lastH = html.scrollHeight;
  // Reading the computed value makes the browser apply it at once: without
  // that, Chrome still scrolled smoothly for the jumps that follow.
  ST.addEventListener('refreshInit', function(){ html.style.scrollBehavior = 'auto'; void getComputedStyle(html).scrollBehavior; });
  ST.addEventListener('refresh', function(){ html.style.scrollBehavior = ''; lastH = html.scrollHeight; });
  if(!('ResizeObserver' in window)) return;
  new ResizeObserver(function(){
   if(html.scrollHeight === lastH) return;
   clearTimeout(timer);
   timer = setTimeout(function(){ if(html.scrollHeight !== lastH) ST.refresh(); }, 250);
  }).observe(document.body);
 }
 // GSAP loads further down the page as ordinary scripts, so it is there by
 // DOMContentLoaded; load is the fallback.
 if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
 else start();
 window.addEventListener('load', start);
})();
</script>
