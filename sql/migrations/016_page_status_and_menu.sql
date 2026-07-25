-- Migration 016 — Draft status & menu visibility for Pages
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds a Draft/Published status (draft pages 404 for visitors and drop
-- out of the sitemap) and a "Show in Menu" toggle (unchecking it removes
-- Home, Home 2.0, or About Us from the main nav) to admin/page-edit.php.

ALTER TABLE pages
  ADD COLUMN status ENUM('draft','published') NOT NULL DEFAULT 'published' AFTER slug,
  ADD COLUMN show_in_menu TINYINT(1) NOT NULL DEFAULT 0 AFTER status;

UPDATE pages SET show_in_menu = 1 WHERE slug IN ('/', '/home-2', '/about-us');
