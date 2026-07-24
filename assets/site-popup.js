// Drawlead — site-wide "on open" consultation popup.
// When it's allowed to show (frequency) and what makes it show (moment)
// are both admin-configurable from admin/popup.php — see the data-trigger-*
// attributes on #site-popup.
(function () {
  var popup = document.getElementById('site-popup');
  if (!popup) return;

  var triggerDelay = popup.dataset.triggerDelay === '1';
  var triggerNewPage = popup.dataset.triggerNewPage === '1';
  var triggerRefresh = popup.dataset.triggerRefresh === '1';
  var triggerScrollSection = popup.dataset.triggerScrollSection === '1';

  var SESSION_KEY = 'drawleadPopupShown';
  var shown = false;
  var delayTimer = null;
  var scrollObserver = null;

  function openPopup() {
    if (shown) return;
    shown = true;
    if (delayTimer) window.clearTimeout(delayTimer);
    if (scrollObserver) scrollObserver.disconnect();
    popup.classList.add('open');
    popup.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closePopup() {
    popup.classList.remove('open');
    popup.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  // ── Frequency: is this particular page load allowed to show the popup? ──
  var navEntries = window.performance && performance.getEntriesByType ? performance.getEntriesByType('navigation') : [];
  var navType = navEntries.length ? navEntries[0].type
    : (window.performance && performance.navigation && performance.navigation.type === 1 ? 'reload' : 'navigate');
  var isReload = navType === 'reload';

  var frequencyAllowed;
  if (triggerNewPage || triggerRefresh) {
    frequencyAllowed = (isReload && triggerRefresh) || (!isReload && triggerNewPage);
  } else {
    // Neither "every new page" nor "every refresh" is enabled — fall back
    // to the default: show once per browser session.
    frequencyAllowed = !sessionStorage.getItem(SESSION_KEY);
  }

  if (!frequencyAllowed) return;

  if (!(triggerNewPage || triggerRefresh)) {
    sessionStorage.setItem(SESSION_KEY, '1');
  }

  // ── Moment: what makes it show, once frequency allows this load? ──
  if (triggerDelay) {
    delayTimer = window.setTimeout(openPopup, 3000);
  }

  if (triggerScrollSection) {
    var sections = document.querySelectorAll('section');
    var target = sections[3]; // 4th section on the page
    if (target && window.IntersectionObserver) {
      scrollObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) openPopup();
        });
      }, { threshold: 0.3 });
      scrollObserver.observe(target);
    }
  }

  // ── Close handlers ──
  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-popup-close]')) {
      closePopup();
      return;
    }
    // The CTA can also open the booking popup (data-book, handled by
    // booking.js via its own delegated listener) — close this one first
    // so they don't end up stacked.
    if (e.target.closest('#site-popup [data-book]')) {
      closePopup();
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && popup.classList.contains('open')) closePopup();
  });
})();
