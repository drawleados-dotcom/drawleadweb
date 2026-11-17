// Drawlead CMS admin — visual page editor (admin/page-edit.php, Page Content tab).
//
// Left: the page's sections (drag to reorder, switch to hide) and their text
// and image fields. Right: the page itself in an iframe (?pc_preview=1), which
// the server tags for us: <!--pc:KEY--> before each text run, data-pc-block on
// one-field elements, data-pc-img on images, data-pc-section on sections.
// Typing updates the preview at once; nothing is saved until Publish, which
// posts only the fields that changed (see pc_save_page() in
// includes/page-content.php).
(function () {
  'use strict';

  var form = document.getElementById('pcEditor');
  if (!form) return;

  var data = {};
  try { data = JSON.parse(document.getElementById('pcData').textContent) || {}; } catch (e) { data = {}; }
  var blocks = data.blocks || {};

  var frame = document.getElementById('pcFrame');
  var stage = document.getElementById('pcStage');
  var wrap = document.getElementById('pcFrameWrap');
  var loading = document.getElementById('pcLoading');
  var panel = document.getElementById('pcPanel');
  var list = document.getElementById('pcList');
  var layoutInput = document.getElementById('pcLayout');
  var publishBtn = document.getElementById('pcPublish');
  var discardBtn = document.getElementById('pcDiscard');
  var statusEl = document.getElementById('pcStatus');
  var statusText = statusEl ? statusEl.querySelector('[data-pc-status-text]') : null;
  var liveText = statusText ? statusText.textContent : '';
  var search = document.getElementById('pcSearch');
  var searchEmpty = document.getElementById('pcSearchEmpty');
  var inputs = Array.prototype.slice.call(form.querySelectorAll('[data-pc-input]'));

  var WIDTHS = { desktop: 1440, tablet: 820, phone: 390 };
  var IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
  var IMAGE_MAX = 5 * 1024 * 1024;
  var URL_RE = /^(https?:\/\/|\/)\S+$/i;

  var device = 'desktop';
  var submitting = false;
  var imageData = {};   // image hash -> data: URL of a picked file, for the preview

  function each(nodes, fn) { Array.prototype.forEach.call(nodes || [], fn); }
  function esc(s) {
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }
  function oneLine(s) { return String(s).replace(/\s+/g, ' ').trim(); }
  function normBlock(s) {
    return String(s).replace(/\r\n?/g, '\n').split('\n').map(oneLine).join('\n').replace(/^\n+|\n+$/g, '');
  }
  function rows() { return Array.prototype.slice.call(form.querySelectorAll('.pc-row')); }
  function sortableRows() { return Array.prototype.filter.call(list ? list.children : [], function (r) { return r.dataset && r.dataset.key; }); }

  // ── Dirty tracking ──────────────────────────────────────────────────────
  function valueOf(el) {
    if (el.type === 'checkbox') return el.checked ? '1' : '';
    if (el.type === 'file') return el.files && el.files.length ? el.files[0].name + ':' + el.files[0].size : '';
    return el.value;
  }
  inputs.forEach(function (el) { el.setAttribute('data-initial', valueOf(el)); });
  function changed(el) { return valueOf(el) !== el.getAttribute('data-initial'); }

  function currentLayout() {
    var order = [], hidden = [];
    sortableRows().forEach(function (row) {
      order.push(row.dataset.key);
      var vis = row.querySelector('[data-pc-visible]');
      if (vis && !vis.checked) hidden.push(row.dataset.key);
    });
    return JSON.stringify({ order: order, hidden: hidden });
  }
  var layoutInitial = currentLayout();

  function isDirty() {
    return currentLayout() !== layoutInitial || inputs.some(changed);
  }

  function refreshState() {
    var dirty = isDirty();
    publishBtn.disabled = !dirty || submitting;
    discardBtn.disabled = !dirty || submitting;
    if (statusEl) statusEl.classList.toggle('is-dirty', dirty);
    if (statusText) statusText.textContent = dirty ? 'Unpublished changes' : liveText;
    rows().forEach(function (row) {
      var vis = row.querySelector('[data-pc-visible]');
      var rowChanged = Array.prototype.some.call(row.querySelectorAll('[data-pc-input]'), changed);
      row.classList.toggle('is-changed', rowChanged);
      if (vis) row.classList.toggle('is-off', !vis.checked);
    });
  }

  // ── Preview sizing ──────────────────────────────────────────────────────
  // The editor fills exactly the window height left below its own top edge,
  // whatever the header, tabs and alerts above it add up to.
  function sizeEditor() {
    if (window.innerWidth <= 1100) { form.style.height = ''; return; }
    var top = form.getBoundingClientRect().top + window.pageYOffset;
    form.style.height = Math.max(460, Math.floor(window.innerHeight - top - 24)) + 'px';
  }

  function fit() {
    sizeEditor();
    var w = WIDTHS[device];
    var availW = Math.max(240, stage.clientWidth - 24);
    var availH = Math.max(240, stage.clientHeight - 24);
    var scale = Math.min(1, availW / w);
    frame.style.width = w + 'px';
    frame.style.height = Math.round(availH / scale) + 'px';
    frame.style.transform = 'scale(' + scale + ')';
    wrap.style.width = Math.round(w * scale) + 'px';
    wrap.style.height = availH + 'px';
    redraw();
  }
  each(form.querySelectorAll('[data-pc-device]'), function (btn) {
    btn.addEventListener('click', function () {
      device = btn.getAttribute('data-pc-device');
      each(form.querySelectorAll('[data-pc-device]'), function (b) {
        var on = b === btn;
        b.classList.toggle('is-active', on);
        b.setAttribute('aria-pressed', on ? 'true' : 'false');
      });
      fit();
    });
  });
  window.addEventListener('resize', fit);

  // ── Preview document ────────────────────────────────────────────────────
  var doc = null, win = null;
  var sectionEls = {}, slots = [], textNodes = {}, blockEls = {}, imgEls = {};
  var hoverBox = null, selBox = null, hoverEl = null, selEl = null;
  var ignoreFocusScroll = false;

  // Hook into the preview as soon as its HTML is parsed — the frame's "load"
  // waits for every image and font, which can take a while on a big page.
  function initPreview() {
    var d;
    try { d = frame.contentDocument; } catch (e) { d = null; }
    if (!d || !d.body || d.__pcReady || d.readyState === 'loading' || d.URL === 'about:blank') return;
    d.__pcReady = true;
    doc = d;
    win = frame.contentWindow;
    if (loading) loading.hidden = true;

    sectionEls = {}; slots = []; textNodes = {}; blockEls = {}; imgEls = {};
    each(doc.querySelectorAll('[data-pc-section]'), function (el) {
      sectionEls[el.getAttribute('data-pc-section')] = el;
      var slot = doc.createComment('pc-slot');
      el.parentNode.insertBefore(slot, el);
      slots.push(slot);
      el.setAttribute('data-pc-display', el.style.display || '');
    });
    var walker = doc.createTreeWalker(doc.body, 128 /* NodeFilter.SHOW_COMMENT */, null);
    var c;
    while ((c = walker.nextNode())) {
      if (c.data.indexOf('pc:') !== 0) continue;
      var key = c.data.slice(3);
      var t = c.nextSibling;
      if (!t || t.nodeType !== 3) {
        t = doc.createTextNode('');
        c.parentNode.insertBefore(t, c.nextSibling);
      }
      (textNodes[key] = textNodes[key] || []).push(t);
    }
    each(doc.querySelectorAll('[data-pc-block]'), function (el) {
      var k = el.getAttribute('data-pc-block');
      (blockEls[k] = blockEls[k] || []).push(el);
    });
    each(doc.querySelectorAll('img[data-pc-img]'), function (el) {
      var k = el.getAttribute('data-pc-img');
      (imgEls[k] = imgEls[k] || []).push(el);
    });

    var style = doc.createElement('style');
    style.textContent =
      '.pc-ov{position:fixed;pointer-events:none;z-index:2147483646;border-radius:5px;display:none;box-sizing:border-box}' +
      '.pc-ov-hover{border:2px dashed rgba(50,180,111,.95)}' +
      '.pc-ov-sel{border:2px solid #32b46f;background:rgba(50,180,111,.08);box-shadow:0 0 0 4px rgba(50,180,111,.18)}' +
      '[data-pc-block],img[data-pc-img]{cursor:pointer}';
    (doc.head || doc.body).appendChild(style);
    hoverBox = doc.createElement('div');
    hoverBox.className = 'pc-ov pc-ov-hover';
    selBox = doc.createElement('div');
    selBox.className = 'pc-ov pc-ov-sel';
    doc.body.appendChild(hoverBox);
    doc.body.appendChild(selBox);

    doc.addEventListener('mouseover', function (e) {
      var ed = editableFrom(e.target);
      hoverEl = ed ? ed.el : null;
      drawBox(hoverBox, hoverEl);
    }, true);
    doc.addEventListener('click', onPreviewClick, true);
    doc.addEventListener('submit', function (e) { e.preventDefault(); e.stopPropagation(); }, true);
    var ticking = false;
    function onScroll() {
      if (ticking) return;
      ticking = true;
      win.requestAnimationFrame(function () { ticking = false; redraw(); });
    }
    win.addEventListener('scroll', onScroll, { passive: true });
    win.addEventListener('resize', onScroll);

    // Unpublished edits survive a preview reload (e.g. switching device size doesn't reload, but the page's own scripts might).
    applyLayoutToPreview();
    inputs.forEach(function (el) { if (changed(el)) pushToPreview(el); });
  }

  frame.addEventListener('load', initPreview);
  frame.addEventListener('mouseleave', function () { hoverEl = null; drawBox(hoverBox, null); });
  initPreview();
  setInterval(initPreview, 250); // cheap no-op once the current document is set up

  function drawBox(box, el) {
    if (!box) return;
    if (!el || !el.isConnected) { box.style.display = 'none'; return; }
    var r = el.getBoundingClientRect();
    if (!r.width && !r.height) { box.style.display = 'none'; return; }
    box.style.display = 'block';
    box.style.left = (r.left - 4) + 'px';
    box.style.top = (r.top - 4) + 'px';
    box.style.width = (r.width + 8) + 'px';
    box.style.height = (r.height + 8) + 'px';
  }
  function redraw() { drawBox(hoverBox, hoverEl); drawBox(selBox, selEl); }

  function directMarker(el) {
    for (var n = el.firstChild; n; n = n.nextSibling) {
      if (n.nodeType === 8 && n.data.indexOf('pc:') === 0) return n.data.slice(3);
    }
    return null;
  }

  // What the pointer is on: an image, a one-field element, or an element that
  // directly holds a marked text run (checking a couple of levels up).
  function editableFrom(node) {
    var el = node && node.nodeType === 3 ? node.parentNode : node;
    if (!el || !el.closest) return null;
    var img = el.closest('img[data-pc-img]');
    if (img) return { item: 'img:' + img.getAttribute('data-pc-img'), el: img };
    var b = el.closest('[data-pc-block]');
    if (b) return { item: 'text:' + b.getAttribute('data-pc-block'), el: b };
    for (var i = 0, cur = el; cur && i < 3 && cur !== doc.body; i++, cur = cur.parentElement) {
      var k = directMarker(cur);
      if (k) return { item: 'text:' + k, el: cur };
    }
    return null;
  }

  function onPreviewClick(e) {
    // Links would navigate away and booking buttons open a modal; other
    // clicks (tabs, sliders) still work so hidden content can be reached.
    var a = e.target.closest && e.target.closest('a[href], [data-book], button[type=submit], input[type=submit]');
    if (a) {
      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();
    }
    var ed = editableFrom(e.target);
    if (!ed) return;
    selEl = ed.el;
    drawBox(selBox, selEl);
    revealItem(ed.item, true);
  }

  function previewElFor(item) {
    var parts = item.split(':');
    var type = parts[0], key = parts[1];
    if (type === 'img') return (imgEls[key] || [])[0] || null;
    if (blockEls[key]) return blockEls[key][0];
    var t = (textNodes[key] || [])[0];
    return t ? t.parentElement : null;
  }

  function scrollPreviewTo(el, where) {
    if (!el || !win) return;
    var r = el.getBoundingClientRect();
    var y = win.pageYOffset + r.top - (where === 'top' ? 0 : win.innerHeight / 3);
    try { win.scrollTo({ top: Math.max(0, y), behavior: 'smooth' }); } catch (x) { win.scrollTo(0, Math.max(0, y)); }
  }

  function scrollPanelTo(node) {
    if (!panel || !node) return;
    var pr = panel.getBoundingClientRect(), nr = node.getBoundingClientRect();
    var top = panel.scrollTop + nr.top - pr.top - panel.clientHeight / 3;
    try { panel.scrollTo({ top: Math.max(0, top), behavior: 'smooth' }); } catch (x) { panel.scrollTop = top; }
  }

  // Open the field for a preview item in the panel and focus it.
  function revealItem(item, focus) {
    var node = form.querySelector('[data-pc-item="' + item + '"]');
    if (!node) return;
    if (search && search.value) { search.value = ''; applySearch(); }
    var row = node.closest('.pc-row');
    if (row) openRow(row, false);
    node.classList.remove('is-flash');
    void node.offsetWidth;
    node.classList.add('is-flash');
    scrollPanelTo(node);
    var field = node.querySelector('textarea, input[type=text]');
    if (focus && field) {
      ignoreFocusScroll = true;
      field.focus({ preventScroll: true });
      ignoreFocusScroll = false;
    }
  }

  // Focusing a field outlines (and scrolls to) its element in the preview.
  form.addEventListener('focusin', function (e) {
    var node = e.target.closest && e.target.closest('[data-pc-item]');
    if (!node || !doc) return;
    var el = previewElFor(node.getAttribute('data-pc-item'));
    if (!el) return;
    selEl = el;
    if (!ignoreFocusScroll) {
      scrollPreviewTo(el, 'center');
      setTimeout(redraw, 450);
    }
    drawBox(selBox, selEl);
  });

  // ── Live edits ──────────────────────────────────────────────────────────
  function effective(el) {
    var v = el.getAttribute('data-pc-type') === 'block' ? normBlock(el.value) : oneLine(el.value);
    return v === '' ? (el.getAttribute('data-pc-original') || '') : v;
  }

  function blockHtml(key, value) {
    var b = blocks[key] || { c: [], br: null };
    var kids = b.c || [];
    var out = '', k = 0;
    value.split(/(\[[^\[\]\n]*\]|\n)/).forEach(function (t) {
      if (t === '') return;
      if (t === '\n') {
        out += b.br || '<br>';
      } else if (kids.length && t.length >= 2 && t.charAt(0) === '[' && t.charAt(t.length - 1) === ']') {
        var c = kids[Math.min(k, kids.length - 1)];
        k++;
        out += c[0] + esc(t.slice(1, -1)) + c[1];
      } else {
        out += esc(t);
      }
    });
    return out;
  }

  function pushToPreview(el) {
    var type = el.getAttribute('data-pc-type');
    var key = el.getAttribute('data-pc-key');
    if (type === 'text') {
      var v = effective(el);
      (textNodes[key] || []).forEach(function (t) {
        var m = /^(\s*)[\s\S]*?(\s*)$/.exec(t.nodeValue);
        t.nodeValue = (m ? m[1] : '') + v + (m ? m[2] : '');
      });
    } else if (type === 'block') {
      var html = blockHtml(key, effective(el));
      (blockEls[key] || []).forEach(function (b) { b.innerHTML = html; });
    } else if (type && type.indexOf('img-') === 0) {
      updateImage(key);
    }
    redraw();
  }

  function updateImage(key) {
    var card = form.querySelector('[data-pc-item="img:' + key + '"]');
    if (!card) return;
    var url = card.querySelector('[data-pc-type="img-url"]');
    var alt = card.querySelector('[data-pc-type="img-alt"]');
    var reset = card.querySelector('[data-pc-type="img-reset"]');
    var thumb = card.querySelector('[data-pc-thumb]');
    var src = thumb.getAttribute('data-pc-src-saved');
    if (reset && reset.checked) src = thumb.getAttribute('data-pc-src-original');
    else if (imageData[key]) src = imageData[key];
    else if (url && URL_RE.test(url.value.trim())) src = url.value.trim();
    if (thumb.getAttribute('src') !== src) thumb.setAttribute('src', src);
    var altText = alt ? (oneLine(alt.value) || alt.getAttribute('data-pc-original') || '') : null;
    (imgEls[key] || []).forEach(function (img) {
      if (img.getAttribute('src') !== src) {
        img.removeAttribute('srcset');
        img.setAttribute('src', src);
      }
      if (altText !== null) img.setAttribute('alt', altText);
    });
  }

  function onFile(el) {
    var key = el.getAttribute('data-pc-key');
    var card = el.closest('[data-pc-item]');
    var nameEl = card ? card.querySelector('[data-pc-file-name]') : null;
    var f = el.files && el.files[0];
    if (nameEl && !nameEl.hasAttribute('data-saved')) nameEl.setAttribute('data-saved', nameEl.textContent);
    delete imageData[key];
    if (f && (IMAGE_TYPES.indexOf(f.type) === -1 || f.size > IMAGE_MAX)) {
      window.alert('Please use a JPG, PNG, WEBP or GIF image under 5MB.');
      el.value = '';
      f = null;
    }
    if (nameEl) nameEl.textContent = f ? f.name : nameEl.getAttribute('data-saved');
    if (!f) { updateImage(key); return; }
    var reader = new FileReader();
    reader.onload = function (ev) { imageData[key] = ev.target.result; updateImage(key); };
    reader.readAsDataURL(f);
  }

  form.addEventListener('input', function (e) {
    var el = e.target;
    if (!el.hasAttribute || !el.hasAttribute('data-pc-input') || el.type === 'file') return;
    pushToPreview(el);
    refreshState();
  });
  form.addEventListener('change', function (e) {
    var el = e.target;
    if (!el.hasAttribute) return;
    if (el.hasAttribute('data-pc-input')) {
      if (el.type === 'file') onFile(el); else pushToPreview(el);
    }
    refreshState();
  });
  // Enter in a one-line field would submit (publish) the whole form.
  form.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && e.target.tagName === 'INPUT' && e.target.type !== 'submit' && e.target.type !== 'button') {
      e.preventDefault();
    }
  });

  // ── Sections: open, show/hide, reorder ──────────────────────────────────
  function setOpen(row, open) {
    row.classList.toggle('is-open', open);
    var body = row.querySelector('.pc-row-body');
    if (body) body.hidden = !open;
    var t = row.querySelector('.pc-row-toggle');
    if (t) t.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  function openRow(row, scrollPreview) {
    rows().forEach(function (r) { if (r !== row && r.classList.contains('is-open')) setOpen(r, false); });
    setOpen(row, true);
    if (scrollPreview) {
      var el = sectionEls[row.dataset.key];
      if (!el) {
        var first = row.querySelector('[data-pc-item]');
        el = first ? previewElFor(first.getAttribute('data-pc-item')) : null;
      }
      if (el && el.style.display !== 'none') scrollPreviewTo(el, 'top');
    }
  }
  each(form.querySelectorAll('.pc-row-toggle'), function (btn) {
    btn.addEventListener('click', function () {
      var row = btn.closest('.pc-row');
      if (row.classList.contains('is-open')) setOpen(row, false);
      else openRow(row, true);
    });
  });

  function applyVisibility(key, visible) {
    var el = sectionEls[key];
    if (!el) return;
    if (visible) el.style.display = el.getAttribute('data-pc-display') || '';
    else el.style.setProperty('display', 'none', 'important');
  }

  function applyLayoutToPreview() {
    if (!doc) return;
    var rs = sortableRows();
    if (rs.length === slots.length) {
      rs.forEach(function (row, i) {
        var el = sectionEls[row.dataset.key];
        var slot = slots[i];
        if (el && slot && slot.parentNode) slot.parentNode.insertBefore(el, slot.nextSibling);
      });
    }
    rs.forEach(function (row) {
      var vis = row.querySelector('[data-pc-visible]');
      applyVisibility(row.dataset.key, !vis || vis.checked);
    });
    redraw();
  }

  function layoutChanged(row) {
    applyLayoutToPreview();
    refreshState();
    if (row && doc) {
      var el = sectionEls[row.dataset.key];
      if (el && el.style.display !== 'none') scrollPreviewTo(el, 'top');
    }
  }

  each(form.querySelectorAll('[data-pc-visible]'), function (cb) {
    cb.addEventListener('change', function () {
      var row = cb.closest('.pc-row');
      applyVisibility(row.dataset.key, cb.checked);
      refreshState();
    });
  });

  var dragRow = null;
  if (list) {
    each(list.querySelectorAll('.pc-drag'), function (handle) {
      var row = handle.closest('.pc-row');
      handle.addEventListener('mousedown', function () { row.draggable = true; });
      handle.addEventListener('touchstart', function () { row.draggable = true; }, { passive: true });
      handle.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowUp' && row.previousElementSibling) {
          list.insertBefore(row, row.previousElementSibling);
        } else if (e.key === 'ArrowDown' && row.nextElementSibling) {
          list.insertBefore(row, row.nextElementSibling.nextSibling);
        } else {
          return;
        }
        e.preventDefault();
        handle.focus();
        layoutChanged(row);
      });
    });
    list.addEventListener('dragstart', function (e) {
      var row = e.target.closest && e.target.closest('.pc-row');
      if (!row || !row.draggable) { e.preventDefault(); return; }
      dragRow = row;
      row.classList.add('is-dragging');
      e.dataTransfer.effectAllowed = 'move';
      try { e.dataTransfer.setData('text/plain', row.dataset.key); } catch (x) { /* old browsers */ }
    });
    list.addEventListener('dragover', function (e) {
      if (!dragRow) return;
      e.preventDefault();
      var row = e.target.closest && e.target.closest('.pc-row');
      if (!row || row === dragRow || row.parentNode !== list) return;
      var r = row.getBoundingClientRect();
      list.insertBefore(dragRow, e.clientY > r.top + r.height / 2 ? row.nextSibling : row);
    });
    list.addEventListener('drop', function (e) { if (dragRow) e.preventDefault(); });
    list.addEventListener('dragend', function () {
      if (!dragRow) return;
      var row = dragRow;
      row.classList.remove('is-dragging');
      row.draggable = false;
      dragRow = null;
      layoutChanged(row);
    });
    document.addEventListener('mouseup', function () {
      each(list.children, function (r) { if (r !== dragRow) r.draggable = false; });
    });
  }

  // ── Search ──────────────────────────────────────────────────────────────
  function applySearch() {
    var q = search.value.trim().toLowerCase();
    var any = false;
    rows().forEach(function (row) {
      var hits = 0;
      each(row.querySelectorAll('[data-pc-item]'), function (item) {
        var f = item.querySelector('textarea, input[type=text]');
        var hay = (item.getAttribute('data-pc-search') || '') + ' ' + (f ? f.value.toLowerCase() : '');
        var match = q === '' || hay.indexOf(q) !== -1;
        item.hidden = !match;
        if (match) hits++;
      });
      var name = ((row.querySelector('.pc-row-name') || {}).textContent || '').toLowerCase();
      var nameHit = q !== '' && name.indexOf(q) !== -1;
      if (nameHit) each(row.querySelectorAll('[data-pc-item]'), function (i) { i.hidden = false; });
      row.hidden = q !== '' && hits === 0 && !nameHit;
      setOpen(row, q !== '' && (hits > 0 || nameHit));
      if (!row.hidden) any = true;
    });
    if (searchEmpty) searchEmpty.hidden = any;
  }
  if (search) search.addEventListener('input', applySearch);

  // ── Publish / discard ───────────────────────────────────────────────────
  form.addEventListener('submit', function (e) {
    if (submitting || !isDirty()) { e.preventDefault(); return; }
    var bad = inputs.filter(function (el) {
      return el.getAttribute('data-pc-type') === 'img-url' && el.value.trim() !== '' && !URL_RE.test(el.value.trim());
    });
    if (bad.length) {
      e.preventDefault();
      window.alert('Image URLs must start with https:// or / (copy them from Admin → Media).');
      var item = bad[0].closest('[data-pc-item]');
      if (item) revealItem(item.getAttribute('data-pc-item'), false);
      bad[0].focus();
      return;
    }
    layoutInput.value = currentLayout();
    // Post only what changed: keeps big pages under the server's field limit.
    inputs.forEach(function (el) { if (!changed(el)) el.disabled = true; });
    submitting = true;
    publishBtn.disabled = true;
    discardBtn.disabled = true;
    publishBtn.textContent = 'Publishing…';
  });

  discardBtn.addEventListener('click', function () {
    if (!isDirty() || !window.confirm('Discard all unpublished changes on this page?')) return;
    submitting = true;
    window.location.href = window.location.pathname + '?id=' + encodeURIComponent(new URLSearchParams(window.location.search).get('id') || '') + '&tab=content';
  });

  window.addEventListener('beforeunload', function (e) {
    if (!submitting && isDirty()) {
      e.preventDefault();
      e.returnValue = '';
    }
  });
  // Coming back with the browser's Back button can restore a half-submitted form.
  window.addEventListener('pageshow', function (e) { if (e.persisted) window.location.reload(); });

  var flash = document.querySelector('[data-pc-flash]');
  if (flash) setTimeout(function () { flash.style.display = 'none'; fit(); }, 6000);

  fit();
  refreshState();
})();
