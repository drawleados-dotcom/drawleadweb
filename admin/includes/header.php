<?php
/**
 * Admin shell header. The calling page must, BEFORE including this file:
 *   - require_once __DIR__.'/../../includes/bootstrap.php';
 *   - call require_login() or require_admin()
 *   - set $pageTitle, optionally $pageSub and $activeNav
 */
$u = current_user();
$activeNav = $activeNav ?? '';
$hasPendingMigrationsNav = $u && $u['role'] === 'admin' && pending_migrations_exist($pdo);
// A page can start with the sidebar closed (e.g. the visual page editor, which
// needs the width) by setting $sidebarCollapsed = true. The ☰ button in the
// top bar opens and closes it on any page.
$sidebarCollapsed = !empty($sidebarCollapsed);
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
<link rel="stylesheet" href="<?= asset_url('/admin/assets/admin.css') ?>">
</head>
<body<?= $sidebarCollapsed ? ' class="sidebar-collapsed"' : '' ?>>
<div class="shell">
  <aside class="sidebar" id="adminSidebar">
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
      <a class="side-link<?= $activeNav === 'media' ? ' active' : '' ?>" href="media.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
        Media
      </a>
      <?php if ($u && $u['role'] === 'admin'): ?>
      <?php $seoNavOpen = $activeNav === 'seo' || str_starts_with($activeNav, 'seo-'); ?>
      <details class="side-group"<?= $seoNavOpen ? ' open' : '' ?>>
        <summary class="side-link<?= $seoNavOpen ? ' is-current' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/><path d="M8 11h6M11 8v6"/></svg>
          SEO
          <svg class="side-caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
        </summary>
        <div class="side-sub">
          <?php foreach (seo_admin_nav() as $seoKey => [$seoLabel, $seoHref]): ?>
          <a class="side-sublink<?= $activeNav === $seoKey ? ' active' : '' ?>" href="<?= h($seoHref) ?>"<?= $activeNav === $seoKey ? ' aria-current="page"' : '' ?>><?= h($seoLabel) ?></a>
          <?php endforeach; ?>
        </div>
      </details>
      <a class="side-link<?= $activeNav === 'users' ? ' active' : '' ?>" href="users.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Users
      </a>
      <a class="side-link<?= $activeNav === 'leads' ? ' active' : '' ?>" href="leads.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
        Lead Source
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
        Popups
      </a>
      <a class="side-link<?= $activeNav === 'sidebar' ? ' active' : '' ?>" href="sidebar.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M15 3v18"/></svg>
        Sidebar
      </a>
      <a class="side-link<?= $activeNav === 'menu' ? ' active' : '' ?>" href="menu.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/><path d="M8 6v12"/></svg>
        Menu
      </a>
      <a class="side-link<?= $activeNav === 'deployments' ? ' active' : '' ?>" href="deployments.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v6c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/><path d="M3 11v6c0 1.66 4.03 3 9 3s9-1.34 9-3v-6"/></svg>
        Production Environment
      </a>
      <a class="side-link<?= $activeNav === 'migrations' ? ' active' : '' ?>" href="run-migrations.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
        Migrations
        <?php if ($hasPendingMigrationsNav): ?><span class="badge badge-draft" style="margin-left:auto">Pending</span><?php endif; ?>
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
      <div class="topbar-title">
        <button type="button" class="side-toggle" data-side-toggle aria-controls="adminSidebar"
                aria-expanded="<?= $sidebarCollapsed ? 'false' : 'true' ?>" title="<?= $sidebarCollapsed ? 'Show menu' : 'Hide menu' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
          <span class="sr-only">Show or hide the menu</span>
        </button>
        <div>
          <h1><?= h($pageTitle ?? '') ?></h1>
          <?php if (!empty($pageSub)): ?><div class="topbar-sub"><?= h($pageSub) ?></div><?php endif; ?>
        </div>
      </div>
      <a class="btn btn-ghost btn-sm" href="/" target="_blank">View Site ↗</a>
    </div>
    <div class="content">
