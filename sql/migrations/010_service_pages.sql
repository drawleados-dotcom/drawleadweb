-- Migration 010 — Dedicated landing pages for the three core services
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Each row's `template` matches templates/{template}-body.php. Meta
-- title/description (and the rest of the SEO panel) are editable from
-- Admin → Pages exactly like Home and About Us.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Custom ERP Solution', '/custom-erp-solution',
  'Custom ERP Solution | Drawlead',
  'A custom ERP shaped around how your business actually works — modules mapped to your real workflows, role-based access, and migration off spreadsheets and legacy systems.',
  'custom-erp-solution'),
('Ecommerce Solutions', '/ecommerce-solutions',
  'Ecommerce Solutions | Drawlead',
  'Shopify, WooCommerce, and custom storefront builds with live inventory sync and automated order, invoice, and GST workflows — one connected stack from storefront to fulfilment.',
  'ecommerce-solutions'),
('Marketing Solutions', '/marketing-solutions',
  'Marketing Solutions | Drawlead',
  'Technical SEO and performance marketing that fix the leak between lead and conversion — Google, Meta, and LinkedIn campaigns with instant WhatsApp and email follow-up on every lead.',
  'marketing-solutions');
