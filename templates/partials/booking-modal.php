<div id="booking-modal" class="booking-modal" aria-hidden="true" data-csrf="<?= h(csrf_token()) ?>">
  <div class="booking-overlay" data-book-close></div>
  <div class="booking-dialog" role="dialog" aria-modal="true" aria-labelledby="booking-title">
    <button type="button" class="booking-close" data-book-close aria-label="Close">&times;</button>

    <div class="booking-left">
      <div class="booking-left-inner">
        <div class="booking-eyebrow">Free Consultation Call</div>
        <h2 id="booking-title">From scattered spreadsheets<br>to <span class="g">one unified board</span></h2>
        <p>See how Drawlead brings your leads, sales, marketing, and operations out of Excel sheets, WhatsApp chats, and sticky notes — into a single command center.</p>

        <div class="bv">
          <div class="bv-chaos">
            <span class="bv-chip">📊 Excel</span>
            <span class="bv-chip">💬 WhatsApp</span>
            <span class="bv-chip">📝 Notes</span>
            <span class="bv-chip">📧 Email</span>
          </div>
          <div class="bv-arrow">→</div>
          <div class="bv-board">
            <div class="bv-board-bar"></div>
            <div class="bv-board-row"></div>
            <div class="bv-board-row"></div>
            <div class="bv-board-row w2"></div>
          </div>
        </div>

        <ul class="booking-points">
          <li>30-minute strategy call, 100% free</li>
          <li>No obligation, no sales pressure</li>
          <li>Walk away with a clear next step</li>
        </ul>
      </div>
    </div>

    <div class="booking-right">
      <div class="booking-step" data-step="datetime">
        <div class="booking-step-title">Pick a date &amp; time</div>

        <div class="bcal" id="bcal">
          <div class="bcal-head">
            <button type="button" class="bcal-nav" id="bcal-prev" aria-label="Previous month">←</button>
            <div class="bcal-month" id="bcal-month"></div>
            <button type="button" class="bcal-nav" id="bcal-next" aria-label="Next month">→</button>
          </div>
          <div class="bcal-weekdays"><span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span></div>
          <div class="bcal-days" id="bcal-days"></div>

          <div class="bslots" id="bslots" hidden>
            <div class="bslots-head">
              <button type="button" class="bslots-back" id="bslots-back">← Back</button>
              <div class="bslots-title" id="bslots-title"></div>
            </div>
            <div class="bslots-grid" id="bslots-grid"></div>
            <div class="bslots-empty" id="bslots-empty" hidden>No times available this day — please pick another date.</div>
          </div>
        </div>
      </div>

      <div class="booking-step" data-step="form" hidden>
        <button type="button" class="booking-back" id="booking-back">← Back to calendar</button>
        <div class="booking-step-title" id="booking-form-title"></div>
        <form id="booking-form" novalidate>
          <div id="booking-fields"></div>
          <input type="text" name="website" id="booking-honeypot" autocomplete="off" tabindex="-1" style="position:absolute;left:-9999px;top:-9999px" aria-hidden="true">
          <div class="booking-error" id="booking-error" hidden></div>
          <button type="submit" class="btn btn-black booking-submit" id="booking-submit-btn">Confirm Booking →</button>
        </form>
      </div>

      <div class="booking-step" data-step="success" hidden>
        <div class="booking-success">
          <div class="booking-success-icon">✓</div>
          <h3>You're booked!</h3>
          <p id="booking-success-detail"></p>
          <p class="booking-success-sub">A confirmation email is on its way to you.</p>
          <a href="/#cases" class="btn btn-outline2" data-book-close>View Our Case Studies →</a>
        </div>
      </div>
    </div>
  </div>
</div>
