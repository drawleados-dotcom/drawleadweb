// Drawlead CMS admin: show/hide toggle on every password field.
// Each <input type="password"> gets an eye button inside its right edge that
// switches it between hidden and visible. Without JS the field is a plain
// password input, so nothing depends on this. Styles: .pw-wrap / .pw-toggle
// in admin.css.
(function () {
  var EYE = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>';
  var EYE_OFF = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.6 5.1A9.8 9.8 0 0 1 12 5c6.4 0 10 7 10 7a17.6 17.6 0 0 1-2.9 3.9M6.6 6.6C3.8 8.4 2 12 2 12s3.6 7 10 7a9.6 9.6 0 0 0 5.4-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/><path d="M2 2l20 20"/></svg>';

  function enhance(input) {
    var wrap = document.createElement('span');
    wrap.className = 'pw-wrap';
    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(input);

    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'pw-toggle';
    if (input.id) btn.setAttribute('aria-controls', input.id);
    wrap.appendChild(btn);

    function render(shown) {
      btn.innerHTML = shown ? EYE_OFF : EYE;
      btn.setAttribute('aria-label', shown ? 'Hide password' : 'Show password');
      btn.setAttribute('aria-pressed', shown ? 'true' : 'false');
      btn.title = shown ? 'Hide password' : 'Show password';
    }

    // Keep the caret in the field when the button is clicked with a mouse;
    // keyboard users still reach the button with Tab.
    btn.addEventListener('mousedown', function (e) { e.preventDefault(); });
    btn.addEventListener('click', function () {
      var shown = input.type === 'password';
      input.type = shown ? 'text' : 'password';
      render(shown);
    });

    // Submit as a password field again, so the browser never files the
    // value away as ordinary form text.
    if (input.form) {
      input.form.addEventListener('submit', function () {
        input.type = 'password';
        render(false);
      });
    }

    render(false);
  }

  document.querySelectorAll('input[type="password"]').forEach(enhance);
})();
