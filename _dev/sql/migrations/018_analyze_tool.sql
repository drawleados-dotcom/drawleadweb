-- Migration 018 — Drawlead Analyze
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Creates analyze_reports for the new /analyze tool: enter a URL, get a
-- rule-based CRO scorecard plus a rebuilt version of the page's own
-- copy in Drawlead's CRO layout, saved at a shareable /analyze/{token}.

CREATE TABLE IF NOT EXISTS analyze_reports (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  token                 VARCHAR(32) NOT NULL UNIQUE,
  target_url            VARCHAR(500) NOT NULL,
  page_title            VARCHAR(300) NOT NULL DEFAULT '',
  page_description      VARCHAR(500) NOT NULL DEFAULT '',
  cro_score             TINYINT UNSIGNED NOT NULL DEFAULT 0,
  sub_scores            TEXT,
  target_audience       VARCHAR(190) NOT NULL DEFAULT '',
  audience_match_score  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  changes_json          TEXT,
  new_page_json         TEXT,
  created_at            DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
