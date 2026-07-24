-- Migration 007 — Admin-manageable case study Departments/Services list
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Replaces the old fixed 3-item list with a table the admin can add to
-- from the Case Study edit screen. Existing case_studies.services values
-- (a comma-separated text list) are untouched and keep working as-is.

CREATE TABLE IF NOT EXISTS case_study_services (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(190) NOT NULL UNIQUE,
  sort_order  INT NOT NULL DEFAULT 0,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO case_study_services (name, sort_order) VALUES
('Custom ERP Solution', 1),
('Ecommerce Solutions', 2),
('Marketing Solutions', 3);
