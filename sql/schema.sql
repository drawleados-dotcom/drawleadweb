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
  status            ENUM('draft','published') NOT NULL DEFAULT 'published',
  show_in_menu      TINYINT(1) NOT NULL DEFAULT 0,      -- plain nav links only (Home, Home 2.0, About Us)
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
  image            TEXT,                                    -- either an /uploads/ filename, or a data: URI for a built-in default graphic
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

-- Admin-manageable sidebar CTA block (Text / Image / CTA), shown below
-- the always-dynamic "Recent Posts" list on blog post and case study pages.
CREATE TABLE IF NOT EXISTS site_sidebar (
  id               INT PRIMARY KEY DEFAULT 1,
  enabled          TINYINT(1) NOT NULL DEFAULT 1,
  image            VARCHAR(255) NOT NULL DEFAULT '',
  image_alt        VARCHAR(190) NOT NULL DEFAULT '',
  title            VARCHAR(190) NOT NULL DEFAULT 'Book a Consultation',
  text             TEXT,
  cta_text         VARCHAR(100) NOT NULL DEFAULT 'Book a Free Consultation',
  cta_use_booking  TINYINT(1) NOT NULL DEFAULT 1,
  cta_link         VARCHAR(255) NOT NULL DEFAULT '',
  updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Drawlead Analyze — enter a URL, get a rule-based CRO scorecard plus
-- a rebuilt version of the page's own copy in Drawlead's CRO layout,
-- saved at a shareable /analyze/{token}. ──

CREATE TABLE IF NOT EXISTS analyze_reports (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  token                 VARCHAR(32) NOT NULL UNIQUE,
  target_url            VARCHAR(500) NOT NULL,
  page_title            VARCHAR(300) NOT NULL DEFAULT '',
  page_description      VARCHAR(500) NOT NULL DEFAULT '',
  cro_score             TINYINT UNSIGNED NOT NULL DEFAULT 0,
  sub_scores            TEXT,                                 -- JSON: {label: score}
  target_audience       VARCHAR(190) NOT NULL DEFAULT '',
  audience_match_score  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  changes_json          TEXT,                                 -- JSON: [{title, reasoning, category}]
  new_page_json         TEXT,                                 -- JSON: extracted content used for the Tab 1 CRO rebuild
  created_at            DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (created_at)
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
('Platform — Inventory Management', '/platform-inventory',
  'Inventory Management Platform | Drawlead',
  'Track stock across every warehouse and channel, get alerted before you run out, and stop guessing what you actually have on hand.',
  'platform-module'),
('Industry — Construction & Real Estate', '/industry-construction',
  'Construction & Real Estate ERP Solution | Drawlead',
  'Run multi-site construction and real estate operations from a single system — instead of a different spreadsheet for every project.',
  'industry'),
('Industry — Healthcare & Wellness', '/industry-healthcare',
  'Healthcare & Wellness ERP Solution | Drawlead',
  'Run clinics and wellness centers where scheduling, billing, and patient follow-ups never depend on a phone call.',
  'industry'),
('Industry — Manufacturing', '/industry-manufacturing',
  'Manufacturing ERP Solution | Drawlead',
  'Track production runs, raw material stock, and quality checks from a single dashboard instead of a factory floor full of paper logs.',
  'industry'),
('Industry — Marketing Agencies', '/industry-agencies',
  'Marketing Agencies ERP Solution | Drawlead',
  'Run an agency where client projects, leads, and delivery timelines all live in one place instead of six different tools.',
  'industry'),
('Industry — Retail & E-Commerce', '/industry-retail',
  'Retail & E-Commerce ERP Solution | Drawlead',
  'Sell across stores and online channels with stock, orders, and customers synced in real time — not reconciled at the end of the day.',
  'industry'),
('Industry — Logistics & Transport', '/industry-logistics',
  'Logistics & Transport ERP Solution | Drawlead',
  'Track fleet, deliveries, and compliance documents from one dashboard instead of a driver group chat and a filing cabinet.',
  'industry'),
('Industry — Jewellery & Gems', '/industry-jewellery',
  'Jewellery & Gems ERP Solution | Drawlead',
  'Run a jewellery business where stock, purity, and billing are never a guessing game — synced across every counter and branch.',
  'industry'),
('Industry — Education & Training', '/industry-education',
  'Education & Training ERP Solution | Drawlead',
  'From admissions to fee collection to attendance, manage every part of running a school or training institute in one system.',
  'industry'),
('Industry — Hospitality & Restaurants', '/industry-hospitality',
  'Hospitality & Restaurants ERP Solution | Drawlead',
  'Manage orders, table turnover, and kitchen inventory from one dashboard instead of a POS, a notebook, and a supplier call sheet.',
  'industry'),
('Industry — Automotive & Auto Services', '/industry-automotive',
  'Automotive & Auto Services ERP Solution | Drawlead',
  'Run a dealership or service center where job cards, spare parts, and billing all live in one place — not three.',
  'industry'),
('Industry — Textile & Apparel', '/industry-textile',
  'Textile & Apparel ERP Solution | Drawlead',
  'Track raw material, production stages, and finished goods stock across every unit and showroom without a separate spreadsheet for each.',
  'industry'),
('Industry — Pharmaceuticals & Distribution', '/industry-pharma',
  'Pharmaceuticals & Distribution ERP Solution | Drawlead',
  'Manage batch tracking, expiry alerts, and regulatory compliance across your entire distribution network from one system.',
  'industry'),
('Industry — Professional Services', '/industry-professional',
  'Professional Services ERP Solution | Drawlead',
  'Run a law firm, accounting practice, or consultancy where client work, billing, and deadlines are never scattered across inboxes.',
  'industry'),
('Industry — Food & Beverage Manufacturing', '/industry-food-beverage',
  'Food & Beverage Manufacturing ERP Solution | Drawlead',
  'Track raw ingredients, batch production, and quality checks across every shift, with full traceability from ingredient to finished product.',
  'industry'),
('Industry — IT & Software Services', '/industry-it-software',
  'IT & Software Services ERP Solution | Drawlead',
  'Run a software or IT services company where project timelines, resource allocation, and client billing all live in one connected system.',
  'industry'),
('Industry — Financial Services & NBFCs', '/industry-financial',
  'Financial Services & NBFCs ERP Solution | Drawlead',
  'Manage loan accounts, repayment tracking, and regulatory compliance from one system built for how NBFCs and financial services actually operate.',
  'industry'),
('Industry — Agriculture & Agri-Business', '/industry-agriculture',
  'Agriculture & Agri-Business ERP Solution | Drawlead',
  'Track procurement, storage, and distribution of agricultural produce across every warehouse and season without losing visibility between harvests.',
  'industry'),
('Industry — Event Management', '/industry-events',
  'Event Management ERP Solution | Drawlead',
  'Run an event management business where bookings, vendor coordination, and budgets are tracked in one place, not across a dozen chat threads.',
  'industry'),
('Industry — Beauty & Salon Chains', '/industry-beauty',
  'Beauty & Salon Chains ERP Solution | Drawlead',
  'Manage appointments, staff schedules, and product inventory across every branch of your salon or spa chain from one dashboard.',
  'industry'),
('Industry — Wholesale & Distribution', '/industry-wholesale',
  'Wholesale & Distribution ERP Solution | Drawlead',
  'Manage dealer orders, stock allocation, and distribution logistics from one system instead of juggling order books and phone calls.',
  'industry');

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Home 2.0', '/home-2', 'Drawlead | Intelligent Business Operating System',
  'Drawlead helps MSMEs and SMEs grow with websites, SEO, performance marketing and a unified business operating system.',
  'home2');

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Analyze', '/analyze',
  'Drawlead Analyze — Free CRO Website Analysis',
  'Enter your website URL and get a free, rule-based conversion-rate-optimization scorecard plus a rebuilt version of your page in a modern, high-converting layout.',
  'analyze');

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Ulagai', '/ulagai',
  'Ulagai — High-Performance Ecommerce Stores | Drawlead',
  'We engineer high-performance ecommerce stores designed to convert traffic into consistent online orders, for scaling D2C brands serious about growth.',
  'ulagai');

UPDATE pages SET show_in_menu = 1 WHERE slug IN ('/', '/home-2', '/about-us', '/analyze');

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
-- The default popup image is a built-in data: URI graphic (no /uploads/
-- file needed) — same value seeded by migration 017 for existing installs.
INSERT IGNORE INTO site_popup (id, image, image_alt, title, description, points) VALUES
(1,
 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDQwIiBoZWlnaHQ9IjU2MCIgdmlld0JveD0iMCAwIDQ0MCA1NjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgPGRlZnM+CiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImciIHgxPSIwIiB5MT0iMCIgeDI9IjEiIHkyPSIxIj4KICAgICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzMyYjQ2ZiIvPgogICAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMwZjVjM2YiLz4KICAgIDwvbGluZWFyR3JhZGllbnQ+CiAgICA8cGF0dGVybiBpZD0iZ3JpZCIgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBwYXR0ZXJuVW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KICAgICAgPHBhdGggZD0iTTQwIDBIMFY0MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDcpIiBzdHJva2Utd2lkdGg9IjEiLz4KICAgIDwvcGF0dGVybj4KICA8L2RlZnM+CiAgPHJlY3Qgd2lkdGg9IjQ0MCIgaGVpZ2h0PSI1NjAiIGZpbGw9InVybCgjZykiLz4KICA8cmVjdCB3aWR0aD0iNDQwIiBoZWlnaHQ9IjU2MCIgZmlsbD0idXJsKCNncmlkKSIvPgogIDxjaXJjbGUgY3g9IjM5MCIgY3k9IjUwIiByPSIxNzAiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNykiLz4KICA8Y2lyY2xlIGN4PSIyMCIgY3k9IjU0MCIgcj0iMTMwIiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDYpIi8+CgogIDxyZWN0IHg9IjQ4IiB5PSI2OCIgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiByeD0iMTYiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4xNSkiLz4KICA8cmVjdCB4PSI2MiIgeT0iODYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIyOCIgcng9IjQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI2ZmZmZmZiIgc3Ryb2tlLXdpZHRoPSIyLjUiLz4KICA8bGluZSB4MT0iNjIiIHkxPSI5NiIgeDI9Ijk0IiB5Mj0iOTYiIHN0cm9rZT0iI2ZmZmZmZiIgc3Ryb2tlLXdpZHRoPSIyLjUiLz4KICA8bGluZSB4MT0iNzAiIHkxPSI4MCIgeDI9IjcwIiB5Mj0iOTAiIHN0cm9rZT0iI2ZmZmZmZiIgc3Ryb2tlLXdpZHRoPSIyLjUiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIvPgogIDxsaW5lIHgxPSI4NiIgeTE9IjgwIiB4Mj0iODYiIHkyPSI5MCIgc3Ryb2tlPSIjZmZmZmZmIiBzdHJva2Utd2lkdGg9IjIuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+CiAgPGNpcmNsZSBjeD0iNzgiIGN5PSIxMDUiIHI9IjMiIGZpbGw9IiNmZmZmZmYiLz4KCiAgPHRleHQgeD0iNDgiIHk9IjMzMCIgZm9udC1mYW1pbHk9IkFyaWFsLCBIZWx2ZXRpY2EsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iNzYiIGZvbnQtd2VpZ2h0PSI4MDAiIGZpbGw9IiNmZmZmZmYiPjMwIE1pbjwvdGV4dD4KICA8dGV4dCB4PSI0OCIgeT0iMzYyIiBmb250LWZhbWlseT0iQXJpYWwsIEhlbHZldGljYSwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZm9udC13ZWlnaHQ9IjcwMCIgbGV0dGVyLXNwYWNpbmc9IjIiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC44KSI+RlJFRSBTVFJBVEVHWSBDQUxMPC90ZXh0PgoKICA8bGluZSB4MT0iNDgiIHkxPSIzOTQiIHgyPSIyMTAiIHkyPSIzOTQiIHN0cm9rZT0icmdiYSgyNTUsMjU1LDI1NSwwLjMpIiBzdHJva2Utd2lkdGg9IjIiLz4KCiAgPHRleHQgeD0iNDgiIHk9IjQyOCIgZm9udC1mYW1pbHk9IkFyaWFsLCBIZWx2ZXRpY2EsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSI2MDAiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC45KSI+Tm8gb2JsaWdhdGlvbjwvdGV4dD4KICA8dGV4dCB4PSI0OCIgeT0iNDU0IiBmb250LWZhbWlseT0iQXJpYWwsIEhlbHZldGljYSwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZm9udC13ZWlnaHQ9IjYwMCIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjkpIj5SZXNwb25zZSB3aXRoaW4gMjQgaG91cnM8L3RleHQ+CiAgPHRleHQgeD0iNDgiIHk9IjQ4MCIgZm9udC1mYW1pbHk9IkFyaWFsLCBIZWx2ZXRpY2EsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSI2MDAiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC45KSI+QnVpbHQgYnkgRHJhd2xlYWQgc3BlY2lhbGlzdHM8L3RleHQ+Cjwvc3ZnPgo=',
 'Free 30-minute consultation with a Drawlead specialist',
 'Build Your Business OS',
 'Book a free 30-minute call with a Drawlead specialist. We map your current workflows and show you exactly what a unified ERP, ecommerce, and marketing system looks like for your business, with no obligation.',
 'Custom ERP built around your workflows
Ecommerce and marketing systems that connect
AI automation for repetitive work
Free, no-obligation consultation');

INSERT IGNORE INTO site_sidebar (id, title, text) VALUES
(1, 'Book a Consultation', 'Ready to take your business to the next level?');

INSERT INTO whatsapp_flow_steps (step_order, message, step_type, options)
SELECT 1, 'Hi! Welcome to Drawlead — your digital solutions partner. What problem do you need solved?', 'choice',
       '["Custom ERP Solution / Software","Ecommerce Solutions","Marketing Solutions"]'
WHERE NOT EXISTS (SELECT 1 FROM whatsapp_flow_steps);

-- No default user is created here on purpose — the first time you open
-- /admin/login.php with an empty `users` table, it redirects to
-- /admin/signup.php so you can create the first admin account yourself.
