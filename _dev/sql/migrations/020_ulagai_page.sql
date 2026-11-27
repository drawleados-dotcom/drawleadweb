-- Migration 020 — Ulagai page
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds Ulagai as a real page (/ulagai) -- a dark/violet ecommerce-
-- agency-style landing hero, editable Draft/Published like any other
-- page in Admin -> Pages. Not linked from the main nav by default;
-- turn on "Show in Menu" on its edit screen if you want it there.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Ulagai', '/ulagai',
  'Ulagai — High-Performance Ecommerce Stores | Drawlead',
  'We engineer high-performance ecommerce stores designed to convert traffic into consistent online orders, for scaling D2C brands serious about growth.',
  'ulagai');
