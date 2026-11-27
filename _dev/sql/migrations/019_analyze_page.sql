-- Migration 019 — Analyze page in Admin -> Pages
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds Analyze as a real page row (/analyze), so it shows up in
-- Admin -> Pages with its own Draft/Published toggle, meta
-- title/description, and Show in Menu checkbox -- same as Home,
-- Home 2.0, and About Us. index.php checks this row's status before
-- serving /analyze, and falls back to "published" if the row does not
-- exist yet so the tool keeps working before this migration runs.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Analyze', '/analyze',
  'Drawlead Analyze — Free CRO Website Analysis',
  'Enter your website URL and get a free, rule-based conversion-rate-optimization scorecard plus a rebuilt version of your page in a modern, high-converting layout.',
  'analyze');

UPDATE pages SET show_in_menu = 1 WHERE slug = '/analyze';
