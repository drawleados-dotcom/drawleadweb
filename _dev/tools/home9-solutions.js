 <script>
 // Home 9: Solutions, one card at a time.
 //
 // On a laptop or desktop screen (1025px wide, 640px tall and up) the
 // section's layout (sidebar + cards) is pinned for a stretch of scrolling
 // and only one card shows: the stretch is split evenly between the cards,
 // and scrolling through it swaps each in turn, with the sidebar's entry for
 // it outlined. The pin is CSS (assets/home9.css, section 5, under
 // .sol-pinned): .sol-scroll is the track, as tall as the screen plus one
 // step per card, and .sol-layout sticks to the top of it. This script
 // switches .sol-pinned on with that media query, works out which card the
 // scroll position belongs to, and marks it .is-active (the cards before it
 // .is-past, so a card leaves upward going down the page and downward going
 // back up). Elsewhere (phones, short windows) the cards stand in a column
 // and the active one is the last whose top has passed 40% down the screen.
 // A sidebar click scrolls to its card (pinned: into that card's stretch)
 // without putting a hash in the address bar; the href works without script.
 // sync-home9.pl removes Home 8's sticky-stack driver for these cards.
 (function(){
  const sec = document.getElementById('solutions');
  if(!sec) return;
  const track = sec.querySelector('.sol-scroll');
  const stage = sec.querySelector('.sol-layout');
  const links = Array.from(sec.querySelectorAll('.sol-nav-item'));
  const cards = links.map(a => document.getElementById(a.getAttribute('href').slice(1)));
  if(!track || !stage || !links.length || cards.some(c => !c)) return;

  const wide = window.matchMedia('(min-width:1025px) and (min-height:640px)');
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)');
  const n = cards.length;
  let active = -1, raf = 0;

  const pinned = () => sec.classList.contains('sol-pinned');
  // scroll distance given to each card while pinned
  const step = () => Math.max(1, (track.offsetHeight - stage.offsetHeight) / n);

  function set(i){
   if(i === active) return;
   active = i;
   links.forEach((a, k) => {
    a.classList.toggle('is-active', k === i);
    if(k === i) a.setAttribute('aria-current', 'true');
    else a.removeAttribute('aria-current');
   });
   cards.forEach((c, k) => {
    c.classList.toggle('is-active', k === i);
    c.classList.toggle('is-past', k < i);
   });
  }
  function update(){
   raf = 0;
   let i = 0;
   if(pinned()){
    const p = -track.getBoundingClientRect().top;     // how far into the pin
    i = Math.floor(p / step());
   } else {
    const line = window.innerHeight * 0.4;
    cards.forEach((c, k) => { if(c.getBoundingClientRect().top <= line) i = k; });
   }
   set(Math.max(0, Math.min(n - 1, i)));
  }
  function queue(){ if(!raf) raf = requestAnimationFrame(update); }
  function mode(){
   sec.classList.toggle('sol-pinned', wide.matches);
   active = -1;
   update();
  }

  links.forEach((a, k) => {
   a.addEventListener('click', e => {
    e.preventDefault();
    const behavior = reduce.matches ? 'auto' : 'smooth';
    if(pinned()){
     const top = window.scrollY + track.getBoundingClientRect().top + (k + 0.3) * step();
     window.scrollTo({ top: Math.round(top), behavior });
    } else {
     cards[k].scrollIntoView({ behavior, block: 'start' });
    }
   });
  });
  window.addEventListener('scroll', queue, { passive: true });
  window.addEventListener('resize', queue);
  if(wide.addEventListener) wide.addEventListener('change', mode);
  else if(wide.addListener) wide.addListener(mode);
  mode();
 })();
 </script>
