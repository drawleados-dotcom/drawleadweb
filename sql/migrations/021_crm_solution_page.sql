-- Migration 021 — CRM Solution page
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds CRM as a real page (/crm-solution) and "CRM" as a
-- Departments/Service tag, so case studies can be tagged with it and
-- show up on the new page's Case Studies section.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('CRM', '/crm-solution',
  'CRM Solution | Drawlead',
  'A CRM built around how you actually sell — capture every lead, track every deal, and automate follow-ups, connected to the same ERP, ecommerce, and marketing systems you already run on.',
  'crm-solution');

INSERT IGNORE INTO case_study_services (name, sort_order) VALUES ('CRM', 4);
