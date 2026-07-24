// Drawlead — site-wide "on open" consultation popup.
// Shows once per browser session, a short moment after the page loads.
(function () {
  var popup = document.getElementById('site-popup');
  if (!popup) return;

  var SESSION_KEY = 'drawleadPopupShown';

  function openPopup() {
    popup.classList.add('open');
    popup.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closePopup() {
    popup.classList.remove('open');
    popup.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  if (!sessionStorage.getItem(SESSION_KEY)) {
    sessionStorage.setItem(SESSION_KEY, '1');
    window.setTimeout(openPopup, 1400);
  }

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
