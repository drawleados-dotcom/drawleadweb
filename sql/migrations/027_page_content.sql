-- Migration 027 — Section-wise page content editing
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Stores the text and image edits made in Admin → Pages → Edit → Page Content.
-- Only edited items get a row; everything else keeps rendering straight from
-- the page's template. item_key is a hash of the item's section and original
-- text (t…), image path (i…) or image alt (a…) — see includes/page-content.php.
-- Deleting a page deletes its edits.

CREATE TABLE IF NOT EXISTS page_content (
  page_id      INT NOT NULL,
  item_key     VARCHAR(40)  NOT NULL,
  section_key  VARCHAR(190) NOT NULL DEFAULT '',
  kind         ENUM('text','img_src','img_alt') NOT NULL DEFAULT 'text',
  original     TEXT,
  value        TEXT NOT NULL,
  updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (page_id, item_key),
  FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
