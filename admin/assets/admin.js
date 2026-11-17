// Drawlead CMS admin — tiny rich text editor (no external libraries).
// Wraps a contenteditable <div> and keeps a hidden <textarea> in sync so
// the surrounding <form> submits real HTML.
(function () {
  function initEditor(root) {
    var body = root.querySelector('.rte-body');
    var hidden = root.querySelector('textarea');
    if (!body || !hidden) return;

    body.innerHTML = hidden.value || '<p></p>';

    function sync() {
      hidden.value = body.innerHTML;
      // Setting .value programmatically doesn't fire a native "input"
      // event, but other scripts (e.g. the SEO analyzer's word-count
      // check) listen for one on this hidden textarea — dispatch it
      // manually so they stay in sync with what the user is typing.
      hidden.dispatchEvent(new Event('input', { bubbles: true }));
    }
    body.addEventListener('input', sync);
    body.addEventListener('blur', sync);

    root.querySelectorAll('.rte-toolbar button').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var cmd = btn.getAttribute('data-cmd');
        body.focus();
        if (cmd === 'createLink') {
          var url = window.prompt('Link URL:', 'https://');
          if (url) document.execCommand('createLink', false, url);
        } else if (cmd === 'insertImage') {
          var imgUrl = window.prompt('Image URL (upload it under Admin → Media first, then paste its URL):', 'https://');
          if (imgUrl) document.execCommand('insertImage', false, imgUrl);
        } else if (cmd === 'formatBlock') {
          document.execCommand('formatBlock', false, btn.getAttribute('data-value'));
        } else {
          document.execCommand(cmd, false, null);
        }
        sync();
      });
    });

    // Make sure the plain form submit (if JS partially fails) still has content.
    root.closest('form').addEventListener('submit', sync);
  }

  document.querySelectorAll('.rte').forEach(initEditor);

  // Live slug preview from a "name"/"title" field, if present.
  var nameField = document.querySelector('[data-slug-source]');
  var slugField = document.querySelector('[data-slug-target]');
  if (nameField && slugField) {
    nameField.addEventListener('input', function () {
      if (slugField.dataset.touched === '1') return;
      slugField.value = nameField.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    });
    slugField.addEventListener('input', function () {
      slugField.dataset.touched = '1';
    });
  }

  // Image live preview — supports one or several image inputs on the same
  // page (e.g. a blog's single featured image, or a case study's desktop/
  // mobile/result images), each paired with the preview <img> inside its
  // own .field wrapper.
  document.querySelectorAll('[data-image-input]').forEach(function (fileInput) {
    var container = fileInput.closest('.field') || document;
    var preview = container.querySelector('[data-image-preview]');
    if (!preview) return;
    fileInput.addEventListener('change', function () {
      if (!fileInput.files || !fileInput.files[0]) return;
      var reader = new FileReader();
      reader.onload = function (ev) {
        preview.src = ev.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(fileInput.files[0]);
    });
  });

  // Page Content tab (admin/page-edit.php): find-on-page across every
  // section's fields, expand/collapse all, and a prompt before leaving with
  // edits that haven't been saved.
  var pcSections = document.querySelectorAll('.pc-section');
  if (pcSections.length) {
    var search = document.getElementById('pcSearch');
    var emptyNote = document.getElementById('pcSearchEmpty');
    var toggleAll = document.querySelector('[data-pc-toggle-all]');
    var openBefore = null;

    function syncToggleLabel() {
      if (!toggleAll) return;
      var allOpen = Array.prototype.every.call(pcSections, function (s) { return s.open || s.hidden; });
      toggleAll.textContent = allOpen ? 'Collapse all' : 'Expand all';
    }

    if (search) {
      search.addEventListener('input', function () {
        var q = search.value.trim().toLowerCase();
        if (q !== '' && openBefore === null) {
          openBefore = Array.prototype.map.call(pcSections, function (s) { return s.open; });
        }
        var anyMatch = false;
        pcSections.forEach(function (section, i) {
          var hits = 0;
          section.querySelectorAll('.pc-item').forEach(function (item) {
            var field = item.querySelector('input[type=text], textarea');
            var hay = ((item.getAttribute('data-pc-text') || '') + ' ' + (field ? field.value : '')).toLowerCase();
            var match = q === '' || hay.indexOf(q) !== -1;
            item.hidden = !match;
            if (match) hits++;
          });
          var name = (section.querySelector('.pc-sec-name') || {}).textContent || '';
          var nameMatch = q !== '' && name.toLowerCase().indexOf(q) !== -1;
          if (nameMatch) {
            section.querySelectorAll('.pc-item').forEach(function (item) { item.hidden = false; });
          }
          section.hidden = q !== '' && hits === 0 && !nameMatch;
          if (q !== '') {
            section.open = hits > 0 || nameMatch;
          } else if (openBefore) {
            section.open = openBefore[i];
          }
          if (!section.hidden) anyMatch = true;
        });
        if (q === '') openBefore = null;
        if (emptyNote) emptyNote.hidden = anyMatch;
        syncToggleLabel();
      });
    }

    if (toggleAll) {
      syncToggleLabel();
      toggleAll.addEventListener('click', function () {
        var open = toggleAll.textContent === 'Expand all';
        pcSections.forEach(function (s) { if (!s.hidden) s.open = open; });
        syncToggleLabel();
      });
      pcSections.forEach(function (s) { s.addEventListener('toggle', syncToggleLabel); });
    }

    // Preview a pasted image URL in its card, like a picked file.
    document.querySelectorAll('[data-pc-img-url]').forEach(function (urlInput) {
      var preview = (urlInput.closest('.field') || document).querySelector('[data-image-preview]');
      if (!preview) return;
      var original = preview.getAttribute('src');
      urlInput.addEventListener('change', function () {
        var v = urlInput.value.trim();
        preview.src = /^(https?:\/\/|\/)\S+$/i.test(v) ? v : original;
      });
    });

    var dirty = false;
    var submitting = false;
    document.querySelectorAll('.pc-form').forEach(function (form) {
      form.addEventListener('input', function () { dirty = true; });
      form.addEventListener('change', function () { dirty = true; });
      form.addEventListener('submit', function () { submitting = true; });
    });
    window.addEventListener('beforeunload', function (e) {
      if (dirty && !submitting) {
        e.preventDefault();
        e.returnValue = '';
      }
    });
  }
})();
