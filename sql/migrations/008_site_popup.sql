-- Migration 008 — Site-wide "on open" consultation popup
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.

CREATE TABLE IF NOT EXISTS site_popup (
  id               INT PRIMARY KEY DEFAULT 1,
  enabled          TINYINT(1) NOT NULL DEFAULT 0,
  image            VARCHAR(255) NOT NULL DEFAULT '',
  image_alt        VARCHAR(190) NOT NULL DEFAULT '',
  title            VARCHAR(190) NOT NULL DEFAULT '',
  description      VARCHAR(400) NOT NULL DEFAULT '',
  points           TEXT,                                    -- one point per line, first 4 shown with a checkmark
  cta_text         VARCHAR(100) NOT NULL DEFAULT 'Book a Free Consultation',
  cta_use_booking  TINYINT(1) NOT NULL DEFAULT 1,            -- 1 = opens the existing booking popup, 0 = uses cta_link
  cta_link         VARCHAR(255) NOT NULL DEFAULT '',
  updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Disabled by default (enabled=0) so it doesn't start popping up on the
-- live site before the admin has actually configured any content.
INSERT IGNORE INTO site_popup (id) VALUES (1);
