-- Migration 011 — Dedicated landing pages for the 7 Platform modules
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- All seven share one template file (templates/platform-module-body.php),
-- which reads the module's copy from includes/platform-modules.php based
-- on the page's own slug. Meta title/description (and the rest of the
-- SEO panel) are editable from Admin → Pages like Home and About Us.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Platform — Management', '/platform-management',
  'Management Platform | Drawlead',
  'Centralized dashboards and operational visibility for faster, smarter business decisions — one view of how your business is actually performing.',
  'platform-module'),
('Platform — Sales', '/platform-sales',
  'Sales Platform | Drawlead',
  'Manage leads, pipelines, customers, and revenue operations from one unified platform — CRM, pipeline, and invoicing in one place.',
  'platform-module'),
('Platform — Marketing', '/platform-marketing',
  'Marketing Platform | Drawlead',
  'Track campaigns, automate WhatsApp & email, and improve customer engagement at scale, with every lead attributed back to its source.',
  'platform-module'),
('Platform — Operations', '/platform-operations',
  'Operations Platform | Drawlead',
  'Streamline activities, inventory, and vendor management with intelligent process automation.',
  'platform-module'),
('Platform — Finance', '/platform-finance',
  'Finance Platform | Drawlead',
  'Centralize billing, expenses, financial reporting, and accounting integrations seamlessly.',
  'platform-module'),
('Platform — HR', '/platform-hr',
  'HR Platform | Drawlead',
  'Manage employees, attendance, payroll workflows, and leave management efficiently.',
  'platform-module'),
('Platform — R&D', '/platform-rd',
  'R&D Platform | Drawlead',
  'Enable innovation with AI-powered automation, predictive analytics, and custom intelligence.',
  'platform-module');
