<button type="button" class="wa-fab" id="wa-fab" aria-label="Chat with us on WhatsApp">
  <svg viewBox="0 0 32 32" width="30" height="30"><path d="M16 3C9 3 3 9 3 16c0 2.6.8 5 2.1 7L3 29l6.2-2c2 1.1 4.3 1.7 6.8 1.7 7 0 13-6 13-13S23 3 16 3z" fill="#fff"/><path d="M23.5 19.4c-.4-.2-2.2-1.1-2.6-1.2-.3-.1-.6-.2-.8.2-.2.4-.9 1.2-1.1 1.4-.2.2-.4.3-.8.1-.4-.2-1.6-.6-3-1.9-1.1-1-1.9-2.2-2.1-2.6-.2-.4 0-.6.2-.8.2-.2.4-.4.5-.7.2-.2.2-.4.3-.6.1-.2 0-.5 0-.7-.1-.2-.8-2-1.1-2.7-.3-.7-.6-.6-.8-.6h-.7c-.2 0-.6.1-.9.5-.3.4-1.2 1.2-1.2 2.9s1.2 3.3 1.4 3.6c.2.2 2.4 3.7 5.8 5.1.8.3 1.4.5 1.9.7.8.2 1.5.2 2.1.1.6-.1 2-.8 2.3-1.6.3-.8.3-1.4.2-1.6-.1-.1-.3-.2-.7-.4z" fill="#25D366"/></svg>
</button>

<div class="wa-panel" id="wa-panel" data-csrf="<?= h(csrf_token()) ?>" aria-hidden="true">
  <div class="wa-header">
    <div class="wa-avatar">D</div>
    <div class="wa-header-info">
      <div class="wa-header-name">Drawlead</div>
      <div class="wa-header-status"><span class="wa-dot"></span>Typically replies instantly</div>
    </div>
    <button type="button" class="wa-close" id="wa-close" aria-label="Close chat">&times;</button>
  </div>

  <div class="wa-body" id="wa-body">
    <div class="wa-date-chip">Today</div>
  </div>

  <div class="wa-input-bar">
    <input type="text" id="wa-input" class="wa-input" placeholder="Type a message" disabled>
    <input type="text" id="wa-honeypot" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;top:-9999px" aria-hidden="true">
    <button type="button" class="wa-send" id="wa-send" disabled aria-label="Send">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="#fff"><path d="M3 20l18-8L3 4v6l12 2-12 2z"/></svg>
    </button>
  </div>
</div>
