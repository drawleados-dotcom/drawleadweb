<?php
/**
 * Standalone booking page — no nav, no footer, just the calendar.
 * Meant to be shared directly (WhatsApp, email signature, ad landing),
 * not linked from the main nav.
 *
 * $bookingPageMinimal tells partials/booking-modal.php (included by
 * layout-end.php further down) to render itself as the page instead of
 * an overlay. The hidden trigger below fires the real booking.js
 * openModal() on load, so the calendar gets populated the exact same
 * way a normal [data-book] click would — no duplicated logic here.
 */
$bookingPageMinimal = true;
?>
<button type="button" data-book id="auto-book-trigger" class="sr-only" aria-hidden="true" tabindex="-1"></button>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var t = document.getElementById('auto-book-trigger');
  if (t) t.click();
});
</script>
