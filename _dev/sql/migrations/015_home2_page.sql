-- Migration 015 — Home 2.0 page
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds a second homepage variant at /home-2 — same content as Home,
-- rebuilt as a card-based SaaS/CRO-style layout. Rendered by
-- templates/home2-body.php, linked from the main nav next to Home.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Home 2.0', '/home-2',
  'Drawlead | Intelligent Business Operating System',
  'Drawlead helps MSMEs and SMEs grow with websites, SEO, performance marketing and a unified business operating system.',
  'home2');
