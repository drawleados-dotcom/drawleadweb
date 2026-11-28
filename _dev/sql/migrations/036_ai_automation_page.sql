-- Migration 036 — AI & Automation landing page
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds the AI & Automation landing page at /ai-automation, rendered by
-- templates/ai-automation-body.php (styles /assets/aiauto.css, script
-- /assets/aiauto.js, on top of the home page's home10.css and home10.js).
-- Published but not in the menu: add it from Admin → Menu when ready.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
 ('AI & Automation', '/ai-automation',
   'AI Agents, WhatsApp & Business Process Automation | Drawlead',
   'Custom AI agents, WhatsApp automation, AI applications and connected workflow automation, engineered around how your business actually operates. Talk to Drawlead.',
   'ai-automation');
