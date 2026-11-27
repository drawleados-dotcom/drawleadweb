-- Migration 025 — Home 8 page
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds a dark-theme homepage variant at /home-8, rendered by
-- templates/home8-body.php. It loads /assets/home7.css for layout and
-- animation and /assets/home8.css for colour, and shares Home 7's animation
-- libraries (/assets/matter.min.js, gsap.min.js, ScrollTrigger.min.js).
-- /home-7 is untouched.
--
-- show_in_menu is left at 0 on purpose: turning it on would add a nav link to
-- every page on the site, Home 7 included. Tick "Show in Menu" in
-- Admin -> Pages when you want the link.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Home 8', '/home-8',
  'Drawlead | Intelligent Business Operating System',
  'Drawlead helps MSMEs and SMEs grow with websites, SEO, performance marketing and a unified business operating system.',
  'home8');
