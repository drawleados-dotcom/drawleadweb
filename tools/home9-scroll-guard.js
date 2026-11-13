<script>
// Home 9: keep GSAP ScrollTrigger's pin positions true to the page.
//
// ScrollTrigger works out where each pin (the closing CTA's letter train,
// #cta-intro) starts and ends when it refreshes: on load and, debounced, on
// resize. This page also has scripts that change section heights themselves
// when the window changes (Solutions switching its pinned mode at 640px tall,
// Case Studies' and Industries' runways, which follow the window size). When
// one of those settles after ScrollTrigger has refreshed, the pin keeps the
// old positions and fires early, laying the white letter train over the
// sections above it (seen at 125% display scaling when DevTools opened and
// took the window under 640px tall).
//
// So: watch the page's height, and whenever it differs from what it was at
// ScrollTrigger's last refresh, refresh again (debounced). A refresh that
// changes nothing leaves the height as it was, so this cannot loop.
// tools/sync-home9.pl places this after the last section.
(function(){
 if(!('ResizeObserver' in window)) return;
 const doc = document.documentElement;
 let lastH = -1, timer = 0, started = false;
 function start(){
  const ST = window.ScrollTrigger;
  if(started || !ST) return;
  started = true;
  lastH = doc.scrollHeight;
  ST.addEventListener('refresh', function(){ lastH = doc.scrollHeight; });
  new ResizeObserver(function(){
   if(doc.scrollHeight === lastH) return;
   clearTimeout(timer);
   timer = setTimeout(function(){ if(doc.scrollHeight !== lastH) ST.refresh(); }, 250);
  }).observe(document.body);
 }
 if(document.readyState === 'complete') start();
 else window.addEventListener('load', start);
})();
</script>
