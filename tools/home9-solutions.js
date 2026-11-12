 <script>
 // Home 9: Solutions as a horizontal accordion.
 //
 // The three cards stand in a row; one is open and the other two are narrow
 // spines showing their number and name. Hovering a closed card opens it and
 // closes the open one; keyboard focus and a tap do the same, so every card is
 // reachable without a mouse. The last card opened stays open.
 //
 // Only from 1200px up: below that there is not room for one open card plus two
 // spines, and the cards simply stack. The layout is CSS (assets/home9.css,
 // .sol-acc); this script only marks the open card, builds the spines from each
 // card's own name, and switches .sol-acc on and off with the breakpoint.
 // sync-home9.pl removes Home 8's sticky-stack driver for these cards.
 (function(){
  const grid = document.querySelector('#solutions .sol-grid');
  if(!grid) return;
  const cards = Array.from(grid.children).filter(el => el.classList.contains('sol-card'));
  if(cards.length < 2) return;

  const wide = window.matchMedia('(min-width:1200px)');

  // Spine: what a closed card shows. Taken from the card's own title so the
  // copy lives in one place; hidden from screen readers, which read the card.
  cards.forEach((card, i) => {
   const name = card.querySelector('.sol-name');
   const spine = document.createElement('span');
   spine.className = 'sol-spine';
   spine.setAttribute('aria-hidden', 'true');
   const num = document.createElement('i');
   num.textContent = String(i + 1).padStart(2, '0');
   const label = document.createElement('b');
   label.textContent = name ? name.textContent.trim() : '';
   spine.append(num, label);
   card.appendChild(spine);
  });

  let open = 0, pending = 0;
  function show(i){
   open = i;
   cards.forEach((c, k) => c.classList.toggle('is-open', k === i));
  }

  cards.forEach((card, i) => {
   // A short hover intent, so sweeping the pointer across the row on the way
   // somewhere else does not flick every card open in turn.
   card.addEventListener('mouseenter', () => {
    if(!wide.matches || i === open) return;
    clearTimeout(pending);
    pending = setTimeout(() => show(i), 90);
   });
   card.addEventListener('mouseleave', () => clearTimeout(pending));
   card.addEventListener('focusin', () => { if(wide.matches) show(i); });
   card.addEventListener('click', () => { if(wide.matches && i !== open) show(i); });
  });

  function sync(){ grid.classList.toggle('sol-acc', wide.matches); }
  show(0);
  sync();
  if(wide.addEventListener) wide.addEventListener('change', sync);
  else if(wide.addListener) wide.addListener(sync);
 })();
 </script>
