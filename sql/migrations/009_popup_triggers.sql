-- Migration 009 — Admin-configurable triggers for the site popup
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.

ALTER TABLE site_popup
  ADD COLUMN trigger_delay          TINYINT(1) NOT NULL DEFAULT 1,  -- show after a few seconds
  ADD COLUMN trigger_new_page       TINYINT(1) NOT NULL DEFAULT 0,  -- show again on every new page navigated to
  ADD COLUMN trigger_refresh        TINYINT(1) NOT NULL DEFAULT 0,  -- show again on every page refresh
  ADD COLUMN trigger_scroll_section TINYINT(1) NOT NULL DEFAULT 0;  -- show when the 4th section scrolls into view
