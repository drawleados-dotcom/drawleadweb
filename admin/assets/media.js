// Drawlead CMS admin — Media library: drag-and-drop upload, one-click URL
// copying, and filename search. Loaded only by admin/media.php.
(function () {
  function fmt(bytes) {
    return bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(bytes / 1024)) + ' KB';
  }

  // ── Upload ──
  var form = document.querySelector('[data-media-upload]');
  if (form) {
    var zone = form.querySelector('[data-dropzone]');
    var input = form.querySelector('[data-media-input]');
    var status = form.querySelector('[data-dropzone-status]');
    var maxBytes = parseInt(form.getAttribute('data-max-bytes'), 10) || 0;
    var maxFiles = parseInt(form.getAttribute('data-max-files'), 10) || 0;
    var postLimit = parseInt(form.getAttribute('data-post-limit'), 10) || 0;

    var fail = function (msg) {
      status.textContent = msg;
      status.classList.add('is-error');
      input.value = '';
    };

    // Checks the same limits the server enforces, so an oversized pick
    // fails instantly instead of after a long upload.
    var send = function () {
      var files = input.files;
      if (!files || !files.length) return;
      status.classList.remove('is-error');
      if (maxFiles && files.length > maxFiles) {
        return fail('You can upload up to ' + maxFiles + ' images at a time.');
      }
      var total = 0;
      for (var i = 0; i < files.length; i++) {
        if (maxBytes && files[i].size > maxBytes) {
          return fail(files[i].name + ' is ' + fmt(files[i].size) + ' — the limit is ' + fmt(maxBytes) + ' per image.');
        }
        total += files[i].size;
      }
      if (postLimit && total > postLimit) {
        return fail('Those images add up to ' + fmt(total) + ' — upload under ' + fmt(postLimit) + ' at a time.');
      }
      status.textContent = 'Uploading ' + files.length + (files.length === 1 ? ' image' : ' images') + '…';
      zone.classList.add('is-busy');
      form.submit();
    };

    input.addEventListener('change', send);

    zone.addEventListener('dragover', function (e) {
      e.preventDefault();
      zone.classList.add('is-over');
    });
    zone.addEventListener('dragleave', function (e) {
      if (!zone.contains(e.relatedTarget)) zone.classList.remove('is-over');
    });
    zone.addEventListener('drop', function (e) {
      e.preventDefault();
      zone.classList.remove('is-over');
      if (!e.dataTransfer || !e.dataTransfer.files || !e.dataTransfer.files.length) return;
      try {
        input.files = e.dataTransfer.files;
      } catch (err) {
        return fail('This browser can’t take dropped files — click the box to choose them instead.');
      }
      send();
    });

    // A file dropped just outside the box would otherwise make the browser
    // navigate away to display it.
    window.addEventListener('dragover', function (e) { e.preventDefault(); });
    window.addEventListener('drop', function (e) { e.preventDefault(); });
  }

  // ── Copy URL / path ──
  function flashCopied(btn) {
    if (!btn.getAttribute('data-label')) btn.setAttribute('data-label', btn.textContent);
    btn.textContent = 'Copied ✓';
    btn.classList.add('is-copied');
    clearTimeout(btn._copyTimer);
    btn._copyTimer = setTimeout(function () {
      btn.textContent = btn.getAttribute('data-label');
      btn.classList.remove('is-copied');
    }, 1600);
  }

  function legacyCopy(text, btn) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly', '');
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    var ok = false;
    try { ok = document.execCommand('copy'); } catch (err) { ok = false; }
    document.body.removeChild(ta);
    if (ok) flashCopied(btn);
    else window.prompt('Copy this:', text);
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-copy]');
    if (!btn) return;
    e.preventDefault();
    var text = btn.getAttribute('data-copy');
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(
        function () { flashCopied(btn); },
        function () { legacyCopy(text, btn); }
      );
    } else {
      legacyCopy(text, btn);
    }
  });

  // Clicking into a URL box selects all of it, for a manual Ctrl+C.
  document.querySelectorAll('[data-copy-source]').forEach(function (el) {
    el.addEventListener('focus', function () { el.select(); });
    el.addEventListener('mouseup', function (e) { e.preventDefault(); });
  });

  // ── Filters: Show (Blogs / Pages / …) → one blog or page → search ──
  // Each card's data-refs lists tokens like "blog blog:12 page page:3" or
  // "unused"; the dropdown values are those same tokens. The choice is kept
  // in the URL hash, so it survives the reload after a delete or rename.
  var search = document.querySelector('[data-media-search]');
  var typeSel = document.querySelector('[data-media-type]');
  if (search && typeSel) {
    var items = document.querySelectorAll('[data-media-item]');
    var none = document.querySelector('[data-media-noresults]');
    var count = document.querySelector('[data-media-count]');
    var recordSels = {};
    document.querySelectorAll('[data-media-record]').forEach(function (s) {
      recordSels[s.getAttribute('data-media-record')] = s;
    });

    var apply = function () {
      var type = typeSel.value;
      Object.keys(recordSels).forEach(function (k) { recordSels[k].hidden = k !== type; });
      var rec = recordSels[type] ? recordSels[type].value : '';
      var token = rec || type;

      var q = search.value.trim().toLowerCase().replace(/\s+/g, ' ');
      // File names are slugs, so "team photo" should also find team-photo.jpg.
      var slugQ = q.replace(/ /g, '-');
      var shown = 0;
      items.forEach(function (it) {
        var refs = ' ' + (it.getAttribute('data-refs') || '') + ' ';
        var hay = it.getAttribute('data-search') || '';
        var hit = (!token || refs.indexOf(' ' + token + ' ') !== -1)
          && (!q || hay.indexOf(q) !== -1 || hay.indexOf(slugQ) !== -1);
        it.hidden = !hit;
        if (hit) shown++;
      });
      if (none) none.hidden = shown !== 0;
      if (count) {
        var filtered = token || q;
        count.hidden = !filtered;
        count.textContent = 'Showing ' + shown + ' of ' + items.length + ' images';
      }
      var hash = token ? '#show=' + encodeURIComponent(token) : '';
      try { history.replaceState(null, '', location.pathname + location.search + hash); } catch (err) {}
      // A form post drops the hash; giving the forms it means the redirect
      // back here (which has no hash of its own) lands on the same filter.
      document.querySelectorAll('form[method="post"]').forEach(function (f) {
        f.setAttribute('action', 'media.php' + hash);
      });
    };

    // Restore a filter from the URL hash (#show=blog:12).
    var m = /[#&]show=([^&]+)/.exec(location.hash);
    if (m) {
      var saved = decodeURIComponent(m[1]);
      var savedType = saved.split(':')[0];
      if (typeSel.querySelector('option[value="' + savedType + '"]')) {
        typeSel.value = savedType;
        var rs = recordSels[savedType];
        if (rs && saved !== savedType && rs.querySelector('option[value="' + saved + '"]')) rs.value = saved;
      }
    }

    typeSel.addEventListener('change', function () {
      if (recordSels[typeSel.value]) recordSels[typeSel.value].value = '';
      apply();
    });
    Object.keys(recordSels).forEach(function (k) { recordSels[k].addEventListener('change', apply); });
    search.addEventListener('input', apply);
    apply();
  }

  // ── Image details popup: rename the file (which changes its URL) ──
  var dialog = document.getElementById('mediaDialog');
  if (dialog && typeof dialog.showModal === 'function') {
    var dForm = dialog.querySelector('[data-media-rename]');
    var baseUrl = dForm.getAttribute('data-base-url') || '';
    var q = function (sel) { return dialog.querySelector(sel); };
    var nameIn = q('[data-d-name]'), urlIn = q('[data-d-url]'), ext = q('[data-d-ext]');
    var saveBtn = q('[data-d-save]'), copyBtn = q('[data-d-copy]'), note = q('[data-d-note]');
    var current = null; // { base, ext, folder, url, used }

    // Same rules as media_slug() in admin/media.php.
    var slug = function (s) {
      return String(s).trim().replace(/\.(jpe?g|png|webp|gif|avif)$/i, '').toLowerCase()
        .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 80).replace(/-+$/, '');
    };
    var urlFor = function (base) { return baseUrl + current.folder + base + current.ext; };

    var sync = function (from) {
      if (!current) return;
      var base;
      if (from === 'url') {
        var last = urlIn.value.split('?')[0].split('#')[0].replace(/\/+$/, '').split('/').pop() || '';
        base = slug(decodeURIComponent(last.replace(/%(?![0-9a-f]{2})/gi, '%25')));
        nameIn.value = base;
      } else {
        base = slug(nameIn.value);
        urlIn.value = urlFor(base || '…');
      }
      var changed = base !== '' && base !== current.base;
      if (saveBtn) saveBtn.disabled = !changed;
      copyBtn.disabled = changed;
      copyBtn.title = changed ? 'Save the new name first' : '';
      if (changed) {
        note.hidden = false;
        note.textContent = 'Saving gives this image a new URL: ' + urlFor(base) + '. '
          + (current.used ? 'Everywhere it’s used on the site (' + current.used + ') is updated automatically. ' : '')
          + 'Copies of the old URL pasted outside this site (emails, social posts) will stop working.';
      } else {
        note.hidden = true;
      }
    };

    var open = function (item) {
      var file = item.getAttribute('data-file');
      var dot = file.lastIndexOf('.');
      var path = item.getAttribute('data-path');
      current = {
        base: file.slice(0, dot),
        ext: file.slice(dot).toLowerCase(),
        folder: path.slice(0, path.lastIndexOf('/') + 1),
        url: item.getAttribute('data-url'),
        used: item.getAttribute('data-used') || ''
      };
      q('[data-d-file]').value = file;
      q('[data-d-dir]').value = item.getAttribute('data-dir');
      q('[data-d-img]').src = path;
      q('[data-d-open]').href = path;
      q('[data-d-meta]').textContent = item.getAttribute('data-meta');
      var used = q('[data-d-used]');
      used.textContent = current.used ? 'Used in ' + current.used : 'Not used on any page yet';
      used.classList.toggle('is-plain', !current.used);
      ext.textContent = current.ext;
      nameIn.value = current.base;
      urlIn.value = current.url;
      copyBtn.setAttribute('data-copy', current.url);
      sync('name');
      dialog.showModal();
      if (!nameIn.readOnly) { nameIn.focus(); nameIn.select(); }
    };

    document.addEventListener('click', function (e) {
      var trigger = e.target.closest('[data-media-open]');
      if (!trigger) return;
      var item = trigger.closest('[data-media-item]');
      if (!item) return;
      e.preventDefault();
      open(item);
    });
    nameIn.addEventListener('input', function () { sync('name'); });
    // Show the name as it will be saved ("Team Photo" → "team-photo").
    nameIn.addEventListener('blur', function () { if (slug(nameIn.value)) nameIn.value = slug(nameIn.value); sync('name'); });
    urlIn.addEventListener('input', function () { sync('url'); });
    urlIn.addEventListener('blur', function () { sync('name'); }); // tidy the URL back to its fixed folder
    dialog.querySelectorAll('[data-media-close]').forEach(function (b) {
      b.addEventListener('click', function () { dialog.close(); });
    });
    // A click on the dimmed backdrop (the dialog element itself) closes it.
    dialog.addEventListener('click', function (e) { if (e.target === dialog) dialog.close(); });
    dForm.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && saveBtn && saveBtn.disabled) e.preventDefault();
    });
    dForm.addEventListener('submit', function (e) {
      sync('name');
      if (!saveBtn || saveBtn.disabled) { e.preventDefault(); return; }
      nameIn.value = slug(nameIn.value);
      saveBtn.disabled = true;
      saveBtn.textContent = 'Renaming…';
    });

    var renamed = document.querySelector('[data-media-renamed]');
    if (renamed) renamed.scrollIntoView({ block: 'center' });
  }
})();
