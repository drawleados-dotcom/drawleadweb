-- Migration 022 — Home 5 page
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds a homepage variant at /home-5, rendered by
-- templates/home5-body.php, linked from the main nav next to About Us.
-- Editable Draft/Published like any other page in Admin → Pages.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Home 5', '/home-5',
  'Drawlead | Intelligent Business Operating System',
  'Drawlead helps MSMEs and SMEs grow with websites, SEO, performance marketing and a unified business operating system.',
  'home5');

UPDATE pages SET show_in_menu = 1 WHERE slug = '/home-5';
