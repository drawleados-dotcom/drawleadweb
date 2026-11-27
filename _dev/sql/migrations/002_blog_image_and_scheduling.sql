-- Migration 002: blog image alt text + post scheduling
-- Run this once in phpMyAdmin → your database → SQL tab, on a database
-- that was already set up with the original schema.sql (i.e. it already
-- has a `blogs` table). Safe to run — it only adds new columns/values,
-- it does not touch or delete any existing data.

ALTER TABLE blogs
  ADD COLUMN featured_image_alt VARCHAR(190) NOT NULL DEFAULT '' AFTER featured_image,
  ADD COLUMN scheduled_at DATETIME NULL AFTER status,
  MODIFY COLUMN status ENUM('draft','published','scheduled') NOT NULL DEFAULT 'draft';
