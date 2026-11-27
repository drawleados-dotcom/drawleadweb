// Drawlead — site-wide "on open" consultation popup.
// When it's allowed to show (frequency) and what makes it show (moment)
// are both admin-configurable per popup in Admin → Popups — see the
// data-trigger-* attributes on #site-popup. The "corner" layout is a small
// slide-in card with no overlay, so it leaves page scrolling alone.
(function () {
  var popup = document.getElementById('site-popup');
  if (!popup) return;

  var triggerDelay = popup.dataset.triggerDelay === '1';
  var triggerNewPage = popup.dataset.triggerNewPage === '1';
  var triggerRefresh = popup.dataset.triggerRefresh === '1';
  var triggerScrollSection = popup.dataset.triggerScrollSection === '1';

  var isCorner = popup.dataset.layout === 'corner';
  // One key per popup, so a different popup on another page still gets
  // its own once-per-session showing. id 0 = the pre-migration popup.
  var popupId = popup.dataset.popupId || '0';
  var SESSION_KEY = popupId === '0' ? 'drawleadPopupShown' : 'drawleadPopupShown-' + popupId;
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
    if (!isCorner) {
      document.body.style.overflow = 'hidden';
      // move focus into the dialog so keyboard and screen-reader users land in it
      lastFocus = document.activeElement;
      var firstBtn = popup.querySelector('.site-popup-close');
      if (firstBtn) firstBtn.focus({ preventScroll: true });
    }
  }

  function closePopup() {
    popup.classList.remove('open');
    popup.setAttribute('aria-hidden', 'true');
    if (!isCorner) {
      document.body.style.overflow = '';
      if (lastFocus && lastFocus.focus) lastFocus.focus({ preventScroll: true });
      lastFocus = null;
    }
  }

  // keep Tab inside the modal while it is open
  var lastFocus = null;
  popup.addEventListener('keydown', function (e) {
    if (e.key !== 'Tab' || isCorner || !popup.classList.contains('open')) return;
    var items = popup.querySelectorAll('.site-popup-dialog button, .site-popup-dialog a[href]');
    if (!items.length) return;
    var first = items[0], last = items[items.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  });

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
