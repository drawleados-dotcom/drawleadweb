// Drawlead CMS admin — Rank-Math-style live SEO analysis.
// Reads the current form's title/slug/focus-keyword/meta fields plus any
// [data-seo-content] elements (word-count / keyword-density source), and
// live-updates a Google snippet preview, a social share preview, and a
// scored checklist. No server round-trip — pure client-side analysis.
(function () {
  var panel = document.querySelector('.seo-panel');
  if (!panel) return;

  var pathPrefix = panel.getAttribute('data-seo-path-prefix') || '';
  var titleField = document.getElementById('title') || document.getElementById('name');
  var slugField = document.getElementById('slug');
  var focusField = document.getElementById('focus_keyword');
  var seoTitleField = document.getElementById('meta_title');
  var metaDescField = document.getElementById('meta_description');
  var ogTitleField = document.getElementById('og_title');
  var ogDescField = document.getElementById('og_description');
  var ogImageField = document.getElementById('og_image');
  var contentFields = document.querySelectorAll('[data-seo-content]');

  var serpUrl = document.getElementById('serp-url');
  var serpTitle = document.getElementById('serp-title');
  var serpDesc = document.getElementById('serp-desc');
  var scoreBadge = document.getElementById('seo-score-badge');
  var scoreLabel = document.getElementById('seo-score-label');
  var checklist = document.getElementById('seo-checklist');
  var socialImgWrap = document.getElementById('social-preview-img');
  var socialTitle = document.getElementById('social-preview-title');
  var socialDesc = document.getElementById('social-preview-desc');

  function escapeRegExp(s) {
    return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }

  function stripHtml(html) {
    var div = document.createElement('div');
    div.innerHTML = html;
    return (div.textContent || div.innerText || '').trim();
  }

  function contentText() {
    var text = '';
    contentFields.forEach(function (el) {
      text += ' ' + (el.value || '');
    });
    return stripHtml(text);
  }

  function wordCount(text) {
    return text.split(/\s+/).filter(Boolean).length;
  }

  function keywordDensity(text, keyword, words) {
    if (!keyword || !words) return 0;
    var re = new RegExp('\\b' + escapeRegExp(keyword) + '\\b', 'gi');
    var matches = text.match(re);
    return ((matches ? matches.length : 0) / words) * 100;
  }

  function truncate(str, n) {
    return str.length > n ? str.slice(0, n - 1) + '…' : str;
  }

  function addCheck(checks, totals, label, pass, weight) {
    totals.possible += weight;
    if (pass) totals.earned += weight;
    checks.push({ label: label, pass: pass });
  }

  function update() {
    var title = titleField ? titleField.value.trim() : '';
    var seoTitle = (seoTitleField.value || '').trim() || title;
    var slug = slugField ? slugField.value.trim() : '';
    var desc = (metaDescField.value || '').trim();
    var keyword = (focusField.value || '').trim().toLowerCase();
    var fullPath = pathPrefix + slug;
    var content = contentFields.length ? contentText() : '';

    // Google search preview
    if (serpUrl) serpUrl.textContent = 'drawlead.com' + fullPath;
    if (serpTitle) serpTitle.textContent = truncate(seoTitle || 'Your SEO title', 60);
    if (serpDesc) serpDesc.textContent = truncate(desc || 'Your meta description will appear here.', 160);

    // Social share preview
    var ogTitleVal = (ogTitleField.value || '').trim() || seoTitle;
    var ogDescVal = (ogDescField.value || '').trim() || desc;
    var ogImageVal = (ogImageField.value || '').trim();
    if (socialTitle) socialTitle.textContent = ogTitleVal || 'Your social title';
    if (socialDesc) socialDesc.textContent = ogDescVal || 'Your social description will appear here.';
    if (socialImgWrap) {
      if (ogImageVal) {
        socialImgWrap.style.display = 'block';
        socialImgWrap.querySelector('img').src = ogImageVal;
      } else {
        socialImgWrap.style.display = 'none';
      }
    }

    // SEO score + checklist
    if (!keyword) {
      checklist.innerHTML = '';
      scoreBadge.textContent = '—';
      scoreBadge.className = 'seo-score-badge';
      scoreLabel.textContent = 'Add a focus keyword to see your SEO score';
      return;
    }

    var checks = [];
    var totals = { earned: 0, possible: 0 };
    var slugifiedKeyword = keyword.replace(/\s+/g, '-');

    addCheck(checks, totals, 'Focus keyword in SEO title', seoTitle.toLowerCase().indexOf(keyword) !== -1, 15);
    addCheck(checks, totals, 'Focus keyword in meta description', desc.toLowerCase().indexOf(keyword) !== -1, 10);
    addCheck(checks, totals, 'Focus keyword in URL', fullPath.toLowerCase().indexOf(slugifiedKeyword) !== -1, 10);
    addCheck(checks, totals, 'SEO title length is 30–60 characters (' + seoTitle.length + ' now)', seoTitle.length >= 30 && seoTitle.length <= 60, 10);
    addCheck(checks, totals, 'Meta description length is 120–160 characters (' + desc.length + ' now)', desc.length >= 120 && desc.length <= 160, 10);
    addCheck(checks, totals, 'URL is reasonably short (' + fullPath.length + ' characters)', fullPath.length <= 75, 5);

    if (content) {
      var words = wordCount(content);
      var density = keywordDensity(content, keyword, words);
      addCheck(checks, totals, 'Focus keyword found in the content', content.toLowerCase().indexOf(keyword) !== -1, 10);
      addCheck(checks, totals, 'Content is at least 300 words (' + words + ' now)', words >= 300, 10);
      addCheck(checks, totals, 'Keyword density is 0.5%–2.5% (' + density.toFixed(1) + '% now)', density >= 0.5 && density <= 2.5, 10);
    }

    var score = totals.possible ? Math.round((totals.earned / totals.possible) * 100) : 0;
    scoreBadge.textContent = score;
    scoreBadge.className = 'seo-score-badge ' + (score >= 80 ? 'good' : score >= 50 ? 'ok' : 'bad');
    scoreLabel.textContent = score >= 80 ? 'Good SEO score' : score >= 50 ? 'OK — room to improve' : 'Needs improvement';

    checklist.innerHTML = checks.map(function (c) {
      return '<li class="' + (c.pass ? 'pass' : 'fail') + '">' + (c.pass ? '✓' : '✕') + ' ' + c.label + '</li>';
    }).join('');
  }

  [titleField, slugField, focusField, seoTitleField, metaDescField, ogTitleField, ogDescField, ogImageField]
    .filter(Boolean)
    .forEach(function (el) { el.addEventListener('input', update); });
  contentFields.forEach(function (el) { el.addEventListener('input', update); });

  update();
})();
