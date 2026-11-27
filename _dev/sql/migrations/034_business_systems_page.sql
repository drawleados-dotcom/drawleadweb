-- Migration 034 — Business Systems landing page
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds the Business Systems landing page at /business-systems, rendered by
-- templates/business-systems-body.php (styles /assets/bizsys.css, script
-- /assets/bizsys.js, on top of the home page's home10.css and home10.js).
-- Published but not in the menu: add it from Admin → Menu when ready.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
 ('Business Systems', '/business-systems',
   'Custom Business Systems, ERP & Business OS | Drawlead',
   'Custom ERP, Business Operating Systems, CRM, HR, finance and workflow automation engineered around how your business actually works. Talk to Drawlead.',
   'business-systems');
