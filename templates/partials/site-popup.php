<?php
/**
 * The popup for this page, managed from Admin → Popups. Each popup has a
 * layout (split / stacked / corner, see popup_layouts()) and shows on
 * every page or only on the pages picked for it; popup_for_target() picks
 * at most one per page. Renders nothing when no enabled popup matches.
 * Also skipped on $bookingPageMinimal pages (e.g. /book) — a popup asking
 * visitors to book a call would be redundant on a page that already is
 * the booking calendar.
 */
$popup = null;
if (empty($bookingPageMinimal)) {
    $popupTarget = http_response_code() === 404 ? '' : popup_target_for_uri($pdo, (string) ($uri ?? ''));
    $popup = popup_for_target($pdo, $popupTarget);
}
if ($popup):
    $popupLayout = isset(popup_layouts()[$popup['layout']]) ? $popup['layout'] : 'split';
    $popupPoints = array_slice(array_filter(array_map('trim', explode("\n", (string) $popup['points']))), 0, 4);
    $popupHasImage = (string) $popup['image'] !== '';
?>
<div id="site-popup" class="site-popup site-popup--<?= h($popupLayout) ?>" aria-hidden="true"
     data-popup-id="<?= (int) $popup['id'] ?>"
     data-popup-name="<?= h($popup['name'] ?? '') ?>"
     data-layout="<?= h($popupLayout) ?>"
     data-trigger-delay="<?= $popup['trigger_delay'] ? '1' : '0' ?>"
     data-trigger-new-page="<?= $popup['trigger_new_page'] ? '1' : '0' ?>"
     data-trigger-refresh="<?= $popup['trigger_refresh'] ? '1' : '0' ?>"
     data-trigger-scroll-section="<?= $popup['trigger_scroll_section'] ? '1' : '0' ?>">
  <div class="site-popup-overlay" data-popup-close></div>
  <div class="site-popup-dialog<?= $popupHasImage ? '' : ' no-image' ?>" role="dialog" aria-modal="<?= $popupLayout === 'corner' ? 'false' : 'true' ?>" aria-labelledby="site-popup-title">
    <button type="button" class="site-popup-close" data-popup-close aria-label="Close">&times;</button>

    <?php if ($popupHasImage): ?>
    <div class="site-popup-image">
      <img src="<?= h(popup_image_src((string) $popup['image'])) ?>" alt="<?= h($popup['image_alt'] ?: $popup['title']) ?>">
    </div>
    <?php endif; ?>

    <div class="site-popup-body">
      <h2 id="site-popup-title"><?= h($popup['title']) ?></h2>
      <?php if (!empty($popup['description'])): ?><p class="site-popup-desc"><?= h($popup['description']) ?></p><?php endif; ?>

      <?php if ($popupPoints): ?>
      <ul class="site-popup-points">
        <?php foreach ($popupPoints as $point): ?>
        <li><span class="site-popup-tick">&#10003;</span><?= h($point) ?></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>

      <?php if (!empty($popup['cta_text'])): ?>
        <?php if (!empty($popup['cta_use_booking'])): ?>
        <button type="button" data-book class="btn btn-black site-popup-cta"><?= h($popup['cta_text']) ?></button>
        <?php else: ?>
        <a href="<?= h($popup['cta_link'] ?: '#') ?>" class="btn btn-black site-popup-cta"><?= h($popup['cta_text']) ?></a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php endif; ?>
