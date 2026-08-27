-- Migration 023 — Final Home page
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds a homepage variant at /final-home, rendered by
-- templates/final_home-body.php, linked from the main nav next to Home 5.
-- Editable Draft/Published like any other page in Admin → Pages.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Final Home', '/final-home',
  'Drawlead | Intelligent Business Operating System',
  'Drawlead helps MSMEs and SMEs grow with websites, SEO, performance marketing and a unified business operating system.',
  'final_home');

UPDATE pages SET show_in_menu = 1 WHERE slug = '/final-home';
