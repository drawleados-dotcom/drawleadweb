-- Migration 028 — Page Content version history
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Every Publish and Restore in Admin → Pages → Edit → Page Content stores a
-- full snapshot of that page's page_content rows here, with who did it, when,
-- and what changed. The editor's History view lists them and can preview or
-- restore any of them. Deleting a page deletes its history.

CREATE TABLE IF NOT EXISTS page_content_versions (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  page_id     INT NOT NULL,
  user_id     INT NULL,
  user_name   VARCHAR(120) NOT NULL DEFAULT '',
  action      VARCHAR(20)  NOT NULL DEFAULT 'publish',   -- publish | restore | baseline
  summary     VARCHAR(500) NOT NULL DEFAULT '',
  details     TEXT,                                       -- JSON list of every change
  changed     INT NOT NULL DEFAULT 0,
  snapshot    MEDIUMTEXT NOT NULL,                        -- JSON: [[item_key, section_key, kind, original, value], ...]
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY idx_page_versions (page_id, id),
  FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
