-- Migration 005 — Case Studies module
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.

CREATE TABLE IF NOT EXISTS case_studies (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  title               VARCHAR(190) NOT NULL,
  slug                VARCHAR(190) NOT NULL UNIQUE,
  meta_title          VARCHAR(190) NOT NULL DEFAULT '',
  meta_description    VARCHAR(320) NOT NULL DEFAULT '',
  client_name         VARCHAR(190) NOT NULL DEFAULT '',
  description         VARCHAR(400) NOT NULL DEFAULT '',
  problem             TEXT,
  solution            TEXT,
  process             TEXT,
  result              TEXT,
  outcome             TEXT,
  testimonial         TEXT,
  testimonial_author  VARCHAR(190) NOT NULL DEFAULT '',
  services            VARCHAR(255) NOT NULL DEFAULT '',   -- comma-separated: Custom ERP Solution, Ecommerce Solutions, Marketing Solutions
  website_link        VARCHAR(255) NOT NULL DEFAULT '',
  erp_link             VARCHAR(255) NOT NULL DEFAULT '',
  desktop_image       VARCHAR(255) NOT NULL DEFAULT '',
  mobile_image        VARCHAR(255) NOT NULL DEFAULT '',
  result_image        VARCHAR(255) NOT NULL DEFAULT '',
  team                TEXT,                                -- one member per line, e.g. "Vinothkumar Babu — Project Lead"
  status              ENUM('draft','published') NOT NULL DEFAULT 'draft',
  author_id           INT NULL,
  created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE user_access MODIFY COLUMN item_type ENUM('page','blogs','case_studies') NOT NULL;
