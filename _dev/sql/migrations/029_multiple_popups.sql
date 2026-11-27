-- Migration 029 — Multiple popups, each with its own layout and pages
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Replaces the single site_popup row with a popups table: any number of
-- popups, each picking one of three layouts (split / stacked / corner)
-- and either every page or a chosen list of pages. The existing popup is
-- copied across as the first popup, set to show on all pages, so the live
-- site keeps showing exactly what it showed before. site_popup is left in
-- place (no longer read once this has run).

CREATE TABLE IF NOT EXISTS popups (
  id                     INT AUTO_INCREMENT PRIMARY KEY,
  name                   VARCHAR(120) NOT NULL DEFAULT '',            -- admin-only label
  layout                 VARCHAR(20)  NOT NULL DEFAULT 'split',       -- split | stacked | corner
  enabled                TINYINT(1)   NOT NULL DEFAULT 0,
  show_on                VARCHAR(10)  NOT NULL DEFAULT 'selected',    -- all | selected
  targets                TEXT,                                        -- comma-separated: page:ID, blog, case-studies
  image                  TEXT,
  image_alt              VARCHAR(190) NOT NULL DEFAULT '',
  title                  VARCHAR(190) NOT NULL DEFAULT '',
  description            VARCHAR(400) NOT NULL DEFAULT '',
  points                 TEXT,
  cta_text               VARCHAR(100) NOT NULL DEFAULT 'Book a Free Consultation',
  cta_use_booking        TINYINT(1)   NOT NULL DEFAULT 1,
  cta_link               VARCHAR(255) NOT NULL DEFAULT '',
  trigger_delay          TINYINT(1)   NOT NULL DEFAULT 1,
  trigger_new_page       TINYINT(1)   NOT NULL DEFAULT 0,
  trigger_refresh        TINYINT(1)   NOT NULL DEFAULT 0,
  trigger_scroll_section TINYINT(1)   NOT NULL DEFAULT 0,
  created_at             DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at             DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO popups (name, layout, enabled, show_on, targets, image, image_alt, title, description, points,
                    cta_text, cta_use_booking, cta_link,
                    trigger_delay, trigger_new_page, trigger_refresh, trigger_scroll_section)
SELECT 'Consultation Popup', 'split', enabled, 'all', '', image, image_alt, title, description, points,
       cta_text, cta_use_booking, cta_link,
       trigger_delay, trigger_new_page, trigger_refresh, trigger_scroll_section
FROM site_popup
WHERE id = 1 AND NOT EXISTS (SELECT 1 FROM popups);
