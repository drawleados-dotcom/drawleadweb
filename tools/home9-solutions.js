 <script>
 // Home 9: Solutions' sidebar nav.
 //
 // The sidebar (eyebrow, heading, description, one nav entry per card and a
 // consultation card) stays put beside the column of cards while it scrolls;
 // the layout is CSS (assets/home9.css, section 5) and sync-home9.pl builds
 // the nav from the cards. This script marks the card being read, and its
 // nav entry, as .is-active: the last card whose top has passed a line 40%
 // down the screen, so the entry changes as each card takes over the middle
 // of the view. A nav click scrolls its card into view without putting a
 // hash in the address bar (the link's href still works without script).
 // sync-home9.pl removes Home 8's sticky-stack driver for these cards.
 (function(){
  const sec = document.getElementById('solutions');
  if(!sec) return;
  const links = Array.from(sec.querySelectorAll('.sol-nav-item'));
  const cards = links.map(a => document.getElementById(a.getAttribute('href').slice(1)));
  if(!links.length || cards.some(c => !c)) return;

  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)');
  let active = -1, raf = 0;

  function set(i){
   if(i === active) return;
   active = i;
   links.forEach((a, k) => {
    a.classList.toggle('is-active', k === i);
    if(k === i) a.setAttribute('aria-current', 'true');
    else a.removeAttribute('aria-current');
   });
   cards.forEach((c, k) => c.classList.toggle('is-active', k === i));
  }
  function update(){
   raf = 0;
   const line = window.innerHeight * 0.4;
   let i = 0;
   cards.forEach((c, k) => { if(c.getBoundingClientRect().top <= line) i = k; });
   set(i);
  }
  function queue(){ if(!raf) raf = requestAnimationFrame(update); }

  links.forEach((a, k) => {
   a.addEventListener('click', e => {
    e.preventDefault();
    cards[k].scrollIntoView({ behavior: reduce.matches ? 'auto' : 'smooth', block: 'start' });
   });
  });
  window.addEventListener('scroll', queue, { passive: true });
  window.addEventListener('resize', queue);
  update();
 })();
 </script>
