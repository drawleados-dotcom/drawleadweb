-- Migration 024 — Home 7 page
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds a homepage variant at /home-7, rendered by templates/home7-body.php
-- (its own stylesheet is /assets/home7.css and its animation libraries are
-- /assets/matter.min.js, gsap.min.js, ScrollTrigger.min.js). Editable
-- Draft/Published like any other page in Admin -> Pages.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Home 7', '/home-7',
  'Drawlead | Intelligent Business Operating System',
  'Drawlead helps MSMEs and SMEs grow with websites, SEO, performance marketing and a unified business operating system.',
  'home7');

UPDATE pages SET show_in_menu = 1 WHERE slug = '/home-7';
