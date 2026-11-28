// Analytics popups on admin/analytics.php.
//
// Each "View analytics" button opens one dialog that loads
// analytics-data.php?source=ga|gsc|clarity&days=N and draws what comes back:
// a live strip (GA realtime), headline numbers with the change against the
// previous period, a daily trend chart for the selected number (hover or
// arrow keys read each day, and a table view lists them), and ranked tables.
// Everything that comes from an API is inserted with textContent.
(function () {
  var dlg = document.getElementById('adDialog');
  if (!dlg || typeof dlg.showModal !== 'function') return;

  function byId(id) { return document.getElementById(id); }
  var body = byId('adBody'), titleEl = byId('adTitle'), subEl = byId('adSub'), rangeEl = byId('adRange'),
      updatedEl = byId('adUpdated'), linksEl = byId('adLinks'), refreshBtn = byId('adRefresh');
  var NAMES = { site: 'Drawlead website', ga: 'Google Analytics', gsc: 'Google Search Console', clarity: 'Microsoft Clarity' };
  var regionNames = null;
  try { regionNames = new Intl.DisplayNames(['en'], { type: 'region' }); } catch (e) { /* old browser: show the code */ }
  function countryName(code) {
    if (!code) return 'Unknown';
    try { return (regionNames && regionNames.of(code)) || code; } catch (e) { return code; }
  }
  var INK = '#14855a';
  var state = { source: '', days: {}, metric: {}, data: null, req: 0, idx: -1 };

  // ---------- small DOM helpers ----------
  function el(tag, cls, text) {
    var e = document.createElement(tag);
    if (cls) e.className = cls;
    if (text != null) e.textContent = text;
    return e;
  }
  var NS = 'http://www.w3.org/2000/svg';
  function sv(tag, attrs, text) {
    var e = document.createElementNS(NS, tag);
    for (var k in attrs) e.setAttribute(k, attrs[k]);
    if (text != null) e.textContent = text;
    return e;
  }
  function clear(node) { while (node.firstChild) node.removeChild(node.firstChild); }
  // text with any https:// links in it made clickable (Google's errors carry the fix link)
  function linkify(text) {
    var frag = document.createDocumentFragment();
    String(text).split(/(https:\/\/[^\s)"']+)/).forEach(function (part, i) {
      if (i % 2) {
        var a = el('a', '', part.replace(/[.,;]$/, ''));
        a.href = a.textContent; a.target = '_blank'; a.rel = 'noopener';
        frag.appendChild(a);
        if (/[.,;]$/.test(part)) frag.appendChild(document.createTextNode(part.slice(-1)));
      } else if (part) frag.appendChild(document.createTextNode(part));
    });
    return frag;
  }

  // ---------- number formats ----------
  var nf = new Intl.NumberFormat('en-US');
  var cf = new Intl.NumberFormat('en-US', { notation: 'compact', maximumFractionDigits: 1 });
  function dur(s) {
    s = Math.round(s || 0);
    if (s < 60) return s + 's';
    if (s < 3600) return Math.floor(s / 60) + 'm ' + (s % 60) + 's';
    return Math.floor(s / 3600) + 'h ' + Math.floor((s % 3600) / 60) + 'm';
  }
  function fmt(v, f, compact) {
    if (v == null || (typeof v === 'number' && isNaN(v))) return '—';
    switch (f) {
      case 'int': return compact && Math.abs(v) >= 10000 ? cf.format(v) : nf.format(Math.round(v));
      case 'pct': return (v * 100).toFixed(1) + '%';
      case 'pct100': return Number(v).toFixed(1) + '%';
      case 'dur': return dur(v);
      case 'dec1': return Number(v).toFixed(1);
      case 'dec2': return Number(v).toFixed(2);
      default: return String(v);
    }
  }
  var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  function shortDate(ymd, withYear) {
    var p = ymd.split('-');
    return MONTHS[+p[1] - 1] + ' ' + (+p[2]) + (withYear ? ', ' + p[0] : '');
  }
  function ago(ts) {
    var s = Math.max(0, Math.round(Date.now() / 1000 - ts));
    if (s < 60) return 'just now';
    if (s < 3600) return Math.round(s / 60) + ' min ago';
    if (s < 86400) return Math.round(s / 3600) + ' h ago';
    return new Date(ts * 1000).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  }
  // change against the previous period: relative for counts and times,
  // points for rates, plain difference for positions
  function change(k) {
    if (k.prev == null || k.value == null) return null;
    var diff = k.value - k.prev, dir = diff > 0 ? 1 : diff < 0 ? -1 : 0, text;
    if (k.fmt === 'int' || k.fmt === 'dur') {
      if (k.prev === 0) { if (k.value === 0) return { dir: 0, good: 0, text: 'No change' }; text = 'New'; }
      else text = Math.abs(diff / k.prev * 100).toFixed(1) + '%';
    } else if (k.fmt === 'pct') text = Math.abs(diff * 100).toFixed(1) + ' pts';
    else if (k.fmt === 'pct100') text = Math.abs(diff).toFixed(1) + ' pts';
    else text = Math.abs(diff).toFixed(k.fmt === 'dec2' ? 2 : 1);
    var good = !k.better || !dir ? 0 : ((dir > 0) === (k.better === 'up') ? 1 : -1);
    return { dir: dir, good: good, text: text };
  }

  // ---------- open, close, load ----------
  function open(source) {
    state.source = source;
    state.data = null;
    titleEl.textContent = NAMES[source] || 'Analytics';
    subEl.textContent = '';
    updatedEl.textContent = '';
    clear(rangeEl); clear(linksEl);
    refreshBtn.hidden = true;
    clear(body);
    body.appendChild(loadingView());
    document.documentElement.classList.add('ad-lock');
    dlg.showModal();
    load(false);
  }
  dlg.addEventListener('close', function () { document.documentElement.classList.remove('ad-lock'); state.req++; });
  byId('adClose').addEventListener('click', function () { dlg.close(); });
  // a click on the dimmed backdrop (the dialog element itself) closes it
  dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
  refreshBtn.addEventListener('click', function () { load(true); });

  document.addEventListener('click', function (e) {
    var b = e.target.closest && e.target.closest('.ad-open');
    if (b) { e.preventDefault(); open(b.getAttribute('data-dash')); return; }
    var c = e.target.closest && e.target.closest('[data-copy]');
    if (c) {
      var src = document.querySelector(c.getAttribute('data-copy'));
      if (src && navigator.clipboard) navigator.clipboard.writeText(src.textContent.trim()).then(function () {
        var was = c.textContent; c.textContent = 'Copied'; setTimeout(function () { c.textContent = was; }, 1500);
      });
    }
  });

  function load(refresh) {
    var src = state.source, id = ++state.req;
    dlg.classList.add('is-loading');
    refreshBtn.disabled = true;
    var url = 'analytics-data.php?source=' + encodeURIComponent(src) + '&days=' + encodeURIComponent(state.days[src] || '') + (refresh ? '&refresh=1' : '');
    fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
      .then(function (r) {
        return r.json().catch(function () { return { ok: false, error: 'The server sent an unexpected answer (HTTP ' + r.status + '). Reload the page and try again.' }; });
      }, function () { return { ok: false, error: "Couldn't reach the server. Check your connection and try again." }; })
      .then(function (d) {
        if (id !== state.req) return;
        dlg.classList.remove('is-loading');
        refreshBtn.disabled = false;
        if (!d || !d.ok) { state.data = null; renderError(d || {}); return; }
        state.data = d;
        state.days[src] = d.days;
        render(d);
      });
  }

  // ---------- views ----------
  function loadingView() {
    var w = el('div', 'ad-loading');
    w.appendChild(el('span', 'ad-spin'));
    w.appendChild(el('span', '', 'Loading ' + (NAMES[state.source] || 'analytics') + '…'));
    return w;
  }

  var SETUP = {
    ga: function (sa) {
      return [
        sa ? 'The service account key is saved (' + sa + ').' : 'Save a Google service account key under “Google service account” on this page. The steps are listed there.',
        'In Google Analytics → Admin → Property access management, add ' + (sa || 'the service account email') + ' with the Viewer role.',
        'Copy the Property ID (a number) from Google Analytics → Admin → Property details into “Property ID” in the Google Analytics card, then click Save Connections.',
        'In Google Cloud → APIs & Services → Library, check that “Google Analytics Data API” is enabled for the service account’s project.'
      ];
    },
    gsc: function (sa) {
      return [
        sa ? 'The service account key is saved (' + sa + ').' : 'Save a Google service account key under “Google service account” on this page. The steps are listed there.',
        'In Search Console → Settings → Users and permissions, add ' + (sa || 'the service account email') + ' as a Restricted user.',
        'In Google Cloud → APIs & Services → Library, check that “Google Search Console API” is enabled for the service account’s project.'
      ];
    },
    clarity: function () {
      return [
        'In Clarity, open your project → Settings → Data Export → Generate new API token.',
        'Paste the token into “API token” in the Microsoft Clarity card on this page, then click Save Connections.',
        'Clarity allows 10 data requests a day, so the numbers are saved here and refreshed a few times a day.'
      ];
    }
  };
  var SETUP_FIELD = { ga: ['google_service_account', 'ga_property_id'], gsc: ['google_service_account', 'gsc_site_url'], clarity: ['clarity_api_token', 'clarity_api_token'] };

  function renderError(d) {
    var src = state.source, setup = d.setup && SETUP[d.setup] ? d.setup : '';
    subEl.textContent = '';
    refreshBtn.hidden = true;
    var box = el('div', 'ad-state');
    box.appendChild(el('h3', '', setup ? 'Finish connecting ' + NAMES[setup] : 'These numbers could not be loaded'));
    var p = el('p');
    p.appendChild(linkify(d.error || 'Something went wrong.'));
    box.appendChild(p);
    if (setup) {
      var ol = el('ol');
      SETUP[setup](d.sa_email || '').forEach(function (t) { ol.appendChild(el('li', '', t)); });
      box.appendChild(ol);
      var go = el('button', 'btn btn-primary btn-sm', 'Go to the settings');
      go.type = 'button';
      go.addEventListener('click', function () {
        dlg.close();
        var f = byId(SETUP_FIELD[setup][d.sa_email ? 1 : 0]);
        if (f) { f.scrollIntoView({ behavior: 'smooth', block: 'center' }); setTimeout(function () { f.focus({ preventScroll: true }); }, 350); }
      });
      box.appendChild(go);
    } else {
      var again = el('button', 'btn btn-primary btn-sm', 'Try again');
      again.type = 'button';
      again.addEventListener('click', function () { load(false); });
      box.appendChild(again);
    }
    clear(body);
    body.appendChild(box);
    if (src === 'ga' || src === 'gsc' || src === 'clarity') titleEl.textContent = NAMES[src];
  }

  function render(d) {
    titleEl.textContent = d.title || NAMES[d.source];
    subEl.textContent = [d.subtitle, d.period, d.compare ? 'compared with ' + d.compare : ''].filter(Boolean).join(' · ');
    clear(rangeEl);
    (d.ranges || []).forEach(function (r) {
      var b = el('button', 'ad-range-b' + (r.days === d.days ? ' is-on' : ''), r.label);
      b.type = 'button';
      b.setAttribute('aria-pressed', r.days === d.days ? 'true' : 'false');
      b.addEventListener('click', function () {
        if (state.days[state.source] === r.days) return;
        state.days[state.source] = r.days;
        [].forEach.call(rangeEl.children, function (x) { x.classList.toggle('is-on', x === b); x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); });
        load(false);
      });
      rangeEl.appendChild(b);
    });
    updatedEl.textContent = d.fetched_at ? 'Updated ' + ago(d.fetched_at) : '';
    refreshBtn.hidden = false;
    clear(linksEl);
    (d.links || []).forEach(function (l) {
      if (!/^https:\/\//.test(l.url)) return;
      var a = el('a', 'btn btn-ghost btn-sm', l.label + ' ↗');
      a.href = l.url; a.target = '_blank'; a.rel = 'noopener';
      linksEl.appendChild(a);
    });

    var frag = document.createDocumentFragment();
    if (d.note) frag.appendChild(el('div', 'ad-note', d.note));
    if (d.info) frag.appendChild(el('div', 'ad-info', d.info));
    if (d.live) frag.appendChild(liveView(d.live));
    frag.appendChild(kpiView(d));
    if (d.series && d.series.dates && d.series.dates.length) frag.appendChild(chartCard(d));
    frag.appendChild(panelsView(d));
    clear(body);
    body.appendChild(frag);
    drawChart();
  }

  function liveView(live) {
    var w = el('div', 'ad-live');
    w.appendChild(el('span', 'ad-live-dot'));
    var t = el('span', 'ad-live-t');
    t.appendChild(el('b', 'ad-live-n', fmt(live.value, 'int')));
    t.appendChild(document.createTextNode(' ' + live.label));
    w.appendChild(t);
    if (live.rows && live.rows.length) {
      w.appendChild(el('span', 'ad-live-pages', 'Now on: ' + live.rows.map(function (r) { return r[0] + ' (' + fmt(r[1], 'int') + ')'; }).join(', ')));
    }
    return w;
  }

  function chartable(d) {
    var m = d.series && d.series.metrics ? d.series.metrics : {};
    return (d.kpis || []).filter(function (k) { return m[k.id]; });
  }
  function currentKpi(d) {
    var list = chartable(d), want = state.metric[d.source];
    for (var i = 0; i < list.length; i++) if (list[i].id === want) return list[i];
    return list[0];
  }

  function kpiView(d) {
    var grid = el('div', 'ad-kpis'), cur = currentKpi(d), m = d.series && d.series.metrics ? d.series.metrics : {};
    (d.kpis || []).forEach(function (k) {
      var canChart = !!m[k.id], t = el(canChart ? 'button' : 'div', 'ad-kpi' + (cur && cur.id === k.id ? ' is-on' : ''));
      if (canChart) {
        t.type = 'button';
        t.setAttribute('aria-pressed', cur && cur.id === k.id ? 'true' : 'false');
        t.title = 'Show ' + k.label.toLowerCase() + ' on the chart';
        t.addEventListener('click', function () {
          state.metric[d.source] = k.id;
          [].forEach.call(grid.children, function (x) { var on = x === t; x.classList.toggle('is-on', on); if (x.tagName === 'BUTTON') x.setAttribute('aria-pressed', on ? 'true' : 'false'); });
          var old = body.querySelector('.ad-chart-card');
          if (old) old.parentNode.replaceChild(chartCard(d), old);
          drawChart();
        });
      }
      t.appendChild(el('span', 'ad-kpi-l', k.label));
      t.appendChild(el('span', 'ad-kpi-v', fmt(k.value, k.fmt, true)));
      var c = change(k);
      if (c) {
        var dEl = el('span', 'ad-kpi-d' + (c.good > 0 ? ' is-good' : c.good < 0 ? ' is-bad' : ''));
        dEl.appendChild(document.createTextNode((c.dir > 0 ? '▲ ' : c.dir < 0 ? '▼ ' : '') + c.text + ' '));
        dEl.appendChild(el('span', '', 'vs previous'));
        dEl.title = 'Compared with ' + (d.compare || 'the previous period');
        t.appendChild(dEl);
      }
      grid.appendChild(t);
    });
    return grid;
  }

  function chartCard(d) {
    var k = currentKpi(d), card = el('section', 'ad-card ad-chart-card');
    if (!k) return card;
    var head = el('div', 'ad-chart-head');
    head.appendChild(el('h3', '', k.label));
    head.appendChild(el('span', 't-sub', (d.series.labels ? 'per hour' : 'per day') + ' · ' + (d.period || '')));
    card.appendChild(head);
    var box = el('div', 'ad-chart');
    box.appendChild(el('div', 'ad-tip'));
    card.appendChild(box);
    // the same numbers as a table
    var det = el('details', 'ad-table-view');
    det.appendChild(el('summary', '', 'Show as a table'));
    var wrap = el('div', 'ad-table-scroll'), tbl = el('table', 'ad-table'), thead = el('thead'), tr = el('tr');
    tr.appendChild(el('th', '', d.series.labels ? 'Hour' : 'Date'));
    tr.appendChild(el('th', 'num', k.label));
    thead.appendChild(tr); tbl.appendChild(thead);
    var tb = el('tbody'), vals = d.series.metrics[k.id];
    d.series.dates.forEach(function (dt, i) {
      var r = el('tr');
      r.appendChild(el('td', '', d.series.tips ? d.series.tips[i] : shortDate(dt, true)));
      r.appendChild(el('td', 'num', fmt(vals[i], k.fmt)));
      tb.appendChild(r);
    });
    tbl.appendChild(tb); wrap.appendChild(tbl); det.appendChild(wrap);
    card.appendChild(det);
    return card;
  }

  // ---------- the trend chart ----------
  function niceStep(span, n) {
    var raw = span / n, p = Math.pow(10, Math.floor(Math.log10(raw))), f = raw / p;
    return (f <= 1 ? 1 : f <= 2 ? 2 : f <= 2.5 ? 2.5 : f <= 5 ? 5 : 10) * p;
  }
  function tickText(v, f, step, hi) {
    if (f === 'int') return hi >= 10000 ? cf.format(v) : nf.format(v);
    if (f === 'pct') return (v * 100).toFixed(step * 100 < 1 ? 1 : 0) + '%';
    if (f === 'pct100') return v.toFixed(step < 1 ? 1 : 0) + '%';
    if (f === 'dur') return dur(v);
    return v.toFixed(step < 1 ? 1 : 0);
  }

  function drawChart() {
    var d = state.data, box = body.querySelector('.ad-chart');
    if (!d || !box) return;
    var k = currentKpi(d);
    if (!k) return;
    var vals = d.series.metrics[k.id], dates = d.series.dates, n = dates.length;
    // an hourly series (Today) brings its own labels; a daily one is labelled by date
    function axisLabel(i) { return d.series.labels ? d.series.labels[i] : shortDate(dates[i]); }
    function tipLabel(i) { return d.series.tips ? d.series.tips[i] : shortDate(dates[i], true); }
    var old = box.querySelector('svg');
    if (old) box.removeChild(old);
    var tip = box.querySelector('.ad-tip');
    tip.hidden = true;

    var W = Math.max(280, box.clientWidth), H = box.clientHeight || 260;
    var padL = 54, padR = 18, padT = 12, padB = 28, pw = W - padL - padR, ph = H - padT - padB;
    var nums = vals.filter(function (v) { return v != null; });
    var lo = 0, hi = nums.length ? Math.max.apply(null, nums) : 0;
    if (k.invert && nums.length) lo = Math.min.apply(null, nums);
    if (hi === lo) hi = lo + (k.fmt === 'pct' ? 0.1 : 1);
    var step = niceStep(hi - lo, 4);
    if (k.fmt === 'int') step = Math.max(1, Math.round(step));
    lo = k.invert ? Math.max(k.fmt === 'dec1' ? 1 : 0, Math.floor(lo / step) * step) : 0;
    hi = Math.ceil(hi / step) * step;
    if (hi <= lo) hi = lo + step;
    function x(i) { return padL + (n === 1 ? pw / 2 : i * pw / (n - 1)); }
    function y(v) { var f = (v - lo) / (hi - lo); return k.invert ? padT + f * ph : padT + ph - f * ph; }

    var unit = d.series.labels ? 'hour' : 'day';
    var svg = sv('svg', { viewBox: '0 0 ' + W + ' ' + H, width: W, height: H, tabindex: '0', role: 'img',
      'aria-label': k.label + ' per ' + unit + ', ' + (d.period || '') + '. Use the left and right arrow keys to read each ' + unit + '.' });
    // grid and y ticks
    for (var t = lo; t <= hi + step / 2; t += step) {
      var yy = Math.round(y(t)) + .5;
      svg.appendChild(sv('line', { x1: padL, x2: W - padR, y1: yy, y2: yy, stroke: t === lo && !k.invert ? '#d9d9d6' : '#ececea', 'stroke-width': 1 }));
      svg.appendChild(sv('text', { x: padL - 10, y: yy + 4, 'text-anchor': 'end', class: 'ad-tick' }, tickText(t, k.fmt, step, hi)));
    }
    // x labels: as many as fit (about 64px each, at most seven), always the first and last day
    var every = Math.max(1, Math.ceil((n - 1) / Math.max(1, Math.min(6, Math.floor(pw / 64)))));
    var at = [];
    for (var i = 0; i < n; i += every) at.push(i);
    if (n > 1 && at[at.length - 1] !== n - 1) {
      if (n - 1 - at[at.length - 1] < every * .7 && at.length > 1) at.pop();
      at.push(n - 1);
    }
    at.forEach(function (i, j) {
      var anchor = n === 1 ? 'middle' : j === 0 ? 'start' : j === at.length - 1 ? 'end' : 'middle';
      svg.appendChild(sv('text', { x: x(i), y: H - 6, 'text-anchor': anchor, class: 'ad-tick' }, axisLabel(i)));
    });

    // runs of days that have a value (positions and CTR have gaps)
    var runs = [], run = [];
    vals.forEach(function (v, i) { if (v == null) { if (run.length) runs.push(run); run = []; } else run.push(i); });
    if (run.length) runs.push(run);
    var base = y(lo);
    runs.forEach(function (r) {
      var line = r.map(function (i, j) { return (j ? 'L' : 'M') + x(i).toFixed(1) + ',' + y(vals[i]).toFixed(1); }).join('');
      if (!k.invert && r.length > 1) {
        svg.appendChild(sv('path', { d: line + 'L' + x(r[r.length - 1]).toFixed(1) + ',' + base + 'L' + x(r[0]).toFixed(1) + ',' + base + 'Z', fill: INK, 'fill-opacity': '.1' }));
      }
      if (r.length > 1) svg.appendChild(sv('path', { d: line, fill: 'none', stroke: INK, 'stroke-width': 2, 'stroke-linejoin': 'round', 'stroke-linecap': 'round' }));
      else svg.appendChild(sv('circle', { cx: x(r[0]), cy: y(vals[r[0]]), r: 3, fill: INK }));
    });
    var lastRun = runs[runs.length - 1];
    if (lastRun) {
      var li = lastRun[lastRun.length - 1];
      svg.appendChild(sv('circle', { cx: x(li), cy: y(vals[li]), r: 4, fill: INK, stroke: '#fff', 'stroke-width': 2 }));
    }

    // hover / keyboard layer
    var cross = sv('line', { x1: 0, x2: 0, y1: padT, y2: padT + ph, stroke: '#94948f', 'stroke-width': 1, visibility: 'hidden' });
    var dot = sv('circle', { r: 4.5, fill: INK, stroke: '#fff', 'stroke-width': 2, visibility: 'hidden' });
    var hit = sv('rect', { x: padL - 10, y: 0, width: pw + 20, height: H, fill: 'transparent' });
    svg.appendChild(cross); svg.appendChild(dot); svg.appendChild(hit);
    box.insertBefore(svg, tip);

    function show(i) {
      state.idx = i = Math.max(0, Math.min(n - 1, i));
      var cx = x(i), v = vals[i];
      cross.setAttribute('x1', cx); cross.setAttribute('x2', cx); cross.setAttribute('visibility', 'visible');
      if (v != null) { dot.setAttribute('cx', cx); dot.setAttribute('cy', y(v)); dot.setAttribute('visibility', 'visible'); }
      else dot.setAttribute('visibility', 'hidden');
      clear(tip);
      tip.appendChild(el('b', '', v == null ? 'No data' : fmt(v, k.fmt)));
      tip.appendChild(el('span', '', tipLabel(i)));
      tip.hidden = false;
      var half = tip.offsetWidth / 2;
      tip.style.left = Math.max(half, Math.min(W - half, cx)) + 'px';
    }
    function hide() { cross.setAttribute('visibility', 'hidden'); dot.setAttribute('visibility', 'hidden'); tip.hidden = true; }
    function nearest(evt) {
      var r = svg.getBoundingClientRect(), px = (evt.clientX - r.left) * (W / r.width);
      return n === 1 ? 0 : Math.round((px - padL) / pw * (n - 1));
    }
    hit.addEventListener('pointermove', function (e) { show(nearest(e)); });
    hit.addEventListener('pointerdown', function (e) { show(nearest(e)); });
    hit.addEventListener('pointerleave', hide);
    svg.addEventListener('focus', function () { show(state.idx >= 0 && state.idx < n ? state.idx : n - 1); });
    svg.addEventListener('blur', hide);
    svg.addEventListener('keydown', function (e) {
      var step = { ArrowLeft: -1, ArrowRight: 1, Home: -n, End: n }[e.key];
      if (step) { e.preventDefault(); show((state.idx < 0 ? n - 1 : state.idx) + step); }
    });
  }
  var rt;
  window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(function () { if (dlg.open) drawChart(); }, 120); });

  // ---------- ranked tables ----------
  function panelsView(d) {
    var grid = el('div', 'ad-panels');
    (d.panels || []).forEach(function (p) {
      var sec = el('section', 'ad-card ad-panel' + (p.wide ? ' is-wide' : ''));
      sec.appendChild(el('h3', '', p.title));
      if (p.note) sec.appendChild(el('p', 'ad-panel-note', p.note));
      if (!p.rows || !p.rows.length) {
        sec.appendChild(el('p', 'ad-empty', 'No data for this period yet.'));
        grid.appendChild(sec);
        return;
      }
      // bars are drawn against the biggest row, and shares against the whole
      var max = 0, barFmt = p.bar ? p.cols[p.bar][1] : '';
      if (p.bar) p.rows.forEach(function (r) { max = Math.max(max, +r[p.bar] || 0); });
      if (barFmt === 'pct') max = 1;
      if (barFmt === 'pct100') max = 100;
      var wrap = el('div', 'ad-table-scroll'), tbl = el('table', 'ad-table'), thead = el('thead'), htr = el('tr');
      p.cols.forEach(function (c, i) { htr.appendChild(el('th', i ? 'num' : 'lab', c[0])); });
      thead.appendChild(htr); tbl.appendChild(thead);
      var tb = el('tbody');
      p.rows.forEach(function (r) {
        var tr = el('tr');
        p.cols.forEach(function (c, i) {
          if (i === 0) {
            var text = c[1] === 'country' ? countryName(r[0]) : (r[0] === '' ? '(not set)' : String(r[0]));
            var td = el('td', 'lab'), lab = el('div', 'ad-lab', text);
            lab.title = text;
            td.appendChild(lab);
            if (p.bar && max > 0) {
              var bar = el('div', 'ad-bar'), fill = el('span');
              fill.style.width = Math.max(1, (+r[p.bar] || 0) / max * 100) + '%';
              bar.appendChild(fill); td.appendChild(bar);
            }
            tr.appendChild(td);
          } else {
            tr.appendChild(el('td', c[1] === 'text' ? 'num txt' : 'num', fmt(r[i], c[1])));
          }
        });
        tb.appendChild(tr);
      });
      tbl.appendChild(tb); wrap.appendChild(tbl); sec.appendChild(wrap);
      grid.appendChild(sec);
    });
    return grid;
  }
})();
