-- Migration 006 — Rank-Math-style SEO fields for Pages, Blogs, and Case Studies
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.

ALTER TABLE pages
  ADD COLUMN focus_keyword    VARCHAR(190) NOT NULL DEFAULT '',
  ADD COLUMN canonical_url    VARCHAR(255) NOT NULL DEFAULT '',
  ADD COLUMN robots_index     ENUM('index','noindex') NOT NULL DEFAULT 'index',
  ADD COLUMN robots_follow    ENUM('follow','nofollow') NOT NULL DEFAULT 'follow',
  ADD COLUMN og_title         VARCHAR(190) NOT NULL DEFAULT '',
  ADD COLUMN og_description   VARCHAR(320) NOT NULL DEFAULT '',
  ADD COLUMN og_image         VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE blogs
  ADD COLUMN focus_keyword    VARCHAR(190) NOT NULL DEFAULT '',
  ADD COLUMN canonical_url    VARCHAR(255) NOT NULL DEFAULT '',
  ADD COLUMN robots_index     ENUM('index','noindex') NOT NULL DEFAULT 'index',
  ADD COLUMN robots_follow    ENUM('follow','nofollow') NOT NULL DEFAULT 'follow',
  ADD COLUMN og_title         VARCHAR(190) NOT NULL DEFAULT '',
  ADD COLUMN og_description   VARCHAR(320) NOT NULL DEFAULT '',
  ADD COLUMN og_image         VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE case_studies
  ADD COLUMN focus_keyword    VARCHAR(190) NOT NULL DEFAULT '',
  ADD COLUMN canonical_url    VARCHAR(255) NOT NULL DEFAULT '',
  ADD COLUMN robots_index     ENUM('index','noindex') NOT NULL DEFAULT 'index',
  ADD COLUMN robots_follow    ENUM('follow','nofollow') NOT NULL DEFAULT 'follow',
  ADD COLUMN og_title         VARCHAR(190) NOT NULL DEFAULT '',
  ADD COLUMN og_description   VARCHAR(320) NOT NULL DEFAULT '',
  ADD COLUMN og_image         VARCHAR(255) NOT NULL DEFAULT '';
