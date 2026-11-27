// Business Systems landing page (templates/business-systems-body.php).
// Runs alongside /assets/home10.js, which already handles reveals, the
// word-by-word headings, the booking buttons and the footer.
//  - the hero "Business OS" board: a rotating activity feed and module
//    counters that tick up now and then, only while it is on screen
//  - the approach timeline: the rail fills and each step lights up as it
//    reaches the middle of the screen
// Reduced motion: the board stays still and every step shows as reached.
(function () {
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hasIO = 'IntersectionObserver' in window;

  /* ---------- hero board ---------- */
  var board = document.querySelector('[data-bs-board]');
  if (board && !reduce) {
    var feed = board.querySelector('[data-bs-feed]');
    var ticks = Array.prototype.slice.call(board.querySelectorAll('[data-bs-tick]'));
    var lines = [
      'New lead synced to CRM · Sales',
      'Purchase order approved · Procurement',
      'Site update logged · Projects',
      'Invoice paid · Finance',
      'Attendance marked · HR',
      'Low stock alert raised · Inventory',
      'Follow-up scheduled · CRM',
      'Weekly report ready · Management'
    ];
    var li = 0, timer = null;
    var step = function () {
      if (feed) {
        feed.classList.add('is-swap');
        setTimeout(function () {
          li = (li + 1) % lines.length;
          feed.textContent = lines[li];
          feed.classList.remove('is-swap');
        }, 350);
      }
      if (ticks.length && Math.random() < .7) {
        var el = ticks[Math.floor(Math.random() * ticks.length)];
        var n = parseInt(el.textContent, 10);
        if (!isNaN(n)) el.textContent = n + 1;
      }
    };
    var start = function () { if (!timer) timer = setInterval(step, 2600); };
    var stop = function () { clearInterval(timer); timer = null; };
    if (hasIO) {
      new IntersectionObserver(function (es) {
        if (es[0].isIntersecting && !document.hidden) start(); else stop();
      }, { threshold: .2 }).observe(board);
    } else start();
    document.addEventListener('visibilitychange', function () { if (document.hidden) stop(); });
  }

  /* ---------- approach timeline ---------- */
  var list = document.querySelector('[data-bs-steps]');
  if (list) {
    var steps = Array.prototype.slice.call(list.querySelectorAll('.bsa__step'));
    var rail = list.querySelector('.bsa__rail');
    if (reduce) {
      steps.forEach(function (s) { s.classList.add('is-on'); });
      if (rail) rail.style.setProperty('--p', 1);
      return;
    }
    var raf = 0;
    var update = function () {
      raf = 0;
      var mid = window.innerHeight * .55;
      var r = list.getBoundingClientRect();
      var p = Math.max(0, Math.min(1, (mid - r.top) / (r.height || 1)));
      if (rail) rail.style.setProperty('--p', p.toFixed(3));
      steps.forEach(function (s) {
        var n = s.querySelector('.bsa__n').getBoundingClientRect();
        s.classList.toggle('is-on', n.top + n.height / 2 <= mid);
      });
    };
    var queue = function () { if (!raf) raf = requestAnimationFrame(update); };
    addEventListener('scroll', queue, { passive: true });
    addEventListener('resize', queue);
    update();
  }
})();
