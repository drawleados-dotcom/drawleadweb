-- Migration 012 — Rename the "R&D" platform module to "Inventory Management"
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Renames the page in place if migration 011 already created it as
-- /platform-rd (preserving its id and any admin-edited SEO fields), or
-- inserts it fresh under the new slug if it doesn't exist yet.

UPDATE pages SET
  name = 'Platform — Inventory Management',
  slug = '/platform-inventory',
  meta_title = 'Inventory Management Platform | Drawlead',
  meta_description = 'Track stock across every warehouse and channel, get alerted before you run out, and stop guessing what you actually have on hand.'
WHERE slug = '/platform-rd';

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Platform — Inventory Management', '/platform-inventory',
  'Inventory Management Platform | Drawlead',
  'Track stock across every warehouse and channel, get alerted before you run out, and stop guessing what you actually have on hand.',
  'platform-module');
