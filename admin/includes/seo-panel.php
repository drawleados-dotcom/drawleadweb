<?php
/**
 * Shared Rank-Math-style SEO panel, included inside a <form> by
 * page-edit.php / blog-edit.php / case-study-edit.php.
 *
 * Expected from the including scope:
 *   $seoRow        array  focus_keyword, meta_title, meta_description,
 *                         canonical_url, robots_index, robots_follow,
 *                         og_title, og_description, og_image
 *   $seoPathPrefix string e.g. '/blog/' or '/case-studies/', or '' for
 *                         pages (whose slug field already holds the full path)
 *
 * assets/seo-analyzer.js drives the live SEO score, Google snippet
 * preview, and social share preview against these fields — it reads the
 * title/name field, #slug, and any [data-seo-content] elements the
 * calling page has marked for word-count / keyword-density checks.
 */
?>
<div class="card seo-panel" data-seo-path-prefix="<?= h($seoPathPrefix) ?>">
  <div class="card-title">SEO</div>
  <div class="card-desc">Rank-Math-style on-page analysis, search &amp; social previews.</div>

  <div class="field">
    <label for="focus_keyword">Focus Keyword</label>
    <input type="text" id="focus_keyword" name="focus_keyword" maxlength="190" value="<?= h($seoRow['focus_keyword']) ?>" placeholder="e.g. custom erp software chennai">
    <div class="field-hint">The main phrase this page should rank for — drives the checklist below.</div>
  </div>

  <div class="field">
    <label for="meta_title">SEO Title</label>
    <input type="text" id="meta_title" name="meta_title" maxlength="190" value="<?= h($seoRow['meta_title']) ?>">
  </div>
  <div class="field">
    <label for="meta_description">Meta Description</label>
    <textarea id="meta_description" name="meta_description" rows="3" maxlength="320"><?= h($seoRow['meta_description']) ?></textarea>
  </div>

  <div class="seo-score-panel">
    <div class="seo-score-head">
      <div class="seo-score-badge" id="seo-score-badge">—</div>
      <div class="seo-score-label" id="seo-score-label">Add a focus keyword to see your SEO score</div>
    </div>
    <ul class="seo-checklist" id="seo-checklist"></ul>
  </div>

  <div class="field">
    <label>Google Search Preview</label>
    <div class="serp-preview">
      <div class="serp-url" id="serp-url">drawlead.com<?= h($seoPathPrefix) ?></div>
      <div class="serp-title" id="serp-title">Your SEO title</div>
      <div class="serp-desc" id="serp-desc">Your meta description will appear here.</div>
    </div>
  </div>

  <div class="seo-grid">
    <div class="field">
      <label for="robots_index">Search Engine Visibility</label>
      <select id="robots_index" name="robots_index">
        <option value="index" <?= $seoRow['robots_index'] === 'index' ? 'selected' : '' ?>>Index (show in search results)</option>
        <option value="noindex" <?= $seoRow['robots_index'] === 'noindex' ? 'selected' : '' ?>>No Index (hide from search results)</option>
      </select>
    </div>
    <div class="field">
      <label for="robots_follow">Link Following</label>
      <select id="robots_follow" name="robots_follow">
        <option value="follow" <?= $seoRow['robots_follow'] === 'follow' ? 'selected' : '' ?>>Follow</option>
        <option value="nofollow" <?= $seoRow['robots_follow'] === 'nofollow' ? 'selected' : '' ?>>No Follow</option>
      </select>
    </div>
  </div>

  <div class="field">
    <label for="canonical_url">Canonical URL</label>
    <input type="url" id="canonical_url" name="canonical_url" maxlength="255" value="<?= h($seoRow['canonical_url']) ?>" placeholder="Defaults to this page's own URL if left blank">
  </div>

  <div class="card-title" style="margin-top:1.6rem">Social Share Preview</div>
  <div class="field">
    <label for="og_title">Social Title</label>
    <input type="text" id="og_title" name="og_title" maxlength="190" value="<?= h($seoRow['og_title']) ?>" placeholder="Defaults to the SEO title if left blank">
  </div>
  <div class="field">
    <label for="og_description">Social Description</label>
    <textarea id="og_description" name="og_description" rows="2" maxlength="320" placeholder="Defaults to the meta description if left blank"><?= h($seoRow['og_description']) ?></textarea>
  </div>
  <div class="field">
    <label for="og_image">Social Image URL</label>
    <input type="text" id="og_image" name="og_image" maxlength="255" value="<?= h($seoRow['og_image']) ?>" placeholder="Paste an already-uploaded image URL, e.g. /uploads/xyz.jpg">
  </div>
  <div class="social-preview">
    <div class="social-preview-img" id="social-preview-img" style="display:none"><img alt=""></div>
    <div class="social-preview-body">
      <div class="social-preview-domain">drawlead.com</div>
      <div class="social-preview-title" id="social-preview-title">Your social title</div>
      <div class="social-preview-desc" id="social-preview-desc">Your social description will appear here.</div>
    </div>
  </div>
</div>
