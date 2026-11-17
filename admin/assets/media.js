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

  // ── Search (file name, or where it's used, e.g. "blog", "popup") ──
  var search = document.querySelector('[data-media-search]');
  if (search) {
    var items = document.querySelectorAll('[data-media-item]');
    var none = document.querySelector('[data-media-noresults]');
    search.addEventListener('input', function () {
      var q = search.value.trim().toLowerCase().replace(/\s+/g, ' ');
      // File names are slugs, so "team photo" should also find team-photo.jpg.
      var slugQ = q.replace(/ /g, '-');
      var shown = 0;
      items.forEach(function (it) {
        var hay = it.getAttribute('data-search') || '';
        var hit = !q || hay.indexOf(q) !== -1 || hay.indexOf(slugQ) !== -1;
        it.hidden = !hit;
        if (hit) shown++;
      });
      if (none) none.hidden = shown !== 0;
    });
  }
})();
