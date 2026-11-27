<?php
/**
 * Booking calendar/form/success flow. Included on every page by
 * layout-end.php, opened as an overlay via any [data-book] trigger.
 *
 * A page can instead render this inline as the page itself — no
 * overlay, no marketing panel, no close button — by setting
 * $bookingPageMinimal = true before this include (see
 * templates/book-body.php / the /book route). booking.js's openModal()
 * is still what populates the calendar, so those pages auto-click a
 * hidden [data-book] trigger on load instead of duplicating that logic.
 * On standalone pages, the image already configured for the site-wide
 * consultation popup (Admin → Popups, the first one with an image) is reused as a side panel — same
 * image, no separate upload needed. Renders without it if none is set.
 */
$bookingStandalone = !empty($bookingPageMinimal);
$bookingStandaloneImage = '';
$bookingStandaloneImageAlt = '';
if ($bookingStandalone) {
    $popupForImage = popup_booking_image($pdo);
    if (!empty($popupForImage['image'])) {
        $bookingStandaloneImage = popup_image_src($popupForImage['image']);
        $bookingStandaloneImageAlt = $popupForImage['image_alt'] ?: 'Book a free consultation with Drawlead';
    }
}
?>
<div id="booking-modal" class="booking-modal<?= $bookingStandalone ? ' booking-modal-standalone' : '' ?>" aria-hidden="true" data-csrf="<?= h(csrf_token()) ?>">
  <?php if (!$bookingStandalone): ?>
  <div class="booking-overlay" data-book-close></div>
  <?php endif; ?>
  <div class="booking-dialog<?= $bookingStandaloneImage !== '' ? ' booking-dialog-with-image' : '' ?>" role="dialog" aria-modal="true" aria-labelledby="booking-title">
    <?php if (!$bookingStandalone): ?>
    <button type="button" class="booking-close" data-book-close aria-label="Close">&times;</button>
    <?php endif; ?>

    <?php if ($bookingStandalone): ?>
    <h2 id="booking-title" class="sr-only">Book a Free Consultation</h2>
    <?php if ($bookingStandaloneImage !== ''): ?>
    <div class="booking-standalone-image">
      <img src="<?= h($bookingStandaloneImage) ?>" alt="<?= h($bookingStandaloneImageAlt) ?>">
    </div>
    <?php endif; ?>
    <?php else: ?>
    <div class="booking-left">
      <div class="booking-left-inner">
        <div class="booking-eyebrow">Free Consultation Call</div>
        <h2 id="booking-title">From scattered spreadsheets<br>to <span class="g">one unified board</span></h2>
        <p>See how Drawlead brings your leads, sales, marketing, and operations out of Excel sheets, WhatsApp chats, and sticky notes — into a single command center.</p>

        <div class="bv" aria-hidden="true">
          <div class="bv-chaos">
            <span class="bv-chip">Excel</span>
            <span class="bv-chip">WhatsApp</span>
            <span class="bv-chip">Notes</span>
            <span class="bv-chip">Email</span>
          </div>
          <div class="bv-arrow" aria-hidden="true">↓</div>
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
    <?php endif; ?>

    <div class="booking-right">
      <div class="booking-step" data-step="datetime">
        <div class="booking-step-title">Pick a date &amp; time</div>

        <div class="bcal" id="bcal">
          <div class="bcal-head">
            <button type="button" class="bcal-nav" id="bcal-prev" aria-label="Previous month">←</button>
            <div class="bcal-month" id="bcal-month"></div>
            <button type="button" class="bcal-nav" id="bcal-next" aria-label="Next month">→</button>
          </div>
          <div class="bcal-weekdays" aria-hidden="true"><span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span></div>
          <div class="bcal-days" id="bcal-days"></div>
          <p class="bcal-legend" aria-hidden="true"><span class="bcal-key bcal-key--open"></span>Available <span class="bcal-key bcal-key--today"></span>Today</p>

          <div class="bslots" id="bslots" hidden>
            <div class="bslots-head">
              <button type="button" class="bslots-back" id="bslots-back">← Change date</button>
              <div class="bslots-title" id="bslots-title"></div>
            </div>
            <div class="bslots-grid" id="bslots-grid" aria-live="polite"></div>
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
          <a href="/#case-studies" class="btn btn-outline2" data-book-close>View Our Case Studies →</a>
        </div>
      </div>
    </div>
  </div>
</div>
