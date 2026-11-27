<?php
/**
 * Bottom half of the legal pages: closes the clauses opened by
 * legal-top.php, then the side panel linking the three policies, and the
 * site footer.
 *
 * @var string|null $uri the current path, set by index.php
 */
$legalLinks = [
    '/privacy-policy' => 'Privacy Policy',
    '/refund-policy' => 'Refund Policy',
    '/terms-and-conditions' => 'Terms & Conditions',
];
?>
  </article>
  <script>
  // "On this page": one link per clause, read from the headings as they
  // render (Page Content edits included), so the list never drifts from the text.
  (function () {
    var box = document.querySelector('.legal-toc');
    if (!box) return;
    var list = box.querySelector('ol');
    document.querySelectorAll('.legal-doc > section[id] > h2').forEach(function (h) {
      var a = document.createElement('a');
      a.href = '#' + h.parentNode.id;
      a.textContent = h.textContent;
      var li = document.createElement('li');
      li.appendChild(a);
      list.appendChild(li);
    });
    if (list.children.length > 2) box.hidden = false;
  })();
  </script>
  <aside class="journal-post-aside legal-aside">
    <h2>Policies</h2>
    <nav aria-label="Legal pages">
      <?php foreach ($legalLinks as $legalHref => $legalLabel): ?>
      <a href="<?= h($legalHref) ?>"<?= ($uri ?? '') === $legalHref ? ' aria-current="page"' : '' ?>><?= h($legalLabel) ?></a>
      <?php endforeach; ?>
    </nav>
    <p>Questions about these policies? Email <a href="mailto:official@drawlead.co.in">official@drawlead.co.in</a></p>
  </aside>
</div>
</main>
<?php include __DIR__ . '/modern-footer.php'; ?>
</div>
<script src="<?= asset_url('/assets/home10.js') ?>" defer></script>
