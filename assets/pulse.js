// Built-in website analytics: counts this page view for Admin → Analytics.
//
// Sends one small beacon to /api/pulse.php when the page opens, and one more
// with the seconds the page was in view when the visitor leaves or switches
// away. No cookies: a random visitor id and the current visit (which ends
// after 30 minutes without a page view) are kept in localStorage. The server
// stores no IP address (includes/site-stats.php). Browsers driven by
// automation (navigator.webdriver) are not counted.
(function () {
  if (navigator.webdriver || !window.JSON) return;
  var ENDPOINT = '/api/pulse.php';
  var VISIT_GAP = 30 * 60 * 1000;
  var CLICK_IDS = ['gclid', 'gbraid', 'wbraid', 'fbclid', 'msclkid', 'li_fat_id', 'ttclid'];

  function rid() {
    var b = new Uint8Array(8), s = '';
    if (window.crypto && crypto.getRandomValues) crypto.getRandomValues(b);
    else for (var i = 0; i < 8; i++) b[i] = Math.floor(Math.random() * 256);
    for (var j = 0; j < 8; j++) s += (b[j] < 16 ? '0' : '') + b[j].toString(16);
    return s;
  }
  function get(k) { try { return localStorage.getItem(k); } catch (e) { return null; } }
  function set(k, v) { try { localStorage.setItem(k, v); } catch (e) { /* private mode: counted as a new visitor */ } }
  function send(obj) {
    var body = JSON.stringify(obj);
    try {
      if (navigator.sendBeacon && navigator.sendBeacon(ENDPOINT, new Blob([body], { type: 'text/plain' }))) return;
    } catch (e) { /* fall through */ }
    try { fetch(ENDPOINT, { method: 'POST', body: body, keepalive: true, credentials: 'same-origin', headers: { 'Content-Type': 'text/plain' } }); } catch (e) { /* offline */ }
  }

  var now = Date.now();
  var visitor = get('dl_pulse_v'), isNew = !/^[a-f0-9]{16}$/.test(visitor || '');
  if (isNew) { visitor = rid(); set('dl_pulse_v', visitor); }
  var visit = null;
  try { visit = JSON.parse(get('dl_pulse_s') || 'null'); } catch (e) { visit = null; }
  var entry = !visit || !/^[a-f0-9]{16}$/.test(visit.id || '') || !(now - visit.t < VISIT_GAP);
  if (entry) visit = { id: rid(), t: now };
  function touchVisit() { visit.t = Date.now(); set('dl_pulse_s', JSON.stringify(visit)); }
  touchVisit();

  var params;
  try { params = new URLSearchParams(location.search); } catch (e) { params = { get: function () { return ''; } }; }
  var click = '';
  for (var i = 0; i < CLICK_IDS.length; i++) if (params.get(CLICK_IDS[i])) { click = CLICK_IDS[i]; break; }
  var tz = '';
  try { tz = Intl.DateTimeFormat().resolvedOptions().timeZone || ''; } catch (e) { /* old browser */ }

  var pv = rid();
  send({
    pv: pv, v: visitor, s: visit.id, n: isNew ? 1 : 0, e: entry ? 1 : 0,
    p: location.pathname.slice(0, 300), t: (document.title || '').slice(0, 200),
    r: entry ? (document.referrer || '').slice(0, 500) : '',
    utm_source: (params.get('utm_source') || '').slice(0, 100),
    utm_medium: (params.get('utm_medium') || '').slice(0, 100),
    utm_campaign: (params.get('utm_campaign') || '').slice(0, 100),
    click: click, tz: tz, l: (navigator.language || '').slice(0, 20)
  });

  // seconds the page was actually in view, sent whenever the visitor leaves it
  var shownAt = document.visibilityState === 'visible' ? Date.now() : 0, total = 0, sent = 0;
  function flush() {
    if (shownAt) { total += Date.now() - shownAt; shownAt = 0; }
    var secs = Math.round(total / 1000);
    if (secs > sent) { sent = secs; send({ pv: pv, sec: secs }); }
  }
  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'hidden') flush();
    else { shownAt = Date.now(); touchVisit(); }
  });
  window.addEventListener('pagehide', flush);
})();
