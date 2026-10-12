// Hero dashboard tilt: starts leaning back in perspective and straightens as the
// page scrolls. The scroll sets a target; each frame eases toward it so a flick of
// the wheel never snaps the angle, and the loop stops once it has caught up. The
// look itself is in home8-base.css (section 23); this only writes --dw-p.
(function(){
 const win = document.getElementById('dashWindow');
 if(!win || reduceMotion) return;

 let end = 1, target = 0, cur = 0, raf = 0;

 // Layout position (offsetTop chain), so neither the tilt nor the hero's entrance
 // animation skews the measurement.
 function measure(){
  let top = 0, el = win;
  while(el){ top += el.offsetTop; el = el.offsetParent; }
  // flat once the window's top edge reaches ~20% down the viewport
  end = Math.max(240, top - window.innerHeight * 0.2);
 }

 function read(){ target = Math.min(1, Math.max(0, window.scrollY / end)); }

 function paint(){
  win.style.setProperty('--dw-p', cur.toFixed(4));
  win.classList.toggle('dw-flat', cur >= 1);
 }

 function tick(){
  cur += (target - cur) * 0.12;
  if(Math.abs(target - cur) < 0.0005) cur = target;
  paint();
  raf = cur === target ? 0 : requestAnimationFrame(tick);
 }

 function onScroll(){ read(); if(!raf) raf = requestAnimationFrame(tick); }

 measure(); read(); cur = target; paint();
 win.classList.add('dw-tilt');
 window.addEventListener('scroll', onScroll, { passive: true });
 window.addEventListener('resize', function(){ measure(); onScroll(); });
 window.addEventListener('load', function(){ measure(); onScroll(); });
})();

