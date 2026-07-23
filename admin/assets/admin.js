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
          var imgUrl = window.prompt('Image URL (upload the image first, then paste its URL):', 'https://');
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

  // Featured image live preview.
  var fileInput = document.querySelector('[data-image-input]');
  if (fileInput) {
    fileInput.addEventListener('change', function () {
      var preview = document.querySelector('[data-image-preview]');
      if (!preview || !fileInput.files || !fileInput.files[0]) return;
      var reader = new FileReader();
      reader.onload = function (ev) {
        preview.src = ev.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(fileInput.files[0]);
    });
  }
})();
