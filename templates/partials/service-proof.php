<?php
/**
 * Shared "case studies + CTA" block — sections 7 and 8 of the 8-section
 * service landing page structure (custom-erp-solution, ecommerce-solutions,
 * marketing-solutions; matches the 8-section platform module pages built
 * from includes/platform-modules.php). Real case studies tagged with this
 * service, each showing its testimonial inline if it has one, then a final
 * CTA banner. Renders no case-study section at all if there's no real
 * content yet — no placeholder cards.
 *
 * Expected from the including template:
 *   $serviceCaseStudies  array   from get_case_studies_by_service()
 *   $serviceCtaHeading   string
 *   $serviceCtaSub       string
 */
?>

<?php if (!empty($serviceCaseStudies)): ?>
<section id="service-cases">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Case Studies</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">Real <span class="g">results</span></h2>
 <p class="sec-sub rv">Client work delivered with this exact solution.</p>
 <div class="blog-grid">
   <?php foreach ($serviceCaseStudies as $cs): ?>
   <a class="blog-card rv" href="/case-studies/<?= h($cs['slug']) ?>">
     <?php if (!empty($cs['desktop_image'])): ?>
       <div class="blog-card-img"><img src="<?= h(UPLOAD_URL . $cs['desktop_image']) ?>" alt="<?= h($cs['title']) ?>" loading="lazy"></div>
     <?php else: ?>
       <div class="blog-card-img placeholder"></div>
     <?php endif; ?>
     <div class="blog-card-body">
       <div class="blog-card-title"><?= h($cs['title']) ?></div>
       <?php if (!empty($cs['client_name'])): ?><div class="blog-card-date"><?= h($cs['client_name']) ?></div><?php endif; ?>
       <?php if (!empty($cs['testimonial'])): ?>
       <div class="blog-card-excerpt">&ldquo;<?= h($cs['testimonial']) ?>&rdquo;<?php if (!empty($cs['testimonial_author'])): ?> — <?= h($cs['testimonial_author']) ?><?php endif; ?></div>
       <?php else: ?>
       <div class="blog-card-excerpt"><?= h($cs['description']) ?></div>
       <?php endif; ?>
       <span class="blog-card-arrow">Read Case Study →</span>
     </div>
   </a>
   <?php endforeach; ?>
 </div>
</section>
<?php endif; ?>

<section id="service-cta">
 <h2 class="sec-h rv" style="font-size:clamp(26px,4vw,38px)"><?= $serviceCtaHeading ?></h2>
 <p class="sec-sub rv"><?= h($serviceCtaSub) ?></p>
 <button type="button" data-book class="btn btn-black rv">Book a Free Consultation →</button>
</section>
