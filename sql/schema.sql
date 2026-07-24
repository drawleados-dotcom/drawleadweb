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

-- Admin-manageable Departments/Services list for case studies — the
-- checkbox list on the Case Study edit screen is driven by this table,
-- not a fixed set, so the admin can add new ones over time.
CREATE TABLE IF NOT EXISTS case_study_services (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(190) NOT NULL UNIQUE,
  sort_order  INT NOT NULL DEFAULT 0,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Site-wide "on open" consultation popup, admin-managed via admin/popup.php.
CREATE TABLE IF NOT EXISTS site_popup (
  id               INT PRIMARY KEY DEFAULT 1,
  enabled          TINYINT(1) NOT NULL DEFAULT 0,
  image            VARCHAR(255) NOT NULL DEFAULT '',
  image_alt        VARCHAR(190) NOT NULL DEFAULT '',
  title            VARCHAR(190) NOT NULL DEFAULT '',
  description      VARCHAR(400) NOT NULL DEFAULT '',
  points           TEXT,                                    -- one point per line, first 4 shown with a checkmark
  cta_text         VARCHAR(100) NOT NULL DEFAULT 'Book a Free Consultation',
  cta_use_booking  TINYINT(1) NOT NULL DEFAULT 1,            -- 1 = opens the existing booking popup, 0 = uses cta_link
  cta_link         VARCHAR(255) NOT NULL DEFAULT '',
  trigger_delay          TINYINT(1) NOT NULL DEFAULT 1,      -- show after a few seconds
  trigger_new_page       TINYINT(1) NOT NULL DEFAULT 0,      -- show again on every new page navigated to
  trigger_refresh        TINYINT(1) NOT NULL DEFAULT 0,      -- show again on every page refresh
  trigger_scroll_section TINYINT(1) NOT NULL DEFAULT 0,      -- show when the 4th section scrolls into view
  updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
  'about-us'),
('Custom ERP Solution', '/custom-erp-solution',
  'Custom ERP Solution | Drawlead',
  'A custom ERP shaped around how your business actually works — modules mapped to your real workflows, role-based access, and migration off spreadsheets and legacy systems.',
  'custom-erp-solution'),
('Ecommerce Solutions', '/ecommerce-solutions',
  'Ecommerce Solutions | Drawlead',
  'Shopify, WooCommerce, and custom storefront builds with live inventory sync and automated order, invoice, and GST workflows — one connected stack from storefront to fulfilment.',
  'ecommerce-solutions'),
('Marketing Solutions', '/marketing-solutions',
  'Marketing Solutions | Drawlead',
  'Technical SEO and performance marketing that fix the leak between lead and conversion — Google, Meta, and LinkedIn campaigns with instant WhatsApp and email follow-up on every lead.',
  'marketing-solutions'),
('Platform — Management', '/platform-management',
  'Management Platform | Drawlead',
  'Centralized dashboards and operational visibility for faster, smarter business decisions — one view of how your business is actually performing.',
  'platform-module'),
('Platform — Sales', '/platform-sales',
  'Sales Platform | Drawlead',
  'Manage leads, pipelines, customers, and revenue operations from one unified platform — CRM, pipeline, and invoicing in one place.',
  'platform-module'),
('Platform — Marketing', '/platform-marketing',
  'Marketing Platform | Drawlead',
  'Track campaigns, automate WhatsApp & email, and improve customer engagement at scale, with every lead attributed back to its source.',
  'platform-module'),
('Platform — Operations', '/platform-operations',
  'Operations Platform | Drawlead',
  'Streamline activities, inventory, and vendor management with intelligent process automation.',
  'platform-module'),
('Platform — Finance', '/platform-finance',
  'Finance Platform | Drawlead',
  'Centralize billing, expenses, financial reporting, and accounting integrations seamlessly.',
  'platform-module'),
('Platform — HR', '/platform-hr',
  'HR Platform | Drawlead',
  'Manage employees, attendance, payroll workflows, and leave management efficiently.',
  'platform-module'),
('Platform — R&D', '/platform-rd',
  'R&D Platform | Drawlead',
  'Enable innovation with AI-powered automation, predictive analytics, and custom intelligence.',
  'platform-module');

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

INSERT IGNORE INTO case_study_services (name, sort_order) VALUES
('Custom ERP Solution', 1),
('Ecommerce Solutions', 2),
('Marketing Solutions', 3);

-- Disabled by default (enabled=0) so it doesn't start popping up on the
-- live site before the admin has actually configured any content.
INSERT IGNORE INTO site_popup (id) VALUES (1);

INSERT INTO whatsapp_flow_steps (step_order, message, step_type, options)
SELECT 1, 'Hi! Welcome to Drawlead — your digital solutions partner. What problem do you need solved?', 'choice',
       '["Custom ERP Solution / Software","Ecommerce Solutions","Marketing Solutions"]'
WHERE NOT EXISTS (SELECT 1 FROM whatsapp_flow_steps);

-- No default user is created here on purpose — the first time you open
-- /admin/login.php with an empty `users` table, it redirects to
-- /admin/signup.php so you can create the first admin account yourself.
