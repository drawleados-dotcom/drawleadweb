<?php
/**
 * Drawlead Analyze — results page. $report comes from analyze_get_report()
 * in index.php: target_url, page_title, page_description, cro_score,
 * sub_scores (assoc array), target_audience, audience_match_score,
 * checks (array of ['category','points','earned','title','reason']),
 * new_page (headline/subheadline/cta_text/cards/trust_stats/audience).
 *
 * Every value below traces back to either our own fixed checklist copy
 * or the target site's own scraped text — all of it is attacker-
 * controlled from the analyzed site's perspective, so everything is
 * passed through h() at render time, no exceptions.
 */
$activePage = 'analyze';
include __DIR__ . '/partials/nav.php';

$newPage = $report['new_page'] ?: [];
$checks = $report['checks'] ?: [];
$subScores = $report['sub_scores'] ?: [];

$changed = array_values(array_filter($checks, fn ($c) => ($c['points'] ?? 0) > 0 && ($c['earned'] ?? 0) < ($c['points'] ?? 0)));
$kept = array_values(array_filter($checks, fn ($c) => ($c['points'] ?? 0) > 0 && ($c['earned'] ?? 0) >= ($c['points'] ?? 0)));

$reportDomain = (string) (parse_url($report['target_url'], PHP_URL_HOST) ?: $report['target_url']);
?>

<section id="az-report-hero">
 <div class="grid-bg" style="opacity:.4"></div>
 <div class="az-report-url rv">🔗 <?= h($reportDomain) ?></div>
 <h1 class="sec-h rv" style="max-width:700px">CRO Analysis for <span class="g"><?= h($reportDomain) ?></span></h1>

 <div class="az-tabs rv">
  <button type="button" class="az-tab active" data-az-tab="preview">New CRO Page</button>
  <button type="button" class="az-tab" data-az-tab="report">Report</button>
 </div>
</section>

<div class="az-panel active" id="az-panel-preview">
 <div class="az-preview rv">
  <div class="az-preview-hero">
   <div class="grid-bg" style="opacity:.3"></div>
   <?php if (!empty($newPage['audience'])): ?><div class="az-preview-kicker">Rebuilt for &middot; <?= h($newPage['audience']) ?></div><?php endif; ?>
   <div class="az-preview-h1"><?= h($newPage['headline'] ?? '') ?></div>
   <?php if (!empty($newPage['subheadline'])): ?><p class="az-preview-sub"><?= h($newPage['subheadline']) ?></p><?php endif; ?>
   <span class="az-preview-cta"><?= h($newPage['cta_text'] ?? 'Get Started Today') ?> →</span>
  </div>
  <div class="az-preview-body">
   <?php if (!empty($newPage['cards'])): ?>
   <div class="az-preview-grid">
     <?php foreach ($newPage['cards'] as $card): ?>
     <div class="az-preview-card">
       <div class="az-preview-card-title"><?= h($card['title'] ?? '') ?></div>
       <div class="az-preview-card-text"><?= h($card['text'] ?? '') ?></div>
     </div>
     <?php endforeach; ?>
   </div>
   <?php else: ?>
   <p style="text-align:center;color:var(--g400);font-size:13px">Not enough section content was found on the original page to rebuild feature cards — the score report below explains why.</p>
   <?php endif; ?>

   <?php if (!empty($newPage['trust_stats'])): ?>
   <div class="az-preview-stats">
     <?php foreach ($newPage['trust_stats'] as $stat): ?>
     <span class="az-preview-stat"><?= h($stat) ?></span>
     <?php endforeach; ?>
   </div>
   <?php endif; ?>
  </div>
 </div>

 <div class="sec-cta rv" style="margin-top:3rem">
  <button type="button" data-book class="btn btn-black">Get This Built For Real →</button>
 </div>
</div>

<div class="az-panel" id="az-panel-report">
 <div class="az-score-wrap rv">
  <div class="az-score-ring" style="--pct:<?= (int) $report['cro_score'] ?>">
   <div class="az-score-ring-inner">
    <div class="az-score-value"><?= (int) $report['cro_score'] ?></div>
    <div class="az-score-label">CRO Score</div>
   </div>
  </div>
  <div class="az-audience-card">
   <div class="az-audience-label">Target Audience</div>
   <div class="az-audience-value"><?= h($report['target_audience']) ?></div>
   <div class="az-audience-match">
    <div class="az-audience-match-track"><div class="az-audience-match-fill" style="width:<?= (int) $report['audience_match_score'] ?>%"></div></div>
    <span class="az-audience-match-value"><?= (int) $report['audience_match_score'] ?>% match</span>
   </div>
  </div>
 </div>

 <?php if ($subScores): ?>
 <div class="az-subscores rv">
   <?php foreach ($subScores as $label => $value): ?>
   <div class="az-subscore-row">
    <div class="az-subscore-label"><?= h($label) ?></div>
    <div class="az-subscore-track"><div class="az-subscore-fill" style="width:<?= (int) $value ?>%"></div></div>
    <div class="az-subscore-value"><?= (int) $value ?></div>
   </div>
   <?php endforeach; ?>
 </div>
 <?php endif; ?>

 <?php if ($changed): ?>
 <div class="az-changes-heading rv">What We Changed (<?= count($changed) ?>)</div>
 <div class="az-changes rv">
   <?php foreach ($changed as $c): ?>
   <div class="az-change-item">
     <div class="az-change-icon az-change-icon-fixed">✓</div>
     <div>
       <div class="az-change-title"><?= h($c['title'] ?? '') ?></div>
       <div class="az-change-reason"><?= h($c['reason'] ?? '') ?></div>
     </div>
   </div>
   <?php endforeach; ?>
 </div>
 <?php endif; ?>

 <?php if ($kept): ?>
 <div class="az-changes-heading rv">Already Strong (<?= count($kept) ?>)</div>
 <div class="az-changes rv">
   <?php foreach ($kept as $c): ?>
   <div class="az-change-item">
     <div class="az-change-icon az-change-icon-kept">✓</div>
     <div>
       <div class="az-change-title"><?= h($c['title'] ?? '') ?></div>
       <div class="az-change-reason"><?= h($c['reason'] ?? '') ?></div>
     </div>
   </div>
   <?php endforeach; ?>
 </div>
 <?php endif; ?>

 <div class="sec-cta rv" style="margin-top:1rem">
  <button type="button" data-book class="btn btn-black">Book a Free Consultation →</button>
  <a href="/analyze" class="btn btn-outline2">Analyze Another Site</a>
 </div>
</div>

<script>
(function(){
  var tabs = document.querySelectorAll('.az-tab');
  var panels = {
    preview: document.getElementById('az-panel-preview'),
    report: document.getElementById('az-panel-report')
  };
  tabs.forEach(function(tab){
    tab.addEventListener('click', function(){
      tabs.forEach(function(t){ t.classList.remove('active'); });
      Object.keys(panels).forEach(function(k){ panels[k].classList.remove('active'); });
      tab.classList.add('active');
      panels[tab.getAttribute('data-az-tab')].classList.add('active');
    });
  });
})();
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
