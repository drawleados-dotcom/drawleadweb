// Lead Source — remembers how this visitor reached the site, so a lead sent
// later (booking form, WhatsApp chat) can say where it came from.
//
// Two touches are kept in localStorage:
//   first — the very first visit we saw from this browser (never replaced)
//   last  — the latest visit that came from somewhere: an external
//           referrer, UTM tags or an ad click id. Moving between our own
//           pages, or coming back by typing the address, doesn't replace it
//           (the usual "last non-direct" rule). It expires after 90 days.
// booking.js / whatsapp.js call window.DLLeadSource.get(trigger) when they
// submit and send the result as `source`. includes/lead-source.php saves it.
(function () {
  var FIRST_KEY = 'dl_ls_first';
  var LAST_KEY = 'dl_ls_last';
  var MAX_AGE = 90 * 24 * 60 * 60 * 1000;
  var UTM = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
  var CLICK_IDS = ['gclid', 'gbraid', 'wbraid', 'fbclid', 'msclkid', 'li_fat_id', 'ttclid'];
  // This site's old address. admin.drawlead.com served it until drawlead.com
  // went live (2026-11-27) and now redirects here, so a tab still open on it
  // hands over admin.drawlead.com as the referrer. That's us, not a referral.
  // Keep in step with LEAD_SOURCE_OWN_HOSTS in includes/lead-source.php.
  var OWN_HOSTS = ['admin.drawlead.com'];

  function read(key) {
    try { return JSON.parse(localStorage.getItem(key) || 'null'); } catch (e) { return null; }
  }
  function write(key, val) {
    try { localStorage.setItem(key, JSON.stringify(val)); } catch (e) { /* private mode — fine */ }
  }

  function externalReferrer() {
    var ref = document.referrer || '';
    if (!ref) return '';
    try {
      var host = new URL(ref).hostname.replace(/^www\./, '');
      var self = location.hostname.replace(/^www\./, '');
      return host === self || OWN_HOSTS.indexOf(host) !== -1 ? '' : ref;
    } catch (e) { return ''; }
  }

  // The landing page without ad click ids (long, and useless to read).
  function cleanLanding() {
    try {
      var u = new URL(location.href);
      CLICK_IDS.forEach(function (k) { u.searchParams.delete(k); });
      return (u.pathname + u.search).slice(0, 500);
    } catch (e) { return location.pathname; }
  }

  var params;
  try { params = new URLSearchParams(location.search); } catch (e) { params = { get: function () { return null; } }; }

  var touch = { landing: cleanLanding(), referrer: externalReferrer(), click_id: '', ts: Date.now() };
  var hasCampaign = false;
  UTM.forEach(function (k) {
    var v = params.get(k) || '';
    touch[k] = v.slice(0, 190);
    if (v) hasCampaign = true;
  });
  for (var i = 0; i < CLICK_IDS.length; i++) {
    if (params.get(CLICK_IDS[i])) { touch.click_id = CLICK_IDS[i]; break; }
  }
  var cameFromSomewhere = hasCampaign || !!touch.click_id || !!touch.referrer;

  if (!read(FIRST_KEY)) write(FIRST_KEY, touch);
  var last = read(LAST_KEY);
  if (cameFromSomewhere || !last || !last.ts || Date.now() - last.ts > MAX_AGE) {
    write(LAST_KEY, touch);
  }

  window.DLLeadSource = {
    get: function (trigger) {
      return {
        page: (location.pathname + location.search).slice(0, 500),
        title: (document.title || '').slice(0, 255),
        trigger: (trigger || '').slice(0, 190),
        session: read(LAST_KEY) || touch,
        first: read(FIRST_KEY) || touch,
      };
    },
  };
})();
