// Drawlead CMS admin — "Choose image" picker. Opens the Media library in a
// dialog: pick any existing image, or upload a new one (it goes into the
// library too) and use it. Data comes from admin/media-api.php.
//
// Markup (see admin/popup-edit.php):
//   <div data-media-picker data-csrf="…">
//     <input type="hidden" name="image" value="…" data-mp-value>
//     <div data-mp-preview><img data-mp-img src="…"></div>
//     <button type="button" data-mp-open>Choose image</button>
//     <button type="button" data-mp-clear>Remove</button>
//   </div>
(function () {
  var pickers = document.querySelectorAll('[data-media-picker]');
  if (!pickers.length) return;

  var API = 'media-api.php';
  var dialog, grid, search, status, useBtn, fileInput, emptyMsg;
  var images = null; // cached library list
  var maxBytes = 0;
  var selected = null; // url
  var activePicker = null;

  function el(tag, cls, text) {
    var n = document.createElement(tag);
    if (cls) n.className = cls;
    if (text) n.textContent = text;
    return n;
  }

  function setStatus(msg, isError) {
    status.textContent = msg || '';
    status.classList.toggle('is-error', !!isError);
  }

  function buildDialog() {
    dialog = el('dialog', 'media-dialog mp-dialog');
    dialog.setAttribute('aria-labelledby', 'mpTitle');
    dialog.innerHTML =
      '<div class="media-dialog-form">' +
        '<div class="media-dialog-head"><h2 id="mpTitle">Media library</h2>' +
          '<button type="button" class="media-dialog-x" data-mp-close aria-label="Close">×</button></div>' +
        '<div class="mp-toolbar">' +
          '<input type="search" class="mp-search" placeholder="Search images…" data-mp-search>' +
          '<label class="btn btn-primary mp-upload">Upload new image' +
            '<input type="file" accept="image/jpeg,image/png,image/webp,image/gif,image/avif" data-mp-file hidden></label>' +
          '<span class="mp-status" data-mp-status aria-live="polite"></span>' +
        '</div>' +
        '<div class="mp-body" data-mp-drop>' +
          '<div class="mp-grid" data-mp-grid></div>' +
          '<div class="mp-empty" data-mp-empty hidden></div>' +
        '</div>' +
        '<div class="media-dialog-foot">' +
          '<span class="mp-hint">Tip: drop an image anywhere in this window to upload it.</span>' +
          '<button type="button" class="btn btn-ghost" data-mp-close>Cancel</button>' +
          '<button type="button" class="btn btn-primary" data-mp-use disabled>Use this image</button>' +
        '</div>' +
      '</div>';
    document.body.appendChild(dialog);

    grid = dialog.querySelector('[data-mp-grid]');
    search = dialog.querySelector('[data-mp-search]');
    status = dialog.querySelector('[data-mp-status]');
    useBtn = dialog.querySelector('[data-mp-use]');
    fileInput = dialog.querySelector('[data-mp-file]');
    emptyMsg = dialog.querySelector('[data-mp-empty]');

    dialog.querySelectorAll('[data-mp-close]').forEach(function (b) {
      b.addEventListener('click', function () { dialog.close(); });
    });
    dialog.addEventListener('click', function (e) {
      if (e.target === dialog) dialog.close(); // backdrop
    });
    search.addEventListener('input', render);
    useBtn.addEventListener('click', applySelection);
    fileInput.addEventListener('change', function () {
      if (fileInput.files && fileInput.files[0]) upload(fileInput.files[0]);
      fileInput.value = '';
    });

    var drop = dialog.querySelector('[data-mp-drop]');
    ['dragenter', 'dragover'].forEach(function (t) {
      dialog.addEventListener(t, function (e) {
        e.preventDefault();
        drop.classList.add('is-drag');
      });
    });
    ['dragleave', 'drop'].forEach(function (t) {
      dialog.addEventListener(t, function (e) {
        e.preventDefault();
        if (t === 'dragleave' && dialog.contains(e.relatedTarget)) return;
        drop.classList.remove('is-drag');
      });
    });
    dialog.addEventListener('drop', function (e) {
      var f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
      if (f) upload(f);
    });
  }

  function render() {
    var q = search.value.trim().toLowerCase();
    grid.innerHTML = '';
    var shown = (images || []).filter(function (img) {
      return !q || img.file.toLowerCase().indexOf(q) !== -1;
    });
    shown.forEach(function (img) {
      var b = el('button', 'mp-item' + (img.url === selected ? ' is-selected' : ''));
      b.type = 'button';
      b.title = img.file;
      b.dataset.url = img.url;
      var thumb = el('span', 'mp-thumb');
      var im = el('img');
      im.src = img.url;
      im.alt = '';
      im.loading = 'lazy';
      thumb.appendChild(im);
      b.appendChild(thumb);
      b.appendChild(el('span', 'mp-name', img.file));
      b.appendChild(el('span', 'mp-meta', (img.w ? img.w + '×' + img.h + ' · ' : '') + img.size));
      b.addEventListener('click', function () { select(img.url); });
      b.addEventListener('dblclick', function () { select(img.url); applySelection(); });
      grid.appendChild(b);
    });
    emptyMsg.hidden = shown.length > 0;
    emptyMsg.textContent = images && images.length
      ? 'No images match "' + search.value.trim() + '".'
      : 'Your media library is empty. Upload an image to get started.';
  }

  function select(url) {
    selected = url;
    useBtn.disabled = !url;
    grid.querySelectorAll('.mp-item').forEach(function (b) {
      b.classList.toggle('is-selected', b.dataset.url === url);
    });
  }

  function load() {
    setStatus('Loading images…');
    return fetch(API, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
      .then(function (res) {
        if (!res.ok) throw new Error(res.d.error || 'Could not load the media library.');
        images = res.d.images || [];
        maxBytes = res.d.max_bytes || 0;
        setStatus('');
        render();
      })
      .catch(function (err) {
        setStatus(err.message || 'Could not load the media library.', true);
      });
  }

  function upload(file) {
    if (!/^image\/(jpeg|png|webp|gif|avif)$/.test(file.type)) {
      setStatus(file.name + ': use a JPG, PNG, WEBP, GIF, or AVIF image.', true);
      return;
    }
    if (maxBytes && file.size > maxBytes) {
      setStatus(file.name + ': larger than the ' + Math.round(maxBytes / 1048576) + ' MB limit.', true);
      return;
    }
    var fd = new FormData();
    fd.append('csrf_token', activePicker.dataset.csrf || '');
    fd.append('file', file);
    setStatus('Uploading ' + file.name + '…');
    fetch(API, { method: 'POST', body: fd, credentials: 'same-origin', headers: { Accept: 'application/json' } })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
      .then(function (res) {
        if (!res.ok || !res.d.image) throw new Error(res.d.error || 'Upload failed. Please try again.');
        images = [res.d.image].concat(images || []);
        search.value = '';
        selected = res.d.image.url;
        render();
        select(selected);
        grid.scrollTop = 0;
        setStatus('Uploaded — it\'s selected. Click "Use this image".');
      })
      .catch(function (err) {
        setStatus(err.message || 'Upload failed. Please try again.', true);
      });
  }

  function applySelection() {
    if (!selected || !activePicker) return;
    setValue(activePicker, selected);
    dialog.close();
  }

  function setValue(picker, url) {
    picker.querySelector('[data-mp-value]').value = url;
    var img = picker.querySelector('[data-mp-img]');
    img.src = url;
    picker.classList.toggle('has-image', !!url);
    picker.dispatchEvent(new CustomEvent('mediapicker:change', { bubbles: true, detail: { url: url } }));
  }

  pickers.forEach(function (picker) {
    picker.querySelectorAll('[data-mp-open]').forEach(function (b) { b.addEventListener('click', open); });
    function open() {
      if (!dialog) buildDialog();
      activePicker = picker;
      // The preview's src is always a /uploads/... path for an uploaded
      // image (older values store a bare filename), so match on that.
      var current = picker.querySelector('[data-mp-value]').value ? picker.querySelector('[data-mp-img]').getAttribute('src') : '';
      selected = current && current.charAt(0) === '/' ? current : null;
      useBtn.disabled = !selected;
      search.value = '';
      setStatus('');
      if (typeof dialog.showModal === 'function') dialog.showModal(); else dialog.setAttribute('open', '');
      if (images === null) load(); else render();
      search.focus();
    }
    var clear = picker.querySelector('[data-mp-clear]');
    if (clear) {
      clear.addEventListener('click', function () {
        picker.querySelector('[data-mp-value]').value = '';
        picker.classList.remove('has-image');
        picker.querySelector('[data-mp-img]').removeAttribute('src');
      });
    }
  });
})();
