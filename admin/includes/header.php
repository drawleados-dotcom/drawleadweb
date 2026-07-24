<?php
/**
 * Admin shell header. The calling page must, BEFORE including this file:
 *   - require_once __DIR__.'/../../includes/bootstrap.php';
 *   - call require_login() or require_admin()
 *   - set $pageTitle, optionally $pageSub and $activeNav
 */
$u = current_user();
$activeNav = $activeNav ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle ?? 'Admin') ?> — Drawlead Admin</title>
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="shell">
  <aside class="sidebar">
    <div class="sidebar-logo">Drawlead<span>Admin Panel</span></div>
    <nav class="side-nav">
      <a class="side-link<?= $activeNav === 'dashboard' ? ' active' : '' ?>" href="index.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
        Dashboard
      </a>
      <a class="side-link<?= $activeNav === 'pages' ? ' active' : '' ?>" href="pages.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
        Pages
      </a>
      <a class="side-link<?= $activeNav === 'blogs' ? ' active' : '' ?>" href="blogs.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        Blogs
      </a>
      <a class="side-link<?= $activeNav === 'case-studies' ? ' active' : '' ?>" href="case-studies.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-4 4"/></svg>
        Case Studies
      </a>
      <?php if ($u && $u['role'] === 'admin'): ?>
      <a class="side-link<?= $activeNav === 'users' ? ' active' : '' ?>" href="users.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Users
      </a>
      <a class="side-link<?= $activeNav === 'calendar' ? ' active' : '' ?>" href="calendar.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        Calendar
      </a>
      <a class="side-link<?= $activeNav === 'whatsapp' ? ' active' : '' ?>" href="whatsapp.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.5 8.5 0 0 1-12.4 7.6L3 20l1.05-5.4A8.5 8.5 0 1 1 21 11.5z"/></svg>
        WhatsApp
      </a>
      <a class="side-link<?= $activeNav === 'popup' ? ' active' : '' ?>" href="popup.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18"/><path d="M8 13.5l2 2 4-4.5"/></svg>
        Popup
      </a>
      <?php endif; ?>
      <a class="side-link<?= $activeNav === 'analytics' ? ' active' : '' ?>" href="analytics.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
        Analytics &amp; Search
      </a>
    </nav>
    <div class="side-user">
      <div class="side-user-name"><?= h($u['name'] ?? '') ?></div>
      <div class="side-user-role"><?= h($u['role'] ?? '') ?></div>
      <a class="side-logout" href="logout.php">Log out →</a>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <h1><?= h($pageTitle ?? '') ?></h1>
        <?php if (!empty($pageSub)): ?><div class="topbar-sub"><?= h($pageSub) ?></div><?php endif; ?>
      </div>
      <a class="btn btn-ghost btn-sm" href="/" target="_blank">View Site ↗</a>
    </div>
    <div class="content">
