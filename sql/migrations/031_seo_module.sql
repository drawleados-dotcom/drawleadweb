-- Migration 031 — SEO module (Admin → SEO)
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- seo_redirects  Admin → SEO → Redirects. 301/302/307/308/410 rules, applied
--                by index.php before any route. match_type 'prefix' = the
--                source and everything under it ("/old/*").
-- seo_404_log    Admin → SEO → 404 Logs. One row per missing URL, with hits.
-- seo_schemas    Admin → SEO → Schema. Custom JSON-LD blocks and the pages
--                they print on ("*", "/about-us", "/blog/*").
-- Global settings, sitemap, robots and image options are stored in the
-- existing settings table (seo_* keys), so they need nothing here.

CREATE TABLE IF NOT EXISTS seo_redirects (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  source      VARCHAR(500) NOT NULL,
  match_type  VARCHAR(10)  NOT NULL DEFAULT 'exact',
  target      VARCHAR(500) NOT NULL DEFAULT '',
  code        SMALLINT     NOT NULL DEFAULT 301,
  enabled     TINYINT(1)   NOT NULL DEFAULT 1,
  note        VARCHAR(190) NOT NULL DEFAULT '',
  hits        INT          NOT NULL DEFAULT 0,
  last_hit_at DATETIME     NULL,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY idx_source (source(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS seo_404_log (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  path        VARCHAR(500) NOT NULL,
  path_hash   CHAR(40)     NOT NULL,
  hits        INT          NOT NULL DEFAULT 1,
  referrer    VARCHAR(500) NOT NULL DEFAULT '',
  user_agent  VARCHAR(255) NOT NULL DEFAULT '',
  is_bot      TINYINT(1)   NOT NULL DEFAULT 0,
  first_seen  DATETIME     NULL,
  last_seen   DATETIME     NULL,
  UNIQUE KEY uniq_path (path_hash),
  KEY idx_last_seen (last_seen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS seo_schemas (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(190) NOT NULL DEFAULT '',
  target      TEXT,
  schema_json MEDIUMTEXT   NOT NULL,
  enabled     TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
