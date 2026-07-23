-- ══════════════════════════════════════════════════════════════
-- Drawlead CMS — database schema
-- Import this once in phpMyAdmin (Hostinger cPanel → MySQL Databases)
-- against the empty database you create for this site.
-- ══════════════════════════════════════════════════════════════

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120)  NOT NULL,
  email         VARCHAR(190)  NOT NULL UNIQUE,
  password_hash VARCHAR(255)  NOT NULL,
  role          ENUM('admin','editor') NOT NULL DEFAULT 'editor',
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pages (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  name              VARCHAR(190) NOT NULL,
  slug              VARCHAR(190) NOT NULL UNIQUE,      -- e.g. "/" or "/about-us"
  meta_title        VARCHAR(190) NOT NULL DEFAULT '',
  meta_description  VARCHAR(320) NOT NULL DEFAULT '',
  template          VARCHAR(60)  NOT NULL,              -- matches templates/{template}-body.php
  updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS blogs (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  title             VARCHAR(190) NOT NULL,
  slug              VARCHAR(190) NOT NULL UNIQUE,       -- served at /blog/{slug}
  meta_title        VARCHAR(190) NOT NULL DEFAULT '',
  meta_description  VARCHAR(320) NOT NULL DEFAULT '',
  excerpt           VARCHAR(400) NOT NULL DEFAULT '',
  content           LONGTEXT,
  featured_image    VARCHAR(255) NOT NULL DEFAULT '',
  status            ENUM('draft','published') NOT NULL DEFAULT 'draft',
  author_id         INT NULL,
  created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Per-user access grants. role='admin' users always have full access
-- regardless of rows here; this table only matters for role='editor'.
--   item_type='page'  + item_id=<pages.id>  → can edit that specific page
--   item_type='blogs' + item_id=0           → can manage the Blogs module
CREATE TABLE IF NOT EXISTS user_access (
  user_id     INT NOT NULL,
  item_type   ENUM('page','blogs') NOT NULL,
  item_id     INT NOT NULL DEFAULT 0,
  PRIMARY KEY (user_id, item_type, item_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
  `key`   VARCHAR(100) PRIMARY KEY,
  `value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Seed data ──

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Home', '/', 'Drawlead | Intelligent Business Operating System',
  'Drawlead helps MSMEs and SMEs grow with websites, SEO, performance marketing and a unified business operating system.',
  'home'),
('About Us', '/about-us', 'About Us | Drawlead — Digital Transformation Company',
  'Drawlead is a Chennai-based digital transformation company founded by Vinothkumar Babu, helping MSMEs and SMEs grow through websites, SEO, performance marketing, and intelligent business systems.',
  'about-us');

INSERT IGNORE INTO settings (`key`, `value`) VALUES
('ga_measurement_id', ''),
('gsc_verification_tag', ''),
('blog_meta_title', 'Blog | Drawlead'),
('blog_meta_description', 'Insights on growth, SEO, and digital transformation from the Drawlead team.'),
('site_name', 'Drawlead');

-- No default user is created here on purpose — the first time you open
-- /admin/login.php with an empty `users` table, it redirects to
-- /admin/signup.php so you can create the first admin account yourself.
