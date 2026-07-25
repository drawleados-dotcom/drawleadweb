-- Migration 014 — Admin-manageable sidebar (Text / Image / CTA)
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Powers the CTA block in the sticky sidebar shown on blog post and
-- case study pages (templates/partials/sidebar.php), below the
-- always-dynamic "Recent Posts" list.

CREATE TABLE IF NOT EXISTS site_sidebar (
  id               INT PRIMARY KEY DEFAULT 1,
  enabled          TINYINT(1) NOT NULL DEFAULT 1,
  image            VARCHAR(255) NOT NULL DEFAULT '',
  image_alt        VARCHAR(190) NOT NULL DEFAULT '',
  title            VARCHAR(190) NOT NULL DEFAULT 'Book a Consultation',
  text             TEXT,
  cta_text         VARCHAR(100) NOT NULL DEFAULT 'Book a Free Consultation',
  cta_use_booking  TINYINT(1) NOT NULL DEFAULT 1,
  cta_link         VARCHAR(255) NOT NULL DEFAULT '',
  updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO site_sidebar (id, title, text) VALUES
(1, 'Book a Consultation', 'Ready to take your business to the next level?');
