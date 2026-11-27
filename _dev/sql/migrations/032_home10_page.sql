-- Migration 032 — Home 10 page, set as the homepage
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually. Run it once only: it moves
-- the homepage. Needs migration 027 (page_content).
--
-- Adds Home 10, rendered by templates/home10-body.php with /assets/home10.css
-- and /assets/home10.js, and puts it at /. The page that was at / moves to
-- /home-previous as a draft, with its content edits and version history.
--
-- To go back: Admin -> Pages, give Home 10 another URL (e.g. /home-10), then
-- set the old page's URL to / and publish it.

START TRANSACTION;

-- SEO fields carry over from the current homepage.
INSERT INTO pages (name, slug, status, show_in_menu, meta_title, meta_description, focus_keyword, canonical_url,
    robots_index, robots_follow, og_title, og_description, og_image, template)
  SELECT 'Home 10', '/home-10', 'published', show_in_menu, meta_title, meta_description, focus_keyword, canonical_url,
    robots_index, robots_follow, og_title, og_description, og_image, 'home10'
  FROM pages WHERE slug = '/';

-- Testimonials holds placeholder copy: hidden until it is filled in (Admin ->
-- Pages -> Home 10 -> Page Content).
INSERT INTO page_content (page_id, item_key, section_key, kind, original, value)
  SELECT id, 'layout', '', 'text', '', '{"order":[],"hidden":["section:testimonials"]}'
  FROM pages WHERE template = 'home10';

-- Editors of the current homepage can edit the new one.
INSERT IGNORE INTO user_access (user_id, item_type, item_id)
  SELECT a.user_id, 'page', n.id FROM user_access a
  JOIN pages o ON o.slug = '/' AND a.item_type = 'page' AND a.item_id = o.id
  JOIN pages n ON n.template = 'home10';

-- Popups shown on the current homepage show on the new one.
UPDATE popups p
  JOIN pages o ON o.slug = '/'
  JOIN pages n ON n.template = 'home10'
  SET p.targets = CONCAT(p.targets, ',page:', n.id)
  WHERE FIND_IN_SET(CONCAT('page:', o.id), p.targets);

-- The old page has to give up / before the new one can take it, hence the
-- order; status is set first because it reads the slug being replaced.
UPDATE pages
  SET status = IF(slug = '/', 'draft', 'published'),
      slug = IF(slug = '/', '/home-previous', '/')
  WHERE slug = '/' OR template = 'home10'
  ORDER BY slug = '/' DESC;

COMMIT;
