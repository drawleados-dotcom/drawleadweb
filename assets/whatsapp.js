// Drawlead — WhatsApp-style lead-capture chat widget.
// Simulated chat only (no real WhatsApp connection) — walks a visitor
// through an admin-configured sequence of questions, then collects a
// phone number and saves the conversation as a lead.
(function () {
  var fab = document.getElementById('wa-fab');
  var panel = document.getElementById('wa-panel');
  if (!fab || !panel) return;

  var closeBtn = document.getElementById('wa-close');
  var body = document.getElementById('wa-body');
  var input = document.getElementById('wa-input');
  var sendBtn = document.getElementById('wa-send');
  var honeypot = document.getElementById('wa-honeypot');
  var csrfToken = panel.dataset.csrf;

  var state = {
    started: false,
    steps: [],
    stepIndex: 0,
    pendingStep: null,
    answers: [],
    phase: 'idle', // 'idle' | 'choice' | 'text' | 'phone' | 'done'
  };

  function fetchJSON(url, opts) {
    return fetch(url, opts).then(function (r) { return r.json(); });
  }

  function scrollToBottom() {
    body.scrollTop = body.scrollHeight;
  }

  function nowLabel() {
    var d = new Date();
    var h = d.getHours();
    var m = d.getMinutes();
    var ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12;
    if (h === 0) h = 12;
    return h + ':' + (m < 10 ? '0' + m : m) + ' ' + ampm;
  }

  function addMessage(text, who) {
    var el = document.createElement('div');
    el.className = 'wa-msg wa-msg-' + who;
    var textNode = document.createElement('div');
    textNode.textContent = text;
    el.appendChild(textNode);
    var time = document.createElement('span');
    time.className = 'wa-msg-time';
    time.textContent = nowLabel();
    el.appendChild(time);
    body.appendChild(el);
    scrollToBottom();
  }

  function showTyping() {
    hideTyping();
    var t = document.createElement('div');
    t.className = 'wa-typing';
    t.id = 'wa-typing-indicator';
    t.innerHTML = '<span></span><span></span><span></span>';
    body.appendChild(t);
    scrollToBottom();
  }

  function hideTyping() {
    var t = document.getElementById('wa-typing-indicator');
    if (t) t.remove();
  }

  function botSay(text, cb) {
    showTyping();
    setTimeout(function () {
      hideTyping();
      addMessage(text, 'bot');
      if (cb) cb();
    }, 550 + Math.random() * 350);
  }

  function removeChoices() {
    var existing = body.querySelector('.wa-choices');
    if (existing) existing.remove();
  }

  function showChoices(options, onPick) {
    removeChoices();
    var wrap = document.createElement('div');
    wrap.className = 'wa-choices';
    options.forEach(function (opt) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'wa-choice-btn';
      btn.textContent = opt;
      btn.addEventListener('click', function () {
        wrap.querySelectorAll('button').forEach(function (b) { b.disabled = true; });
        onPick(opt);
      });
      wrap.appendChild(btn);
    });
    body.appendChild(wrap);
    scrollToBottom();
  }

  function setInputMode(mode, placeholder) {
    if (mode === 'off') {
      input.disabled = true;
      sendBtn.disabled = true;
      input.value = '';
      input.placeholder = 'Type a message';
      input.type = 'text';
    } else {
      input.disabled = false;
      sendBtn.disabled = false;
      input.placeholder = placeholder || 'Type a message';
      input.type = mode === 'phone' ? 'tel' : 'text';
      input.focus();
    }
  }

  function startFlow() {
    if (state.started) return;
    state.started = true;
    fetchJSON('/api/whatsapp-flow.php').then(function (data) {
      state.steps = data.steps || [];
      if (!csrfToken) csrfToken = data.csrf_token;
      askStep(0);
    });
  }

  function askStep(index) {
    state.stepIndex = index;
    if (index >= state.steps.length) {
      askPhone();
      return;
    }
    var step = state.steps[index];
    state.phase = step.type === 'choice' && step.options && step.options.length ? 'choice' : 'text';
    botSay(step.message, function () {
      if (state.phase === 'choice') {
        setInputMode('off');
        showChoices(step.options, function (choice) {
          removeChoices();
          addMessage(choice, 'user');
          state.answers.push({ question: step.message, answer: choice });
          askStep(index + 1);
        });
      } else {
        state.pendingStep = step;
        setInputMode('text', 'Type your answer…');
      }
    });
  }

  function askPhone() {
    state.phase = 'phone';
    botSay("Great — last step! What's the best phone number to reach you on?", function () {
      setInputMode('phone', 'e.g. +91 98765 43210');
    });
  }

  function handleSend() {
    var val = input.value.trim();
    if (!val || input.disabled) return;

    if (state.phase === 'text') {
      addMessage(val, 'user');
      state.answers.push({ question: state.pendingStep.message, answer: val });
      setInputMode('off');
      askStep(state.stepIndex + 1);
      return;
    }

    if (state.phase === 'phone') {
      addMessage(val, 'user');
      setInputMode('off');
      submitLead(val);
    }
  }

  function submitLead(phone) {
    showTyping();
    fetchJSON('/api/whatsapp-submit.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        csrf_token: csrfToken,
        phone: phone,
        answers: state.answers,
        website: honeypot.value,
      }),
    }).then(function (res) {
      hideTyping();
      if (res.success) {
        state.phase = 'done';
        addMessage('Thanks! Our team will reach out to you shortly. 🎉', 'bot');
      } else {
        addMessage(res.error || 'Something went wrong — please try again.', 'bot');
        setInputMode('phone', 'e.g. +91 98765 43210');
      }
    }).catch(function () {
      hideTyping();
      addMessage('Network error — please try again.', 'bot');
      setInputMode('phone', 'e.g. +91 98765 43210');
    });
  }

  sendBtn.addEventListener('click', handleSend);
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      handleSend();
    }
  });

  fab.addEventListener('click', function () {
    panel.classList.add('open');
    panel.setAttribute('aria-hidden', 'false');
    startFlow();
  });
  closeBtn.addEventListener('click', function () {
    panel.classList.remove('open');
    panel.setAttribute('aria-hidden', 'true');
  });
})();
