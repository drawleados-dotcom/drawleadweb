<button type="button" class="wa-fab" id="wa-fab" aria-label="Chat with us on WhatsApp" aria-controls="wa-panel" aria-expanded="false">
  <svg viewBox="0 0 32 32" width="30" height="30" aria-hidden="true" focusable="false"><path d="M16 3C9 3 3 9 3 16c0 2.6.8 5 2.1 7L3 29l6.2-2c2 1.1 4.3 1.7 6.8 1.7 7 0 13-6 13-13S23 3 16 3z" fill="#fff"/><path d="M23.5 19.4c-.4-.2-2.2-1.1-2.6-1.2-.3-.1-.6-.2-.8.2-.2.4-.9 1.2-1.1 1.4-.2.2-.4.3-.8.1-.4-.2-1.6-.6-3-1.9-1.1-1-1.9-2.2-2.1-2.6-.2-.4 0-.6.2-.8.2-.2.4-.4.5-.7.2-.2.2-.4.3-.6.1-.2 0-.5 0-.7-.1-.2-.8-2-1.1-2.7-.3-.7-.6-.6-.8-.6h-.7c-.2 0-.6.1-.9.5-.3.4-1.2 1.2-1.2 2.9s1.2 3.3 1.4 3.6c.2.2 2.4 3.7 5.8 5.1.8.3 1.4.5 1.9.7.8.2 1.5.2 2.1.1.6-.1 2-.8 2.3-1.6.3-.8.3-1.4.2-1.6-.1-.1-.3-.2-.7-.4z" fill="#25D366"/></svg>
  <svg class="wa-fab-x" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
</button>

<div class="wa-panel" id="wa-panel" data-csrf="<?= h(csrf_token()) ?>" role="dialog" aria-label="Chat with Drawlead" aria-hidden="true" inert>
  <div class="wa-header">
    <div class="wa-avatar"><img src="/assets/home10/logos/drawlead-mark.webp" alt="" width="26" height="26"></div>
    <div class="wa-header-info">
      <div class="wa-header-name">Drawlead <svg class="wa-verified" viewBox="0 0 24 24" width="16" height="16" role="img" aria-label="Verified business"><path d="M12 1.5l2.4 1.9 3-.4 1.1 2.8 2.8 1.1-.4 3 1.9 2.4-1.9 2.4.4 3-2.8 1.1-1.1 2.8-3-.4L12 22.5l-2.4-1.9-3 .4-1.1-2.8-2.8-1.1.4-3L1.2 12l1.9-2.4-.4-3 2.8-1.1L6.6 3l3 .4z" fill="#4ADE80"/><path d="M8 12.3l2.7 2.7L16.2 9.5" fill="none" stroke="#0f3a24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
      <div class="wa-header-status"><span class="wa-dot"></span>Typically replies instantly</div>
    </div>
    <button type="button" class="wa-close" id="wa-close" aria-label="Close chat">&times;</button>
  </div>

  <div class="wa-body" id="wa-body" aria-live="polite">
    <div class="wa-date-chip">Today</div>
  </div>

  <div class="wa-input-bar">
    <input type="text" id="wa-input" class="wa-input" placeholder="Type a message" aria-label="Your message" disabled>
    <input type="text" id="wa-honeypot" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;top:-9999px" aria-hidden="true">
    <button type="button" class="wa-send" id="wa-send" disabled aria-label="Send">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="#fff" aria-hidden="true" focusable="false"><path d="M3 20l18-8L3 4v6l12 2-12 2z"/></svg>
    </button>
  </div>
</div>
