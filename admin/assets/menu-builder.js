// Admin → Menu builder (admin/menu.php).
//
// The menu is shown as one flat list of rows, each with a depth: 0 top level,
// 1 sub item, 2 sub-sub item (data-depth, drawn as an indent). An item moves
// together with the rows below it that are deeper than it (its sub items).
// Dragging the ⠿ handle or the arrow buttons reorder and nest; a move that
// would leave a row more than one level deeper than the row above it is not
// offered. On submit the rows are folded back into a tree and posted as JSON;
// includes/nav-menu.php checks it, so nothing here is trusted.
(function () {
  var dataEl = document.getElementById('menuData');
  var form = document.getElementById('menuForm');
  var list = document.getElementById('mbList');
  if (!dataEl || !form || !list) return;

  var data = JSON.parse(dataEl.textContent);
  var icons = data.icons || {};
  var services = data.services || [];
  var MAX = data.maxDepth;
  var INDENT = 34; // px per level, as .mb-item's margin in menu.php
  var emptyEl = document.getElementById('mbEmpty');
  var dirtyEl = document.getElementById('mbDirty');
  var dirty = false;

  // ── helpers ──
  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  function uid() { return 'item-' + Math.random().toString(16).slice(2, 10); }
  function markDirty() { dirty = true; dirtyEl.hidden = false; }
  function rows() {
    return Array.prototype.filter.call(list.children, function (el) { return el.classList.contains('mb-item'); });
  }
  function depth(el) { return +el.getAttribute('data-depth') || 0; }
  function setDepth(el, d) { el.setAttribute('data-depth', d); el.style.setProperty('--d', d); }
  function isRow(el) { return el && el.classList.contains('mb-item'); }
  /** The row and the rows under it that belong to it. */
  function block(li) {
    var out = [li], d = depth(li), n = li.nextElementSibling;
    while (isRow(n) && depth(n) > d) { out.push(n); n = n.nextElementSibling; }
    return out;
  }
  function blockHeight(blk) {
    var base = depth(blk[0]), h = 0;
    blk.forEach(function (r) { h = Math.max(h, depth(r) - base); });
    return h;
  }
  function shift(blk, delta) { blk.forEach(function (r) { setDepth(r, depth(r) + delta); }); }
  function prevSibling(li) {
    var d = depth(li), p = li.previousElementSibling;
    while (isRow(p)) { if (depth(p) === d) return p; if (depth(p) < d) return null; p = p.previousElementSibling; }
    return null;
  }
  function nextSibling(li) {
    var blk = block(li), n = blk[blk.length - 1].nextElementSibling;
    return isRow(n) && depth(n) === depth(li) ? n : null;
  }
  function parentOf(li) {
    var d = depth(li), p = li.previousElementSibling;
    while (isRow(p)) { if (depth(p) < d) return p; p = p.previousElementSibling; }
    return null;
  }
  function field(li, name) { return li.querySelector('[data-f="' + name + '"]'); }
  function iconOptions(selected) {
    var core = '', ind = '';
    Object.keys(icons).forEach(function (k) {
      var opt = '<option value="' + esc(k) + '"' + (k === selected ? ' selected' : '') + '>' + esc(icons[k][0]) + '</option>';
      if (k.indexOf('industry-') === 0) ind += opt; else core += opt;
    });
    return '<option value="">No icon</option><optgroup label="Solutions">' + core + '</optgroup>' +
      (ind ? '<optgroup label="Industries">' + ind + '</optgroup>' : '');
  }
  function serviceOptions(selected) {
    var list = services.slice();
    if (selected && list.indexOf(selected) === -1) list.push(selected);
    return '<option value="">Show the bullet points</option><optgroup label="Show the latest case studies for">' +
      list.map(function (s) {
        return '<option value="' + esc(s) + '"' + (s === selected ? ' selected' : '') + '>' + esc(s) + '</option>';
      }).join('') + '</optgroup>';
  }

  var ARROW = {
    up: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>',
    down: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg>',
    out: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>',
    'in': '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>'
  };

  // ── one menu row ──
  function makeRow(item, d) {
    var li = document.createElement('li');
    li.className = 'mb-item';
    li.setAttribute('data-id', item.id || uid());
    setDepth(li, d);
    li.innerHTML =
      '<div class="mb-bar">' +
        '<button type="button" class="mb-handle" aria-label="Drag to move" title="Drag to move">' +
          '<svg width="12" height="18" viewBox="0 0 12 18" fill="currentColor"><circle cx="3" cy="3" r="1.6"/><circle cx="9" cy="3" r="1.6"/><circle cx="3" cy="9" r="1.6"/><circle cx="9" cy="9" r="1.6"/><circle cx="3" cy="15" r="1.6"/><circle cx="9" cy="15" r="1.6"/></svg>' +
        '</button>' +
        '<button type="button" class="mb-toggle" aria-expanded="false"><span class="mb-title"></span><span class="mb-url"></span></button>' +
        '<span class="mb-badges"><span class="mb-badge" data-b="hidden">Hidden</span><span class="mb-badge mb-badge-mega" data-b="mega">Mega menu</span></span>' +
        '<span class="mb-moves">' +
          '<button type="button" data-move="up" title="Move up" aria-label="Move up">' + ARROW.up + '</button>' +
          '<button type="button" data-move="down" title="Move down" aria-label="Move down">' + ARROW.down + '</button>' +
          '<button type="button" data-move="out" title="Move out of the sub menu" aria-label="Move out of the sub menu">' + ARROW.out + '</button>' +
          '<button type="button" data-move="in" title="Make it a sub item of the item above" aria-label="Make it a sub item of the item above">' + ARROW['in'] + '</button>' +
        '</span>' +
        '<button type="button" class="mb-chev" tabindex="-1" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg></button>' +
      '</div>' +
      '<div class="mb-panel" hidden>' +
        '<div class="mb-grid">' +
          '<div class="field"><label>Label</label><input type="text" data-f="label" maxlength="80"></div>' +
          '<div class="field"><label>Link</label><input type="text" data-f="url" maxlength="255" placeholder="/about-us, #section or https://…">' +
            '<div class="field-hint">Leave empty for a heading that only opens its sub menu.</div></div>' +
        '</div>' +
        '<div class="mb-checks">' +
          '<label class="checkbox-row"><input type="checkbox" data-f="visible"> Show in menu</label>' +
          '<label class="checkbox-row"><input type="checkbox" data-f="new_tab"> Open in a new tab</label>' +
          '<label class="checkbox-row" data-mega-toggle><input type="checkbox" data-f="mega"> Mega menu</label>' +
        '</div>' +
        '<p class="mb-note" data-note="depth" hidden>Mega menus only work on top-level items. Move this item back to the top level to use its mega menu; it is kept until you save.</p>' +
        '<p class="mb-note" data-note="kids" hidden>While the mega menu is on, the desktop header shows it instead of this item’s sub items. The sub items still show in the mobile menu.</p>' +
        '<div class="mb-mega-slot"></div>' +
        '<div class="mb-panel-foot"><button type="button" class="btn btn-danger btn-sm" data-act="remove">Remove item</button></div>' +
      '</div>';
    field(li, 'label').value = item.label || '';
    field(li, 'url').value = item.url || '';
    field(li, 'visible').checked = item.visible !== false;
    field(li, 'new_tab').checked = !!item.new_tab;
    field(li, 'mega').checked = !!(item.mega && item.mega.enabled);
    if (item.mega) li.querySelector('.mb-mega-slot').appendChild(megaEditor(item.mega));
    syncBar(li);
    return li;
  }

  function syncBar(li) {
    var label = field(li, 'label').value.trim();
    var url = field(li, 'url').value.trim();
    li.querySelector('.mb-title').textContent = label || 'Untitled item';
    li.querySelector('.mb-url').textContent = url || 'no link';
    li.querySelector('[data-b="hidden"]').hidden = field(li, 'visible').checked;
    var megaOn = field(li, 'mega').checked && depth(li) === 0;
    li.querySelector('[data-b="mega"]').hidden = !megaOn;
    var ed = li.querySelector('.mb-mega');
    if (ed) ed.hidden = !megaOn;
  }

  // ── mega menu editor ──
  function megaEditor(m) {
    var w = document.createElement('div');
    w.className = 'mb-mega' + (m.layout === 'tiles' ? ' is-tiles' : '');
    var cta = m.cta || {};
    w.innerHTML =
      '<div class="mb-mega-head">' +
        '<div class="field"><label>Mega menu layout</label><select data-m="layout">' +
          '<option value="columns">Wide columns with bullet points</option>' +
          '<option value="tiles">Compact tiles (like Industries)</option>' +
        '</select></div>' +
        '<div class="field-hint mb-only-columns" style="margin-bottom:.9rem">Up to five columns sit in a row, counting the booking box.</div>' +
      '</div>' +
      '<div class="mb-sub-h">Columns</div>' +
      '<div class="mb-cols"></div>' +
      '<button type="button" class="btn btn-ghost btn-sm" data-act="add-col">+ Add column</button>' +
      '<div class="mb-cta">' +
        '<label class="checkbox-row" style="display:inline-flex"><input type="checkbox" data-m="cta-enabled"> Booking box at the end (its button opens the booking calendar)</label>' +
        '<div class="mb-cta-fields">' +
          '<div class="field"><label>Box title</label><input type="text" data-m="cta-title" maxlength="120"></div>' +
          '<div class="field"><label>Button text</label><input type="text" data-m="cta-button" maxlength="60"></div>' +
          '<div class="field mb-span" style="grid-column:1/-1"><label>Box text</label><input type="text" data-m="cta-text" maxlength="300"></div>' +
        '</div>' +
      '</div>';
    w.querySelector('[data-m="layout"]').value = m.layout === 'tiles' ? 'tiles' : 'columns';
    w.querySelector('[data-m="cta-enabled"]').checked = !!cta.enabled;
    w.querySelector('[data-m="cta-title"]').value = cta.title || '';
    w.querySelector('[data-m="cta-button"]').value = cta.button || 'Book a Free Consultation →';
    w.querySelector('[data-m="cta-text"]').value = cta.text || '';
    w.querySelector('.mb-cta-fields').hidden = !cta.enabled;
    var cols = w.querySelector('.mb-cols');
    (m.columns || []).forEach(function (c) { cols.appendChild(columnEditor(c)); });
    syncColumnTools(w);
    return w;
  }

  function columnEditor(c) {
    var d = document.createElement('details');
    d.className = 'mb-col';
    d.innerHTML =
      '<summary><span class="mb-col-icon"></span><span class="mb-col-name"></span>' +
        '<span class="mb-col-tools">' +
          '<button type="button" data-col="up" title="Move column up" aria-label="Move column up">' + ARROW.up + '</button>' +
          '<button type="button" data-col="down" title="Move column down" aria-label="Move column down">' + ARROW.down + '</button>' +
          '<button type="button" data-col="remove" class="mb-col-del">Remove</button>' +
        '</span></summary>' +
      '<div class="mb-col-body">' +
        '<div class="field"><label>Icon</label><select data-c="icon">' + iconOptions(c.icon || '') + '</select></div>' +
        '<div class="field"><label>Icon colours</label><div class="mb-colors">' +
          '<input type="color" data-c="color" title="Gradient start"><input type="color" data-c="color_to" title="Gradient end"></div></div>' +
        '<div class="field mb-span"><label>Title</label><input type="text" data-c="title" maxlength="120"></div>' +
        '<div class="field mb-span"><label>Short text</label><input type="text" data-c="text" maxlength="200">' +
          '<div class="field-hint">Optional. Tiles show it in small capitals under the title.</div></div>' +
        '<div class="field mb-span mb-only-columns"><label>Bullet points</label><textarea data-c="bullets" rows="3"></textarea>' +
          '<div class="field-hint">One per line, up to ' + data.maxBullets + '.</div></div>' +
        '<div class="field mb-span mb-only-columns"><label>Column list</label><select data-c="case_studies">' + serviceOptions(c.case_studies || '') + '</select>' +
          '<div class="field-hint">Case studies fill in by themselves: the three newest published for that service.</div></div>' +
        '<div class="field"><label>Link text</label><input type="text" data-c="link_label" maxlength="60" placeholder="Know More →"></div>' +
        '<div class="field"><label>Link</label><input type="text" data-c="link_url" maxlength="255" placeholder="/custom-erp-solution"></div>' +
      '</div>';
    var set = function (k, v) { d.querySelector('[data-c="' + k + '"]').value = v; };
    set('color', c.color || '#32b46f');
    set('color_to', c.color_to || '#14855a');
    set('title', c.title || '');
    set('text', c.text || '');
    set('bullets', (c.bullets || []).join('\n'));
    set('link_label', c.link_label == null ? 'Know More →' : c.link_label);
    set('link_url', c.link_url || '');
    syncColumn(d);
    return d;
  }

  function syncColumn(d) {
    var v = function (k) { return d.querySelector('[data-c="' + k + '"]').value; };
    d.querySelector('.mb-col-name').textContent = v('title').trim();
    var ic = d.querySelector('.mb-col-icon');
    var key = v('icon');
    ic.innerHTML = icons[key] ? icons[key][1] : '';
    ic.style.background = icons[key] ? 'linear-gradient(135deg,' + v('color') + ',' + v('color_to') + ')' : '';
  }

  function syncColumnTools(w) {
    var cols = w.querySelectorAll('.mb-col');
    Array.prototype.forEach.call(cols, function (c, i) {
      c.querySelector('[data-col="up"]').disabled = i === 0;
      c.querySelector('[data-col="down"]').disabled = i === cols.length - 1;
    });
    w.querySelector('[data-act="add-col"]').disabled = cols.length >= data.maxColumns;
  }

  function readMega(li) {
    var w = li.querySelector('.mb-mega');
    if (!w) return null;
    var q = function (sel) { return w.querySelector(sel); };
    var m = {
      enabled: field(li, 'mega').checked,
      layout: q('[data-m="layout"]').value,
      columns: [],
      cta: {
        enabled: q('[data-m="cta-enabled"]').checked,
        title: q('[data-m="cta-title"]').value,
        text: q('[data-m="cta-text"]').value,
        button: q('[data-m="cta-button"]').value
      }
    };
    Array.prototype.forEach.call(w.querySelectorAll('.mb-col'), function (d) {
      var v = function (k) { return d.querySelector('[data-c="' + k + '"]').value; };
      m.columns.push({
        icon: v('icon'), color: v('color'), color_to: v('color_to'), title: v('title'), text: v('text'),
        bullets: v('bullets').split('\n').map(function (s) { return s.trim(); }).filter(Boolean),
        case_studies: v('case_studies'), link_label: v('link_label'), link_url: v('link_url')
      });
    });
    return m;
  }

  // ── whole list ──
  function refresh() {
    var rs = rows();
    emptyEl.hidden = rs.length > 0;
    rs.forEach(function (li) {
      var d = depth(li), blk = block(li);
      li.querySelector('[data-move="up"]').disabled = !prevSibling(li);
      li.querySelector('[data-move="down"]').disabled = !nextSibling(li);
      li.querySelector('[data-move="out"]').disabled = d === 0;
      li.querySelector('[data-move="in"]').disabled = !prevSibling(li) || d + 1 + blockHeight(blk) > MAX;
      var megaChecked = field(li, 'mega').checked;
      li.querySelector('[data-mega-toggle]').hidden = d > 0;
      li.querySelector('[data-note="depth"]').hidden = !(d > 0 && megaChecked);
      li.querySelector('[data-note="kids"]').hidden = !(d === 0 && megaChecked && blk.length > 1);
      syncBar(li);
    });
  }

  function serialize() {
    var roots = [], last = [];
    rows().forEach(function (li) {
      var d = depth(li);
      var item = {
        id: li.getAttribute('data-id'),
        label: field(li, 'label').value,
        url: field(li, 'url').value,
        visible: field(li, 'visible').checked,
        new_tab: field(li, 'new_tab').checked,
        children: [],
        mega: d === 0 ? readMega(li) : null
      };
      var parent = d > 0 ? last[d - 1] : null;
      if (parent) parent.children.push(item); else roots.push(item);
      last[d] = item;
      last.length = d + 1;
    });
    return roots;
  }

  function addRows(items, d) {
    items.forEach(function (it) {
      list.appendChild(makeRow(it, d));
      if (it.children && it.children.length && d < MAX) addRows(it.children, d + 1);
    });
  }

  function togglePanel(li, open) {
    var panel = li.querySelector('.mb-panel');
    if (open === undefined) open = panel.hidden;
    panel.hidden = !open;
    li.classList.toggle('is-open', open);
    li.querySelector('.mb-toggle').setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  // ── moves ──
  function move(li, how) {
    var blk = block(li), s, after;
    if (how === 'up') {
      s = prevSibling(li);
      if (!s) return;
      blk.forEach(function (r) { list.insertBefore(r, s); });
    } else if (how === 'down') {
      s = nextSibling(li);
      if (!s) return;
      var sb = block(s);
      after = sb[sb.length - 1].nextElementSibling;
      blk.forEach(function (r) { list.insertBefore(r, after); });
    } else if (how === 'in') {
      if (!prevSibling(li) || depth(li) + 1 + blockHeight(blk) > MAX) return;
      shift(blk, 1);
    } else if (how === 'out') {
      if (depth(li) === 0) return;
      var pb = block(parentOf(li));
      after = pb[pb.length - 1].nextElementSibling;
      blk.forEach(function (r) { list.insertBefore(r, after); });
      shift(blk, -1);
    }
    refresh();
    markDirty();
    var btn = li.querySelector('[data-move="' + how + '"]');
    (btn && !btn.disabled ? btn : li.querySelector('.mb-handle')).focus();
  }

  // ── drag and drop ──
  var drag = null;
  list.addEventListener('pointerdown', function (e) {
    var handle = e.target.closest('.mb-handle');
    if (!handle || e.button !== 0) return;
    e.preventDefault();
    var li = handle.closest('.mb-item');
    var blk = block(li);
    var top = li.getBoundingClientRect().top;
    var bottom = blk[blk.length - 1].getBoundingClientRect().bottom;
    var listRect = list.getBoundingClientRect();
    var ph = document.createElement('li');
    ph.className = 'mb-ph';
    ph.style.height = (bottom - top) + 'px';
    setDepth(ph, depth(li));
    list.insertBefore(ph, li);
    var ghost = document.createElement('ul');
    ghost.className = 'mb-ghost';
    ghost.style.left = listRect.left + 'px';
    ghost.style.top = top + 'px';
    ghost.style.width = listRect.width + 'px';
    blk.forEach(function (r) { ghost.appendChild(r); });
    document.body.appendChild(ghost);
    document.body.classList.add('mb-dragging');
    drag = { blk: blk, ph: ph, ghost: ghost, base: depth(li), height: blockHeight(blk), sx: e.clientX, sy: e.clientY, id: e.pointerId,
      startNext: ph.nextElementSibling };
    try { handle.setPointerCapture(e.pointerId); } catch (err) { /* not needed for mouse */ }
  });

  document.addEventListener('pointermove', function (e) {
    if (!drag || e.pointerId !== drag.id) return;
    var dx = e.clientX - drag.sx, dy = e.clientY - drag.sy;
    drag.ghost.style.transform = 'translate(' + dx + 'px,' + dy + 'px)';
    if (e.clientY < 70) window.scrollBy(0, -14);
    else if (e.clientY > window.innerHeight - 70) window.scrollBy(0, 14);

    // Where it would land: before the first row whose middle is below the pointer.
    var target = null, rs = rows();
    for (var i = 0; i < rs.length; i++) {
      var r = rs[i].getBoundingClientRect();
      if (e.clientY < r.top + r.height / 2) { target = rs[i]; break; }
    }
    var oldNext = drag.ph.nextElementSibling;
    if (target !== oldNext) list.insertBefore(drag.ph, target);
    var prev = drag.ph.previousElementSibling, next = drag.ph.nextElementSibling;
    var maxD = isRow(prev) ? Math.min(depth(prev) + 1, MAX - drag.height) : 0;
    var minD = isRow(next) ? Math.max(0, depth(next) - 1) : 0;
    if (minD > maxD) { // no depth works here: stay where it was
      list.insertBefore(drag.ph, oldNext);
      return;
    }
    var want = drag.base + Math.round(dx / INDENT);
    setDepth(drag.ph, Math.max(minD, Math.min(maxD, want)));
  });

  function endDrag(e) {
    if (!drag || (e && e.pointerId !== drag.id)) return;
    var delta = depth(drag.ph) - drag.base;
    var moved = delta !== 0 || drag.ph.nextElementSibling !== drag.startNext;
    drag.blk.forEach(function (r) { list.insertBefore(r, drag.ph); });
    shift(drag.blk, delta);
    drag.ph.remove();
    drag.ghost.remove();
    document.body.classList.remove('mb-dragging');
    drag = null;
    refresh();
    if (moved) markDirty();
  }
  document.addEventListener('pointerup', endDrag);
  document.addEventListener('pointercancel', endDrag);

  // ── clicks and typing inside the list ──
  list.addEventListener('click', function (e) {
    var li = e.target.closest('.mb-item');
    if (!li) return;
    var t;
    if ((t = e.target.closest('[data-move]'))) { move(li, t.getAttribute('data-move')); return; }
    if (e.target.closest('.mb-toggle, .mb-chev')) { togglePanel(li); return; }
    if (e.target.closest('[data-act="remove"]')) {
      var blk = block(li);
      var name = field(li, 'label').value.trim() || 'this item';
      if (blk.length > 1 && !confirm('Remove “' + name + '” and its ' + (blk.length - 1) + ' sub item' + (blk.length > 2 ? 's' : '') + '?')) return;
      blk.forEach(function (r) { r.remove(); });
      refresh();
      markDirty();
      return;
    }
    var w = li.querySelector('.mb-mega');
    if (e.target.closest('[data-act="add-col"]')) {
      var col = columnEditor({});
      w.querySelector('.mb-cols').appendChild(col);
      col.open = true;
      col.querySelector('[data-c="title"]').focus();
      syncColumnTools(w);
      markDirty();
      return;
    }
    if ((t = e.target.closest('[data-col]'))) {
      e.preventDefault(); // the buttons sit in <summary>: don't open/close the column
      var d = t.closest('.mb-col'), how = t.getAttribute('data-col');
      if (how === 'remove') {
        var title = d.querySelector('[data-c="title"]').value.trim();
        if (!confirm('Remove the column' + (title ? ' “' + title + '”' : '') + '?')) return;
        d.remove();
      } else if (how === 'up' && d.previousElementSibling) {
        d.parentNode.insertBefore(d, d.previousElementSibling);
      } else if (how === 'down' && d.nextElementSibling) {
        d.parentNode.insertBefore(d.nextElementSibling, d);
      }
      syncColumnTools(w);
      markDirty();
    }
  });

  list.addEventListener('input', function (e) {
    var li = e.target.closest('.mb-item');
    if (!li) return;
    markDirty();
    var col = e.target.closest('.mb-col');
    if (col) syncColumn(col);
    else syncBar(li);
  });

  list.addEventListener('change', function (e) {
    var li = e.target.closest('.mb-item');
    if (!li) return;
    markDirty();
    var t = e.target;
    if (t.matches('[data-f="mega"]') && t.checked && !li.querySelector('.mb-mega')) {
      // First time on: start with one empty column and a booking box.
      li.querySelector('.mb-mega-slot').appendChild(megaEditor({
        layout: 'columns', columns: [{}],
        cta: { enabled: true, title: 'Not sure which fits?', text: 'Get a free consultation and we’ll map the right solution to your business.', button: 'Book a Free Consultation →' }
      }));
      var first = li.querySelector('.mb-col');
      if (first) first.open = true;
    }
    if (t.matches('[data-m="layout"]')) t.closest('.mb-mega').classList.toggle('is-tiles', t.value === 'tiles');
    if (t.matches('[data-m="cta-enabled"]')) t.closest('.mb-mega').querySelector('.mb-cta-fields').hidden = !t.checked;
    var col = t.closest('.mb-col');
    if (col) syncColumn(col);
    refresh();
  });

  // ── add items ──
  function addItem(label, url) {
    var li = makeRow({ id: uid(), label: label, url: url, visible: true, new_tab: false, mega: null }, 0);
    list.appendChild(li);
    refresh();
    markDirty();
    li.classList.add('mb-new');
    setTimeout(function () { li.classList.remove('mb-new'); }, 1700);
    li.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
  }
  var pageSel = document.getElementById('mbPage');
  document.getElementById('mbAddPage').addEventListener('click', function () {
    var opt = pageSel.options[pageSel.selectedIndex];
    if (opt) addItem(opt.getAttribute('data-label'), opt.value);
  });
  var linkLabel = document.getElementById('mbLinkLabel');
  var linkUrl = document.getElementById('mbLinkUrl');
  var addErr = document.getElementById('mbAddErr');
  document.getElementById('mbAddLink').addEventListener('click', function () {
    var label = linkLabel.value.trim(), url = linkUrl.value.trim();
    var msg = '';
    if (!label) msg = 'Give the link a label.';
    else if (url && !/^(\/(?!\/)|#[A-Za-z][\w-]*$|https?:\/\/[^\/\s]|mailto:|tel:)\S*$/i.test(url)) msg = 'Use a page path like /pricing, a #section, or a full https:// address.';
    addErr.textContent = msg;
    addErr.hidden = !msg;
    if (msg) { (label ? linkUrl : linkLabel).focus(); return; }
    addItem(label, url);
    linkLabel.value = '';
    linkUrl.value = '';
  });

  // ── save ──
  form.addEventListener('submit', function (e) {
    var bad = rows().filter(function (li) { return !field(li, 'label').value.trim(); })[0];
    if (bad) {
      e.preventDefault();
      togglePanel(bad, true);
      field(bad, 'label').focus();
      return;
    }
    document.getElementById('menuJson').value = JSON.stringify(serialize());
    dirty = false;
  });
  window.addEventListener('beforeunload', function (e) {
    if (dirty) { e.preventDefault(); e.returnValue = ''; }
  });

  addRows(data.items || [], 0);
  refresh();
})();
