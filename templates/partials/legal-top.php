<?php
/**
 * Top half of the legal pages (Privacy Policy, Refund Policy, Terms &
 * Conditions; migration 033 adds them). The template sets $legalTitle,
 * $legalLede and $legalUpdated, includes this, writes its clauses, then
 * includes legal-bottom.php.
 *
 * The reading layout is the blog post's (assets/blog-modern.css), with
 * assets/legal.css for lists, the contents box and the short-version card.
 *
 * Every clause is a top-level <section id="..."> inside .legal-doc, so
 * Admin -> Pages -> Edit -> Page Content lists each one by name and any
 * sentence can be changed there without touching code.
 *
 * @var string $legalTitle
 * @var string $legalLede
 * @var string $legalUpdated e.g. "27 November 2026"
 */
$activePage = 'legal';
include __DIR__ . '/nav.php';
?>
<link rel="stylesheet" href="<?= asset_url('/assets/home10.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/blog-modern.css') ?>">
<link rel="stylesheet" href="<?= asset_url('/assets/legal.css') ?>">
<script>document.documentElement.classList.add('js')</script>
<div class="h10 blog-page legal-page">
<a class="skip" href="#legal-doc">Skip to content</a>
<main>
<section class="journal-post-hero legal-hero" id="top">
  <div class="wrap journal-post-hero__in">
    <span class="pill pill--mint"><span class="pill__dot" aria-hidden="true"></span>Legal</span>
    <h1><?= h($legalTitle) ?></h1>
    <p class="legal-lede"><?= h($legalLede) ?></p>
    <div class="journal-post-meta">Last updated <?= h($legalUpdated) ?></div>
  </div>
</section>
<div class="wrap journal-post-layout legal-layout">
  <article class="journal-post-content legal-doc" id="legal-doc">
    <nav class="legal-toc" aria-label="On this page" hidden>
      <p>On this page</p>
      <ol></ol>
    </nav>
