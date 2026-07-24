<?php
/**
 * @var array $caseStudy       set by index.php before including this template
 * @var array $moreCaseStudies set by index.php before including this template
 * @var array $recentPosts     set by index.php before including this template
 */
$activePage = 'case-studies';
include __DIR__ . '/partials/nav.php';

$serviceList = $caseStudy['services'] !== '' ? array_map('trim', explode(',', $caseStudy['services'])) : [];
$teamList = array_filter(array_map('trim', explode("\n", (string) $caseStudy['team'])));
?>

<section id="post-hero">
 <div class="grid-bg" style="opacity:.45"></div>
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Case Study</span><div class="eyebrow-line"></div></div>
 <h1 class="sec-h rv" style="max-width:820px"><?= h($caseStudy['title']) ?></h1>
 <?php if ($serviceList || !empty($caseStudy['client_name'])): ?>
 <div class="cs-meta rv">
   <?php foreach ($serviceList as $service): ?><span class="cs-tag"><?= h($service) ?></span><?php endforeach; ?>
   <?php if (!empty($caseStudy['client_name'])): ?><span class="cs-client">Client: <?= h($caseStudy['client_name']) ?></span><?php endif; ?>
 </div>
 <?php endif; ?>
 <?php if (!empty($caseStudy['description'])): ?>
 <p class="sec-sub rv" style="max-width:680px;margin-left:auto;margin-right:auto"><?= h($caseStudy['description']) ?></p>
 <?php endif; ?>
</section>

<section id="post-body">
 <div class="post-layout">
  <div class="post-main">

<?php if (!empty($caseStudy['desktop_image']) || !empty($caseStudy['mobile_image']) || !empty($caseStudy['result_image'])): ?>
<section id="cs-shots">
 <?php if (!empty($caseStudy['desktop_image'])): ?>
 <div class="cs-shot-desktop rv">
   <img src="<?= h(UPLOAD_URL . $caseStudy['desktop_image']) ?>" alt="<?= h($caseStudy['title']) ?> — desktop view">
 </div>
 <?php endif; ?>
 <?php if (!empty($caseStudy['mobile_image']) || !empty($caseStudy['result_image'])): ?>
 <div class="cs-shots-row rv">
   <?php if (!empty($caseStudy['mobile_image'])): ?>
   <div class="cs-shot-row-item">
     <img src="<?= h(UPLOAD_URL . $caseStudy['mobile_image']) ?>" alt="<?= h($caseStudy['title']) ?> — mobile view">
   </div>
   <?php endif; ?>
   <?php if (!empty($caseStudy['result_image'])): ?>
   <div class="cs-shot-row-item">
     <img src="<?= h(UPLOAD_URL . $caseStudy['result_image']) ?>" alt="<?= h($caseStudy['title']) ?> — result">
   </div>
   <?php endif; ?>
 </div>
 <?php endif; ?>
</section>
<?php endif; ?>

<section id="cs-body">
 <?php
 $blocks = [
   'The Problem' => $caseStudy['problem'],
   'The Solution' => $caseStudy['solution'],
   'The Process' => $caseStudy['process'],
   'The Result' => $caseStudy['result'],
   'The Outcome' => $caseStudy['outcome'],
 ];
 foreach ($blocks as $label => $text):
   if (trim((string) $text) === '') continue;
 ?>
 <div class="cs-block rv">
   <div class="cs-block-label"><?= h($label) ?></div>
   <div class="cs-block-text"><?= nl2br(h($text)) ?></div>
 </div>
 <?php endforeach; ?>

 <?php if (!empty($caseStudy['testimonial'])): ?>
 <div class="cs-testimonial rv">
   <div class="cs-testimonial-text">&ldquo;<?= nl2br(h($caseStudy['testimonial'])) ?>&rdquo;</div>
   <?php if (!empty($caseStudy['testimonial_author'])): ?>
   <div class="cs-testimonial-author">— <?= h($caseStudy['testimonial_author']) ?></div>
   <?php endif; ?>
 </div>
 <?php endif; ?>

 <?php if ($teamList): ?>
 <div class="cs-team rv">
   <div class="cs-block-label">Team</div>
   <ul class="cs-team-list">
     <?php foreach ($teamList as $member): ?><li><?= h($member) ?></li><?php endforeach; ?>
   </ul>
 </div>
 <?php endif; ?>

 <?php if (!empty($caseStudy['website_link']) || !empty($caseStudy['erp_link'])): ?>
 <div class="cs-links rv">
   <?php if (!empty($caseStudy['website_link'])): ?>
   <a href="<?= h($caseStudy['website_link']) ?>" class="btn btn-outline2" target="_blank" rel="noopener">Visit Website ↗</a>
   <?php endif; ?>
   <?php if (!empty($caseStudy['erp_link'])): ?>
   <a href="<?= h($caseStudy['erp_link']) ?>" class="btn btn-outline2" target="_blank" rel="noopener">View Platform ↗</a>
   <?php endif; ?>
 </div>
 <?php endif; ?>

 <div class="post-back rv"><a href="/case-studies">← Back to Case Studies</a></div>
</section>

  </div>
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
 </div>
</section>

<section id="cs-cta">
 <h2 class="sec-h rv" style="font-size:clamp(26px,4vw,38px)">Ready to build <span class="g">your own success story?</span></h2>
 <p class="sec-sub rv">Book a free consultation and see how Drawlead can do the same for your business.</p>
 <button type="button" data-book class="btn btn-black rv">Book a Free Consultation →</button>
</section>

<?php if (!empty($moreCaseStudies)): ?>
<section id="cs-more">
 <h2 class="sec-h rv" style="font-size:clamp(24px,3.5vw,32px)">More <span class="g">Case Studies</span></h2>
 <div class="blog-grid">
   <?php foreach ($moreCaseStudies as $cs): ?>
   <a class="blog-card rv" href="/case-studies/<?= h($cs['slug']) ?>">
     <?php if (!empty($cs['desktop_image'])): ?>
       <div class="blog-card-img"><img src="<?= h(UPLOAD_URL . $cs['desktop_image']) ?>" alt="<?= h($cs['title']) ?>" loading="lazy"></div>
     <?php else: ?>
       <div class="blog-card-img placeholder"></div>
     <?php endif; ?>
     <div class="blog-card-body">
       <?php if (!empty($cs['services'])): $firstService = trim(explode(',', $cs['services'])[0]); ?>
         <div class="cs-card-tag"><?= h($firstService) ?></div>
       <?php endif; ?>
       <div class="blog-card-title"><?= h($cs['title']) ?></div>
       <?php if (!empty($cs['client_name'])): ?><div class="blog-card-date"><?= h($cs['client_name']) ?></div><?php endif; ?>
       <div class="blog-card-excerpt"><?= h($cs['description']) ?></div>
       <span class="blog-card-arrow">Read Case Study →</span>
     </div>
   </a>
   <?php endforeach; ?>
 </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
