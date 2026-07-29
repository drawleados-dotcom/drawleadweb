<?php
/**
 * Site-wide "on open" consultation popup — content and on/off switch
 * managed entirely from admin/popup.php. Renders nothing if disabled or
 * left without a title, so an unconfigured install shows nothing. Also
 * skipped on $bookingPageMinimal pages (e.g. /book) — a popup asking
 * visitors to book a call would be redundant on a page that already is
 * the booking calendar.
 */
$popup = get_site_popup($pdo);
$popupPoints = array_slice(array_filter(array_map('trim', explode("\n", (string) $popup['points']))), 0, 4);
?>
<?php if (empty($bookingPageMinimal) && !empty($popup['enabled']) && $popup['title'] !== ''): ?>
<div id="site-popup" class="site-popup" aria-hidden="true"
     data-trigger-delay="<?= $popup['trigger_delay'] ? '1' : '0' ?>"
     data-trigger-new-page="<?= $popup['trigger_new_page'] ? '1' : '0' ?>"
     data-trigger-refresh="<?= $popup['trigger_refresh'] ? '1' : '0' ?>"
     data-trigger-scroll-section="<?= $popup['trigger_scroll_section'] ? '1' : '0' ?>">
  <div class="site-popup-overlay" data-popup-close></div>
  <div class="site-popup-dialog" role="dialog" aria-modal="true" aria-labelledby="site-popup-title">
    <button type="button" class="site-popup-close" data-popup-close aria-label="Close">&times;</button>

    <?php if (!empty($popup['image'])): ?>
    <div class="site-popup-image">
      <img src="<?= h(popup_image_src($popup['image'])) ?>" alt="<?= h($popup['image_alt'] ?: $popup['title']) ?>">
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
