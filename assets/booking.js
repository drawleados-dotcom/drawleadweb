// Drawlead — Free Consultation Call booking popup.
// Vanilla JS, no dependencies: calendar widget, live slot fetching,
// dynamic form rendering with conditional fields, AJAX submit.
(function () {
  var modal = document.getElementById('booking-modal');
  if (!modal) return;

  var csrfToken = modal.dataset.csrf;
  var dialog = modal.querySelector('.booking-dialog');
  var steps = {
    datetime: modal.querySelector('[data-step="datetime"]'),
    form: modal.querySelector('[data-step="form"]'),
    success: modal.querySelector('[data-step="success"]'),
  };

  var calMonthEl = document.getElementById('bcal-month');
  var calDaysEl = document.getElementById('bcal-days');
  var slotsWrap = document.getElementById('bslots');
  var slotsTitle = document.getElementById('bslots-title');
  var slotsGrid = document.getElementById('bslots-grid');
  var slotsEmpty = document.getElementById('bslots-empty');
  var fieldsWrap = document.getElementById('booking-fields');
  var formTitle = document.getElementById('booking-form-title');
  var form = document.getElementById('booking-form');
  var errorBox = document.getElementById('booking-error');
  var submitBtn = document.getElementById('booking-submit-btn');

  var state = {
    availability: null,
    fields: null,
    viewYear: null,
    viewMonth: null, // 0-11
    selectedDate: null, // 'YYYY-MM-DD'
    selectedTime: null, // 'HH:MM'
  };

  function pad(n) { return n < 10 ? '0' + n : '' + n; }
  function isoDate(y, m, d) { return y + '-' + pad(m + 1) + '-' + pad(d); }
  function todayIso() {
    var t = new Date();
    return isoDate(t.getFullYear(), t.getMonth(), t.getDate());
  }

  function fetchJSON(url, opts) {
    return fetch(url, opts).then(function (r) { return r.json(); });
  }

  function ensureAvailability() {
    if (state.availability) return Promise.resolve(state.availability);
    return fetchJSON('/api/booking-availability.php').then(function (a) {
      state.availability = a;
      var t = new Date();
      state.viewYear = t.getFullYear();
      state.viewMonth = t.getMonth();
      return a;
    });
  }

  function ensureFields() {
    if (state.fields) return Promise.resolve(state.fields);
    return fetchJSON('/api/booking-fields.php').then(function (d) {
      state.fields = d.fields || [];
      return state.fields;
    });
  }

  // ── Calendar rendering ──
  var MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

  function renderCalendar() {
    var a = state.availability;
    calMonthEl.textContent = MONTH_NAMES[state.viewMonth] + ' ' + state.viewYear;
    calDaysEl.innerHTML = '';

    var firstDow = new Date(state.viewYear, state.viewMonth, 1).getDay();
    var daysInMonth = new Date(state.viewYear, state.viewMonth + 1, 0).getDate();
    var today = todayIso();

    for (var i = 0; i < firstDow; i++) {
      var blank = document.createElement('div');
      blank.className = 'bcal-day empty';
      calDaysEl.appendChild(blank);
    }

    for (var d = 1; d <= daysInMonth; d++) {
      var iso = isoDate(state.viewYear, state.viewMonth, d);
      var dow = new Date(state.viewYear, state.viewMonth, d).getDay();
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'bcal-day';
      btn.textContent = d;

      var disabled = iso < today
        || (a.range_start && iso < a.range_start)
        || (a.range_end && iso > a.range_end)
        || a.days_of_week.indexOf(dow) === -1;

      if (disabled) {
        btn.disabled = true;
        btn.className += ' disabled';
      } else {
        btn.addEventListener('click', function (clickedIso) {
          return function () { selectDate(clickedIso); };
        }(iso));
      }
      if (iso === state.selectedDate) {
        btn.className += ' selected';
      }
      calDaysEl.appendChild(btn);
    }

    var prevBtn = document.getElementById('bcal-prev');
    var nextBtn = document.getElementById('bcal-next');
    var minMonth = a.range_start ? a.range_start.slice(0, 7) : null;
    var maxMonth = a.range_end ? a.range_end.slice(0, 7) : null;
    var viewMonthStr = state.viewYear + '-' + pad(state.viewMonth + 1);
    prevBtn.disabled = !!(minMonth && viewMonthStr <= minMonth);
    nextBtn.disabled = !!(maxMonth && viewMonthStr >= maxMonth);
  }

  document.getElementById('bcal-prev').addEventListener('click', function () {
    state.viewMonth--;
    if (state.viewMonth < 0) { state.viewMonth = 11; state.viewYear--; }
    renderCalendar();
  });
  document.getElementById('bcal-next').addEventListener('click', function () {
    state.viewMonth++;
    if (state.viewMonth > 11) { state.viewMonth = 0; state.viewYear++; }
    renderCalendar();
  });

  function selectDate(iso) {
    state.selectedDate = iso;
    state.selectedTime = null;
    renderCalendar();

    slotsWrap.hidden = false;
    slotsGrid.innerHTML = '';
    slotsEmpty.hidden = true;
    var d = new Date(iso + 'T00:00:00');
    slotsTitle.textContent = d.toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric' });

    fetchJSON('/api/booking-slots.php?date=' + encodeURIComponent(iso)).then(function (res) {
      var slots = res.slots || [];
      if (!slots.length) {
        slotsEmpty.hidden = false;
        return;
      }
      slots.forEach(function (t) {
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'bslot';
        b.textContent = to12Hour(t);
        b.addEventListener('click', function () { selectTime(iso, t); });
        slotsGrid.appendChild(b);
      });
    });
  }

  function to12Hour(hhmm) {
    var parts = hhmm.split(':');
    var h = parseInt(parts[0], 10);
    var ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12; if (h === 0) h = 12;
    return h + ':' + parts[1] + ' ' + ampm;
  }

  function selectTime(iso, time) {
    state.selectedTime = time;
    ensureFields().then(function (fields) {
      renderForm(fields);
      var d = new Date(iso + 'T00:00:00');
      formTitle.textContent = d.toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric' }) + ' at ' + to12Hour(time);
      showStep('form');
    });
  }

  document.getElementById('booking-back').addEventListener('click', function () {
    showStep('datetime');
  });

  document.getElementById('bslots-back').addEventListener('click', function () {
    slotsWrap.hidden = true;
    state.selectedDate = null;
    state.selectedTime = null;
    renderCalendar();
  });

  // ── Dynamic form rendering ──
  function renderForm(fields) {
    fieldsWrap.innerHTML = '';
    errorBox.hidden = true;

    fields.forEach(function (f) {
      var row = document.createElement('div');
      row.className = 'field';
      row.dataset.fieldKey = f.key;
      if (f.conditional_field_id) {
        row.dataset.condField = f.conditional_field_id;
        row.dataset.condValue = f.conditional_value;
      }

      var label = document.createElement('label');
      label.textContent = f.label + (f.required ? ' *' : '');
      row.appendChild(label);

      var input;
      if (f.type === 'textarea') {
        input = document.createElement('textarea');
        input.rows = 3;
      } else if (f.type === 'select') {
        input = document.createElement('select');
        var optBlank = document.createElement('option');
        optBlank.value = '';
        optBlank.textContent = f.placeholder || 'Select...';
        input.appendChild(optBlank);
        (f.options || []).forEach(function (opt) {
          var o = document.createElement('option');
          o.value = opt; o.textContent = opt;
          input.appendChild(o);
        });
      } else if (f.type === 'radio' || f.type === 'checkbox') {
        input = document.createElement('div');
        input.className = 'booking-choice-group';
        (f.options || []).forEach(function (opt) {
          var wrap = document.createElement('label');
          wrap.className = 'booking-choice';
          var inp = document.createElement('input');
          inp.type = f.type;
          inp.name = f.key + (f.type === 'checkbox' ? '[]' : '');
          inp.value = opt;
          wrap.appendChild(inp);
          wrap.appendChild(document.createTextNode(' ' + opt));
          input.appendChild(wrap);
        });
      } else {
        input = document.createElement('input');
        input.type = f.type === 'phone' ? 'tel' : (f.type === 'date' ? 'date' : f.type);
      }

      if (input.tagName !== 'DIV') {
        input.name = f.key;
        input.id = 'bf-' + f.key;
        if (f.placeholder && input.tagName !== 'SELECT') input.placeholder = f.placeholder;
        if (f.required) input.required = true;
        row.appendChild(input);
      } else {
        row.appendChild(input);
      }

      fieldsWrap.appendChild(row);
    });

    applyConditionalVisibility();
    fieldsWrap.addEventListener('input', applyConditionalVisibility);
    fieldsWrap.addEventListener('change', applyConditionalVisibility);
  }

  function getFieldValue(key) {
    var radios = fieldsWrap.querySelectorAll('input[name="' + key + '"]');
    if (radios.length && radios[0].type === 'radio') {
      for (var i = 0; i < radios.length; i++) if (radios[i].checked) return radios[i].value;
      return '';
    }
    var checks = fieldsWrap.querySelectorAll('input[name="' + key + '[]"]');
    if (checks.length) {
      var vals = [];
      checks.forEach(function (c) { if (c.checked) vals.push(c.value); });
      return vals;
    }
    var el = fieldsWrap.querySelector('#bf-' + CSS.escape(key));
    return el ? el.value : '';
  }

  function applyConditionalVisibility() {
    fieldsWrap.querySelectorAll('[data-cond-field]').forEach(function (row) {
      var parentField = (state.fields || []).find(function (f) { return String(f.id) === row.dataset.condField; });
      var parentVal = parentField ? getFieldValue(parentField.key) : '';
      var match = Array.isArray(parentVal) ? parentVal.indexOf(row.dataset.condValue) !== -1 : parentVal === row.dataset.condValue;
      row.hidden = !match;
      var input = row.querySelector('input,select,textarea');
      if (input && input.hasAttribute('required')) {
        input.disabled = !match; // skip native validation + submission when hidden
      }
    });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    errorBox.hidden = true;

    var payload = { date: state.selectedDate, time: state.selectedTime, csrf_token: csrfToken, fields: {}, website: document.getElementById('booking-honeypot').value };
    (state.fields || []).forEach(function (f) {
      payload.fields[f.key] = getFieldValue(f.key);
    });

    submitBtn.disabled = true;
    submitBtn.textContent = 'Booking…';

    fetchJSON('/api/booking-submit.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    }).then(function (res) {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Confirm Booking →';
      if (!res.success) {
        errorBox.textContent = res.error || 'Something went wrong. Please try again.';
        errorBox.hidden = false;
        return;
      }
      document.getElementById('booking-success-detail').textContent =
        (res.meeting.name ? res.meeting.name + ', your' : 'Your') + ' call is set for ' + res.meeting.date + ' at ' + res.meeting.time + ' (IST).';
      showStep('success');
    }).catch(function () {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Confirm Booking →';
      errorBox.textContent = 'Network error — please check your connection and try again.';
      errorBox.hidden = false;
    });
  });

  function showStep(name) {
    Object.keys(steps).forEach(function (k) { steps[k].hidden = k !== name; });
  }

  // ── Open / close ──
  function openModal() {
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    showStep('datetime');
    state.selectedDate = null;
    state.selectedTime = null;
    slotsWrap.hidden = true;
    ensureAvailability().then(renderCalendar);
  }

  function closeModal() {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-book]');
    if (trigger) {
      e.preventDefault();
      openModal();
      return;
    }
    if (e.target.closest('[data-book-close]')) {
      closeModal();
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
  });
})();
