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

  // ☰ in the top bar: hide/show the sidebar on desktop; on narrow screens
  // (where it's normally hidden) slide it over the page instead.
  var sideToggle = document.querySelector('[data-side-toggle]');
  var sidebar = document.getElementById('adminSidebar');
  if (sideToggle && sidebar) {
    var narrow = function () { return window.matchMedia('(max-width: 900px)').matches; };
    var isOpen = function () {
      return narrow() ? document.body.classList.contains('sidebar-mobile-open')
                      : !document.body.classList.contains('sidebar-collapsed');
    };
    var sync = function () {
      var open = isOpen();
      sideToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      sideToggle.title = open ? 'Hide menu' : 'Show menu';
    };
    var setOpen = function (open) {
      if (narrow()) document.body.classList.toggle('sidebar-mobile-open', open);
      else document.body.classList.toggle('sidebar-collapsed', !open);
      sync();
      // Let width-dependent layouts (the page editor's preview) refit once the slide ends.
      setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 240);
    };
    sideToggle.addEventListener('click', function () { setOpen(!isOpen()); });
    // …and again when the slide really finishes (a scrollbar can flash mid-way).
    var mainEl = document.querySelector('.main');
    if (mainEl) {
      mainEl.addEventListener('transitionend', function (e) {
        if (e.target === mainEl && e.propertyName === 'margin-left') window.dispatchEvent(new Event('resize'));
      });
    }
    document.addEventListener('click', function (e) {
      if (narrow() && document.body.classList.contains('sidebar-mobile-open')
          && !sidebar.contains(e.target) && !sideToggle.contains(e.target)) {
        setOpen(false);
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && narrow() && document.body.classList.contains('sidebar-mobile-open')) setOpen(false);
    });
    window.addEventListener('resize', sync);
    sync();
  }

})();
