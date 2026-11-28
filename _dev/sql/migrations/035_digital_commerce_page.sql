-- Migration 035 — Digital Commerce landing page
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds the Digital Commerce landing page at /digital-commerce, rendered by
-- templates/digital-commerce-body.php (styles /assets/dcom.css, script
-- /assets/dcom.js, on top of the home page's home10.css and home10.js).
-- Published but not in the menu: add it from Admin → Menu when ready.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
 ('Digital Commerce', '/digital-commerce',
   'Ecommerce Development, Custom Shopify Stores & CRO | Drawlead',
   'Custom Shopify storefronts, conversion-focused product pages, ecommerce integrations and automation, engineered around your brand and your customers. Talk to Drawlead.',
   'digital-commerce');
