<?php
/**
 * Shared "proof + CTA" block for the three service landing pages
 * (custom-erp-solution, ecommerce-solutions, marketing-solutions):
 * real case studies tagged with this service, testimonials pulled from
 * whichever of those have one, then a final CTA banner. Both the case
 * studies and testimonials sections render nothing if there's no real
 * content yet — no placeholder cards.
 *
 * Expected from the including template:
 *   $serviceCaseStudies  array   from get_case_studies_by_service()
 *   $serviceCtaHeading   string
 *   $serviceCtaSub       string
 */
$serviceTestimonials = array_values(array_filter($serviceCaseStudies, fn ($cs) => !empty($cs['testimonial'])));
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
       <div class="blog-card-excerpt"><?= h($cs['description']) ?></div>
       <span class="blog-card-arrow">Read Case Study →</span>
     </div>
   </a>
   <?php endforeach; ?>
 </div>
</section>
<?php endif; ?>

<?php if (!empty($serviceTestimonials)): ?>
<section id="service-testimonials">
 <div class="eyebrow rv"><div class="eyebrow-line"></div><span class="eyebrow-text">Testimonials</span><div class="eyebrow-line"></div></div>
 <h2 class="sec-h rv">What <span class="g">clients say</span></h2>
 <div class="testi-grid">
   <?php foreach ($serviceTestimonials as $t): ?>
   <div class="testi-card rv">
     <div class="testi-text">&ldquo;<?= nl2br(h($t['testimonial'])) ?>&rdquo;</div>
     <?php if (!empty($t['testimonial_author'])): ?><div class="testi-author">— <?= h($t['testimonial_author']) ?></div><?php endif; ?>
   </div>
   <?php endforeach; ?>
 </div>
</section>
<?php endif; ?>

<section id="service-cta">
 <h2 class="sec-h rv" style="font-size:clamp(26px,4vw,38px)"><?= $serviceCtaHeading ?></h2>
 <p class="sec-sub rv"><?= h($serviceCtaSub) ?></p>
 <button type="button" data-book class="btn btn-black rv">Book a Free Consultation →</button>
</section>
