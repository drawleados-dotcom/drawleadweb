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

-- The seven columns below (focus_keyword through og_image) power the
-- Rank-Math-style SEO panel: on-page analysis, robots meta, canonical
-- URL, and Open Graph / Twitter Card social previews. They're repeated
-- identically on pages, blogs, and case_studies.
CREATE TABLE IF NOT EXISTS pages (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  name              VARCHAR(190) NOT NULL,
  slug              VARCHAR(190) NOT NULL UNIQUE,      -- e.g. "/" or "/about-us"
  meta_title        VARCHAR(190) NOT NULL DEFAULT '',
  meta_description  VARCHAR(320) NOT NULL DEFAULT '',
  focus_keyword     VARCHAR(190) NOT NULL DEFAULT '',
  canonical_url     VARCHAR(255) NOT NULL DEFAULT '',
  robots_index      ENUM('index','noindex') NOT NULL DEFAULT 'index',
  robots_follow     ENUM('follow','nofollow') NOT NULL DEFAULT 'follow',
  og_title          VARCHAR(190) NOT NULL DEFAULT '',
  og_description    VARCHAR(320) NOT NULL DEFAULT '',
  og_image          VARCHAR(255) NOT NULL DEFAULT '',
  template          VARCHAR(60)  NOT NULL,              -- matches templates/{template}-body.php
  updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS blogs (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  title             VARCHAR(190) NOT NULL,
  slug              VARCHAR(190) NOT NULL UNIQUE,       -- served at /blog/{slug}
  meta_title        VARCHAR(190) NOT NULL DEFAULT '',
  meta_description  VARCHAR(320) NOT NULL DEFAULT '',
  focus_keyword     VARCHAR(190) NOT NULL DEFAULT '',
  canonical_url     VARCHAR(255) NOT NULL DEFAULT '',
  robots_index      ENUM('index','noindex') NOT NULL DEFAULT 'index',
  robots_follow     ENUM('follow','nofollow') NOT NULL DEFAULT 'follow',
  og_title          VARCHAR(190) NOT NULL DEFAULT '',
  og_description    VARCHAR(320) NOT NULL DEFAULT '',
  og_image          VARCHAR(255) NOT NULL DEFAULT '',
  excerpt           VARCHAR(400) NOT NULL DEFAULT '',
  content           LONGTEXT,
  featured_image    VARCHAR(255) NOT NULL DEFAULT '',
  featured_image_alt VARCHAR(190) NOT NULL DEFAULT '',
  status            ENUM('draft','published','scheduled') NOT NULL DEFAULT 'draft',
  scheduled_at      DATETIME NULL,                        -- used when status='scheduled'
  author_id         INT NULL,
  created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Per-user access grants. role='admin' users always have full access
-- regardless of rows here; this table only matters for role='editor'.
--   item_type='page'         + item_id=<pages.id>  → can edit that specific page
--   item_type='blogs'        + item_id=0           → can manage the Blogs module
--   item_type='case_studies' + item_id=0           → can manage the Case Studies module
CREATE TABLE IF NOT EXISTS user_access (
  user_id     INT NOT NULL,
  item_type   ENUM('page','blogs','case_studies') NOT NULL,
  item_id     INT NOT NULL DEFAULT 0,
  PRIMARY KEY (user_id, item_type, item_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
  `key`   VARCHAR(100) PRIMARY KEY,
  `value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Consultation booking system ──

CREATE TABLE IF NOT EXISTS booking_availability (
  id                     INT PRIMARY KEY DEFAULT 1,
  days_of_week           VARCHAR(20) NOT NULL DEFAULT '1,2,3,4,5', -- 0=Sun..6=Sat
  start_time             TIME NOT NULL DEFAULT '10:00:00',
  end_time               TIME NOT NULL DEFAULT '18:00:00',
  slot_interval_minutes  INT NOT NULL DEFAULT 30,
  range_start            DATE NULL,
  range_end              DATE NULL,
  updated_at             DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS booking_notification_emails (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  email  VARCHAR(190) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS booking_form_fields (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  field_key             VARCHAR(100) NOT NULL UNIQUE,
  label                 VARCHAR(190) NOT NULL,
  field_type            ENUM('text','email','phone','textarea','select','radio','checkbox','date') NOT NULL DEFAULT 'text',
  field_role            ENUM('none','name','email') NOT NULL DEFAULT 'none',
  options               TEXT NULL,
  placeholder           VARCHAR(190) NOT NULL DEFAULT '',
  is_required            TINYINT(1) NOT NULL DEFAULT 1,
  sort_order            INT NOT NULL DEFAULT 0,
  conditional_field_id  INT NULL,
  conditional_value     VARCHAR(190) NULL,
  created_at            DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conditional_field_id) REFERENCES booking_form_fields(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bookings (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  booking_date  DATE NOT NULL,
  booking_time  TIME NOT NULL,
  form_data     LONGTEXT NOT NULL,
  name          VARCHAR(190) NOT NULL DEFAULT '',
  email         VARCHAR(190) NOT NULL DEFAULT '',
  status        ENUM('confirmed','cancelled') NOT NULL DEFAULT 'confirmed',
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_slot (booking_date, booking_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Case Studies ──

CREATE TABLE IF NOT EXISTS case_studies (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  title               VARCHAR(190) NOT NULL,
  slug                VARCHAR(190) NOT NULL UNIQUE,        -- served at /case-studies/{slug}
  meta_title          VARCHAR(190) NOT NULL DEFAULT '',
  meta_description    VARCHAR(320) NOT NULL DEFAULT '',
  focus_keyword       VARCHAR(190) NOT NULL DEFAULT '',
  canonical_url       VARCHAR(255) NOT NULL DEFAULT '',
  robots_index        ENUM('index','noindex') NOT NULL DEFAULT 'index',
  robots_follow       ENUM('follow','nofollow') NOT NULL DEFAULT 'follow',
  og_title            VARCHAR(190) NOT NULL DEFAULT '',
  og_description      VARCHAR(320) NOT NULL DEFAULT '',
  og_image            VARCHAR(255) NOT NULL DEFAULT '',
  client_name         VARCHAR(190) NOT NULL DEFAULT '',
  description         VARCHAR(400) NOT NULL DEFAULT '',
  problem             TEXT,
  solution            TEXT,
  process             TEXT,
  result              TEXT,
  outcome             TEXT,
  testimonial         TEXT,
  testimonial_author  VARCHAR(190) NOT NULL DEFAULT '',
  services            VARCHAR(255) NOT NULL DEFAULT '',    -- comma-separated: Custom ERP Solution, Ecommerce Solutions, Marketing Solutions
  website_link        VARCHAR(255) NOT NULL DEFAULT '',
  erp_link            VARCHAR(255) NOT NULL DEFAULT '',
  desktop_image       VARCHAR(255) NOT NULL DEFAULT '',
  mobile_image        VARCHAR(255) NOT NULL DEFAULT '',
  result_image        VARCHAR(255) NOT NULL DEFAULT '',
  team                TEXT,                                 -- one member per line, e.g. "Vinothkumar Babu — Project Lead"
  status              ENUM('draft','published') NOT NULL DEFAULT 'draft',
  author_id           INT NULL,
  created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── WhatsApp-style lead-capture chat widget ──

CREATE TABLE IF NOT EXISTS whatsapp_flow_steps (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  step_order   INT NOT NULL DEFAULT 0,
  message      TEXT NOT NULL,
  step_type    ENUM('choice','text') NOT NULL DEFAULT 'choice',
  options      TEXT NULL,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS whatsapp_leads (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  answers     LONGTEXT NOT NULL,
  phone       VARCHAR(40) NOT NULL,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
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

INSERT IGNORE INTO booking_availability (id, range_start, range_end)
VALUES (1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY));

INSERT IGNORE INTO booking_form_fields (field_key, label, field_type, field_role, placeholder, is_required, sort_order) VALUES
('name', 'Full Name', 'text', 'name', 'Your name', 1, 1),
('email', 'Email Address', 'email', 'email', 'you@company.com', 1, 2),
('phone', 'Phone Number', 'phone', 'none', '+91 98765 43210', 1, 3),
('company', 'Company Name', 'text', 'none', 'Your business name', 0, 4);

INSERT INTO whatsapp_flow_steps (step_order, message, step_type, options)
SELECT 1, 'Hi! Welcome to Drawlead — your digital solutions partner. What problem do you need solved?', 'choice',
       '["Custom ERP Solution / Software","Ecommerce Solutions","Marketing Solutions"]'
WHERE NOT EXISTS (SELECT 1 FROM whatsapp_flow_steps);

-- No default user is created here on purpose — the first time you open
-- /admin/login.php with an empty `users` table, it redirects to
-- /admin/signup.php so you can create the first admin account yourself.
