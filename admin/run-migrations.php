<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

function migration_002_statements(): array
{
    return [
        "ALTER TABLE blogs ADD COLUMN featured_image_alt VARCHAR(190) NOT NULL DEFAULT '' AFTER featured_image, "
      . "ADD COLUMN scheduled_at DATETIME NULL AFTER status, "
      . "MODIFY COLUMN status ENUM('draft','published','scheduled') NOT NULL DEFAULT 'draft'",
    ];
}

function migration_003_statements(): array
{
    return [
        "CREATE TABLE IF NOT EXISTS booking_availability (
            id INT PRIMARY KEY DEFAULT 1,
            days_of_week VARCHAR(20) NOT NULL DEFAULT '1,2,3,4,5',
            start_time TIME NOT NULL DEFAULT '10:00:00',
            end_time TIME NOT NULL DEFAULT '18:00:00',
            slot_interval_minutes INT NOT NULL DEFAULT 30,
            range_start DATE NULL,
            range_end DATE NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "INSERT IGNORE INTO booking_availability (id, range_start, range_end)
         VALUES (1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY))",

        "CREATE TABLE IF NOT EXISTS booking_notification_emails (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(190) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS booking_form_fields (
            id INT AUTO_INCREMENT PRIMARY KEY,
            field_key VARCHAR(100) NOT NULL UNIQUE,
            label VARCHAR(190) NOT NULL,
            field_type ENUM('text','email','phone','textarea','select','radio','checkbox','date') NOT NULL DEFAULT 'text',
            field_role ENUM('none','name','email') NOT NULL DEFAULT 'none',
            options TEXT NULL,
            placeholder VARCHAR(190) NOT NULL DEFAULT '',
            is_required TINYINT(1) NOT NULL DEFAULT 1,
            sort_order INT NOT NULL DEFAULT 0,
            conditional_field_id INT NULL,
            conditional_value VARCHAR(190) NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (conditional_field_id) REFERENCES booking_form_fields(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "INSERT IGNORE INTO booking_form_fields (field_key, label, field_type, field_role, placeholder, is_required, sort_order) VALUES
         ('name', 'Full Name', 'text', 'name', 'Your name', 1, 1),
         ('email', 'Email Address', 'email', 'email', 'you@company.com', 1, 2),
         ('phone', 'Phone Number', 'phone', 'none', '+91 98765 43210', 1, 3),
         ('company', 'Company Name', 'text', 'none', 'Your business name', 0, 4)",

        "CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            booking_date DATE NOT NULL,
            booking_time TIME NOT NULL,
            form_data LONGTEXT NOT NULL,
            name VARCHAR(190) NOT NULL DEFAULT '',
            email VARCHAR(190) NOT NULL DEFAULT '',
            status ENUM('confirmed','cancelled') NOT NULL DEFAULT 'confirmed',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_slot (booking_date, booking_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];
}

function migration_004_statements(): array
{
    return [
        "CREATE TABLE IF NOT EXISTS whatsapp_flow_steps (
            id INT AUTO_INCREMENT PRIMARY KEY,
            step_order INT NOT NULL DEFAULT 0,
            message TEXT NOT NULL,
            step_type ENUM('choice','text') NOT NULL DEFAULT 'choice',
            options TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "INSERT INTO whatsapp_flow_steps (step_order, message, step_type, options)
         SELECT 1, 'Hi! Welcome to Drawlead — your digital solutions partner. What problem do you need solved?', 'choice',
                '[\"Custom ERP Solution / Software\",\"Ecommerce Solutions\",\"Marketing Solutions\"]'
         WHERE NOT EXISTS (SELECT 1 FROM whatsapp_flow_steps)",

        "CREATE TABLE IF NOT EXISTS whatsapp_leads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            answers LONGTEXT NOT NULL,
            phone VARCHAR(40) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];
}

function migration_005_statements(): array
{
    return [
        "CREATE TABLE IF NOT EXISTS case_studies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(190) NOT NULL,
            slug VARCHAR(190) NOT NULL UNIQUE,
            meta_title VARCHAR(190) NOT NULL DEFAULT '',
            meta_description VARCHAR(320) NOT NULL DEFAULT '',
            client_name VARCHAR(190) NOT NULL DEFAULT '',
            description VARCHAR(400) NOT NULL DEFAULT '',
            problem TEXT,
            solution TEXT,
            process TEXT,
            result TEXT,
            outcome TEXT,
            testimonial TEXT,
            testimonial_author VARCHAR(190) NOT NULL DEFAULT '',
            services VARCHAR(255) NOT NULL DEFAULT '',
            website_link VARCHAR(255) NOT NULL DEFAULT '',
            erp_link VARCHAR(255) NOT NULL DEFAULT '',
            desktop_image VARCHAR(255) NOT NULL DEFAULT '',
            mobile_image VARCHAR(255) NOT NULL DEFAULT '',
            result_image VARCHAR(255) NOT NULL DEFAULT '',
            team TEXT,
            status ENUM('draft','published') NOT NULL DEFAULT 'draft',
            author_id INT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "ALTER TABLE user_access MODIFY COLUMN item_type ENUM('page','blogs','case_studies') NOT NULL",
    ];
}

function migration_006_statements(): array
{
    $seoColumns = "
            ADD COLUMN focus_keyword VARCHAR(190) NOT NULL DEFAULT '',
            ADD COLUMN canonical_url VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN robots_index ENUM('index','noindex') NOT NULL DEFAULT 'index',
            ADD COLUMN robots_follow ENUM('follow','nofollow') NOT NULL DEFAULT 'follow',
            ADD COLUMN og_title VARCHAR(190) NOT NULL DEFAULT '',
            ADD COLUMN og_description VARCHAR(320) NOT NULL DEFAULT '',
            ADD COLUMN og_image VARCHAR(255) NOT NULL DEFAULT ''";

    return [
        "ALTER TABLE pages $seoColumns",
        "ALTER TABLE blogs $seoColumns",
        "ALTER TABLE case_studies $seoColumns",
    ];
}

function migration_007_statements(): array
{
    return [
        "CREATE TABLE IF NOT EXISTS case_study_services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(190) NOT NULL UNIQUE,
            sort_order INT NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "INSERT IGNORE INTO case_study_services (name, sort_order) VALUES
         ('Custom ERP Solution', 1),
         ('Ecommerce Solutions', 2),
         ('Marketing Solutions', 3)",
    ];
}

function migration_008_statements(): array
{
    return [
        "CREATE TABLE IF NOT EXISTS site_popup (
            id INT PRIMARY KEY DEFAULT 1,
            enabled TINYINT(1) NOT NULL DEFAULT 0,
            image VARCHAR(255) NOT NULL DEFAULT '',
            image_alt VARCHAR(190) NOT NULL DEFAULT '',
            title VARCHAR(190) NOT NULL DEFAULT '',
            description VARCHAR(400) NOT NULL DEFAULT '',
            points TEXT,
            cta_text VARCHAR(100) NOT NULL DEFAULT 'Book a Free Consultation',
            cta_use_booking TINYINT(1) NOT NULL DEFAULT 1,
            cta_link VARCHAR(255) NOT NULL DEFAULT '',
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "INSERT IGNORE INTO site_popup (id) VALUES (1)",
    ];
}

function migration_009_statements(): array
{
    return [
        "ALTER TABLE site_popup
            ADD COLUMN trigger_delay TINYINT(1) NOT NULL DEFAULT 1,
            ADD COLUMN trigger_new_page TINYINT(1) NOT NULL DEFAULT 0,
            ADD COLUMN trigger_refresh TINYINT(1) NOT NULL DEFAULT 0,
            ADD COLUMN trigger_scroll_section TINYINT(1) NOT NULL DEFAULT 0",
    ];
}

function migration_010_statements(): array
{
    return [
        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Custom ERP Solution', '/custom-erp-solution',
           'Custom ERP Solution | Drawlead',
           'A custom ERP shaped around how your business actually works — modules mapped to your real workflows, role-based access, and migration off spreadsheets and legacy systems.',
           'custom-erp-solution')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Ecommerce Solutions', '/ecommerce-solutions',
           'Ecommerce Solutions | Drawlead',
           'Shopify, WooCommerce, and custom storefront builds with live inventory sync and automated order, invoice, and GST workflows — one connected stack from storefront to fulfilment.',
           'ecommerce-solutions')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Marketing Solutions', '/marketing-solutions',
           'Marketing Solutions | Drawlead',
           'Technical SEO and performance marketing that fix the leak between lead and conversion — Google, Meta, and LinkedIn campaigns with instant WhatsApp and email follow-up on every lead.',
           'marketing-solutions')",
    ];
}

function migration_011_statements(): array
{
    return [
        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Platform — Management', '/platform-management',
           'Management Platform | Drawlead',
           'Centralized dashboards and operational visibility for faster, smarter business decisions — one view of how your business is actually performing.',
           'platform-module')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Platform — Sales', '/platform-sales',
           'Sales Platform | Drawlead',
           'Manage leads, pipelines, customers, and revenue operations from one unified platform — CRM, pipeline, and invoicing in one place.',
           'platform-module')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Platform — Marketing', '/platform-marketing',
           'Marketing Platform | Drawlead',
           'Track campaigns, automate WhatsApp & email, and improve customer engagement at scale, with every lead attributed back to its source.',
           'platform-module')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Platform — Operations', '/platform-operations',
           'Operations Platform | Drawlead',
           'Streamline activities, inventory, and vendor management with intelligent process automation.',
           'platform-module')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Platform — Finance', '/platform-finance',
           'Finance Platform | Drawlead',
           'Centralize billing, expenses, financial reporting, and accounting integrations seamlessly.',
           'platform-module')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Platform — HR', '/platform-hr',
           'HR Platform | Drawlead',
           'Manage employees, attendance, payroll workflows, and leave management efficiently.',
           'platform-module')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Platform — R&D', '/platform-rd',
           'R&D Platform | Drawlead',
           'Enable innovation with AI-powered automation, predictive analytics, and custom intelligence.',
           'platform-module')",
    ];
}

function migration_012_statements(): array
{
    return [
        "UPDATE pages SET
            name = 'Platform — Inventory Management',
            slug = '/platform-inventory',
            meta_title = 'Inventory Management Platform | Drawlead',
            meta_description = 'Track stock across every warehouse and channel, get alerted before you run out, and stop guessing what you actually have on hand.'
         WHERE slug = '/platform-rd'",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Platform — Inventory Management', '/platform-inventory',
           'Inventory Management Platform | Drawlead',
           'Track stock across every warehouse and channel, get alerted before you run out, and stop guessing what you actually have on hand.',
           'platform-module')",
    ];
}

function migration_013_statements(): array
{
    return [
        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Construction & Real Estate', '/industry-construction',
           'Construction & Real Estate ERP Solution | Drawlead',
           'Run multi-site construction and real estate operations from a single system — instead of a different spreadsheet for every project.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Healthcare & Wellness', '/industry-healthcare',
           'Healthcare & Wellness ERP Solution | Drawlead',
           'Run clinics and wellness centers where scheduling, billing, and patient follow-ups never depend on a phone call.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Manufacturing', '/industry-manufacturing',
           'Manufacturing ERP Solution | Drawlead',
           'Track production runs, raw material stock, and quality checks from a single dashboard instead of a factory floor full of paper logs.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Marketing Agencies', '/industry-agencies',
           'Marketing Agencies ERP Solution | Drawlead',
           'Run an agency where client projects, leads, and delivery timelines all live in one place instead of six different tools.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Retail & E-Commerce', '/industry-retail',
           'Retail & E-Commerce ERP Solution | Drawlead',
           'Sell across stores and online channels with stock, orders, and customers synced in real time — not reconciled at the end of the day.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Logistics & Transport', '/industry-logistics',
           'Logistics & Transport ERP Solution | Drawlead',
           'Track fleet, deliveries, and compliance documents from one dashboard instead of a driver group chat and a filing cabinet.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Jewellery & Gems', '/industry-jewellery',
           'Jewellery & Gems ERP Solution | Drawlead',
           'Run a jewellery business where stock, purity, and billing are never a guessing game — synced across every counter and branch.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Education & Training', '/industry-education',
           'Education & Training ERP Solution | Drawlead',
           'From admissions to fee collection to attendance, manage every part of running a school or training institute in one system.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Hospitality & Restaurants', '/industry-hospitality',
           'Hospitality & Restaurants ERP Solution | Drawlead',
           'Manage orders, table turnover, and kitchen inventory from one dashboard instead of a POS, a notebook, and a supplier call sheet.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Automotive & Auto Services', '/industry-automotive',
           'Automotive & Auto Services ERP Solution | Drawlead',
           'Run a dealership or service center where job cards, spare parts, and billing all live in one place — not three.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Textile & Apparel', '/industry-textile',
           'Textile & Apparel ERP Solution | Drawlead',
           'Track raw material, production stages, and finished goods stock across every unit and showroom without a separate spreadsheet for each.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Pharmaceuticals & Distribution', '/industry-pharma',
           'Pharmaceuticals & Distribution ERP Solution | Drawlead',
           'Manage batch tracking, expiry alerts, and regulatory compliance across your entire distribution network from one system.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Professional Services', '/industry-professional',
           'Professional Services ERP Solution | Drawlead',
           'Run a law firm, accounting practice, or consultancy where client work, billing, and deadlines are never scattered across inboxes.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Food & Beverage Manufacturing', '/industry-food-beverage',
           'Food & Beverage Manufacturing ERP Solution | Drawlead',
           'Track raw ingredients, batch production, and quality checks across every shift, with full traceability from ingredient to finished product.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — IT & Software Services', '/industry-it-software',
           'IT & Software Services ERP Solution | Drawlead',
           'Run a software or IT services company where project timelines, resource allocation, and client billing all live in one connected system.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Financial Services & NBFCs', '/industry-financial',
           'Financial Services & NBFCs ERP Solution | Drawlead',
           'Manage loan accounts, repayment tracking, and regulatory compliance from one system built for how NBFCs and financial services actually operate.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Agriculture & Agri-Business', '/industry-agriculture',
           'Agriculture & Agri-Business ERP Solution | Drawlead',
           'Track procurement, storage, and distribution of agricultural produce across every warehouse and season without losing visibility between harvests.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Event Management', '/industry-events',
           'Event Management ERP Solution | Drawlead',
           'Run an event management business where bookings, vendor coordination, and budgets are tracked in one place, not across a dozen chat threads.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Beauty & Salon Chains', '/industry-beauty',
           'Beauty & Salon Chains ERP Solution | Drawlead',
           'Manage appointments, staff schedules, and product inventory across every branch of your salon or spa chain from one dashboard.',
           'industry')",

        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Industry — Wholesale & Distribution', '/industry-wholesale',
           'Wholesale & Distribution ERP Solution | Drawlead',
           'Manage dealer orders, stock allocation, and distribution logistics from one system instead of juggling order books and phone calls.',
           'industry')",

    ];
}

function migration_014_statements(): array
{
    return [
        "CREATE TABLE IF NOT EXISTS site_sidebar (
            id INT PRIMARY KEY DEFAULT 1,
            enabled TINYINT(1) NOT NULL DEFAULT 1,
            image VARCHAR(255) NOT NULL DEFAULT '',
            image_alt VARCHAR(190) NOT NULL DEFAULT '',
            title VARCHAR(190) NOT NULL DEFAULT 'Book a Consultation',
            text TEXT,
            cta_text VARCHAR(100) NOT NULL DEFAULT 'Book a Free Consultation',
            cta_use_booking TINYINT(1) NOT NULL DEFAULT 1,
            cta_link VARCHAR(255) NOT NULL DEFAULT '',
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "INSERT IGNORE INTO site_sidebar (id, title, text) VALUES
         (1, 'Book a Consultation', 'Ready to take your business to the next level?')",
    ];
}

function migration_015_statements(): array
{
    return [
        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Home 2.0', '/home-2',
           'Drawlead | Intelligent Business Operating System',
           'Drawlead helps MSMEs and SMEs grow with websites, SEO, performance marketing and a unified business operating system.',
           'home2')",
    ];
}

function migration_016_statements(): array
{
    return [
        "ALTER TABLE pages
         ADD COLUMN status ENUM('draft','published') NOT NULL DEFAULT 'published' AFTER slug,
         ADD COLUMN show_in_menu TINYINT(1) NOT NULL DEFAULT 0 AFTER status",

        "UPDATE pages SET show_in_menu = 1 WHERE slug IN ('/', '/home-2', '/about-us')",
    ];
}

/**
 * Unlike the other migration_XXX_statements() functions, this one needs
 * $pdo: the content UPDATE below must only ever apply once. "Run All
 * Pending Migrations" always re-executes every migration's statements on
 * every click (that's how it stays safe for CREATE TABLE/ADD COLUMN/INSERT
 * IGNORE elsewhere), so a plain unconditional UPDATE here would silently
 * overwrite anything an admin later edits in Admin → Popup. Gating it on
 * the structural column-type change means it can't re-fire after the
 * first successful run, regardless of what the content looks like by then.
 */
function migration_017_statements(PDO $pdo): array
{
    $popupImage = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDQwIiBoZWlnaHQ9IjU2MCIgdmlld0JveD0iMCAwIDQ0MCA1NjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgPGRlZnM+CiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImciIHgxPSIwIiB5MT0iMCIgeDI9IjEiIHkyPSIxIj4KICAgICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzMyYjQ2ZiIvPgogICAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMwZjVjM2YiLz4KICAgIDwvbGluZWFyR3JhZGllbnQ+CiAgICA8cGF0dGVybiBpZD0iZ3JpZCIgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBwYXR0ZXJuVW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KICAgICAgPHBhdGggZD0iTTQwIDBIMFY0MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDcpIiBzdHJva2Utd2lkdGg9IjEiLz4KICAgIDwvcGF0dGVybj4KICA8L2RlZnM+CiAgPHJlY3Qgd2lkdGg9IjQ0MCIgaGVpZ2h0PSI1NjAiIGZpbGw9InVybCgjZykiLz4KICA8cmVjdCB3aWR0aD0iNDQwIiBoZWlnaHQ9IjU2MCIgZmlsbD0idXJsKCNncmlkKSIvPgogIDxjaXJjbGUgY3g9IjM5MCIgY3k9IjUwIiByPSIxNzAiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNykiLz4KICA8Y2lyY2xlIGN4PSIyMCIgY3k9IjU0MCIgcj0iMTMwIiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDYpIi8+CgogIDxyZWN0IHg9IjQ4IiB5PSI2OCIgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiByeD0iMTYiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4xNSkiLz4KICA8cmVjdCB4PSI2MiIgeT0iODYiIHdpZHRoPSIzMiIgaGVpZ2h0PSIyOCIgcng9IjQiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI2ZmZmZmZiIgc3Ryb2tlLXdpZHRoPSIyLjUiLz4KICA8bGluZSB4MT0iNjIiIHkxPSI5NiIgeDI9Ijk0IiB5Mj0iOTYiIHN0cm9rZT0iI2ZmZmZmZiIgc3Ryb2tlLXdpZHRoPSIyLjUiLz4KICA8bGluZSB4MT0iNzAiIHkxPSI4MCIgeDI9IjcwIiB5Mj0iOTAiIHN0cm9rZT0iI2ZmZmZmZiIgc3Ryb2tlLXdpZHRoPSIyLjUiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIvPgogIDxsaW5lIHgxPSI4NiIgeTE9IjgwIiB4Mj0iODYiIHkyPSI5MCIgc3Ryb2tlPSIjZmZmZmZmIiBzdHJva2Utd2lkdGg9IjIuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+CiAgPGNpcmNsZSBjeD0iNzgiIGN5PSIxMDUiIHI9IjMiIGZpbGw9IiNmZmZmZmYiLz4KCiAgPHRleHQgeD0iNDgiIHk9IjMzMCIgZm9udC1mYW1pbHk9IkFyaWFsLCBIZWx2ZXRpY2EsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iNzYiIGZvbnQtd2VpZ2h0PSI4MDAiIGZpbGw9IiNmZmZmZmYiPjMwIE1pbjwvdGV4dD4KICA8dGV4dCB4PSI0OCIgeT0iMzYyIiBmb250LWZhbWlseT0iQXJpYWwsIEhlbHZldGljYSwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZm9udC13ZWlnaHQ9IjcwMCIgbGV0dGVyLXNwYWNpbmc9IjIiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC44KSI+RlJFRSBTVFJBVEVHWSBDQUxMPC90ZXh0PgoKICA8bGluZSB4MT0iNDgiIHkxPSIzOTQiIHgyPSIyMTAiIHkyPSIzOTQiIHN0cm9rZT0icmdiYSgyNTUsMjU1LDI1NSwwLjMpIiBzdHJva2Utd2lkdGg9IjIiLz4KCiAgPHRleHQgeD0iNDgiIHk9IjQyOCIgZm9udC1mYW1pbHk9IkFyaWFsLCBIZWx2ZXRpY2EsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSI2MDAiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC45KSI+Tm8gb2JsaWdhdGlvbjwvdGV4dD4KICA8dGV4dCB4PSI0OCIgeT0iNDU0IiBmb250LWZhbWlseT0iQXJpYWwsIEhlbHZldGljYSwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZm9udC13ZWlnaHQ9IjYwMCIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjkpIj5SZXNwb25zZSB3aXRoaW4gMjQgaG91cnM8L3RleHQ+CiAgPHRleHQgeD0iNDgiIHk9IjQ4MCIgZm9udC1mYW1pbHk9IkFyaWFsLCBIZWx2ZXRpY2EsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTQiIGZvbnQtd2VpZ2h0PSI2MDAiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC45KSI+QnVpbHQgYnkgRHJhd2xlYWQgc3BlY2lhbGlzdHM8L3RleHQ+Cjwvc3ZnPgo=';
    $popupImageAlt = 'Free 30-minute consultation with a Drawlead specialist';
    $popupTitle = 'Build Your Business OS';
    $popupDescription = 'Book a free 30-minute call with a Drawlead specialist. We map your current workflows and show you exactly what a unified ERP, ecommerce, and marketing system looks like for your business, with no obligation.';
    $popupPoints = "Custom ERP built around your workflows\nEcommerce and marketing systems that connect\nAI automation for repetitive work\nFree, no-obligation consultation";

    $statements = ["ALTER TABLE site_popup MODIFY COLUMN image TEXT"];

    $stmt = $pdo->query(
        "SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'site_popup' AND COLUMN_NAME = 'image'"
    );
    $alreadyWidened = strtolower((string) $stmt->fetchColumn()) === 'text';

    if (!$alreadyWidened) {
        $statements[] = "UPDATE site_popup SET
            image = '$popupImage',
            image_alt = '$popupImageAlt',
            title = '$popupTitle',
            description = '$popupDescription',
            points = '$popupPoints'
         WHERE id = 1";
    }

    return $statements;
}

function migration_018_statements(): array
{
    return [
        "CREATE TABLE IF NOT EXISTS analyze_reports (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];
}

function migration_019_statements(): array
{
    return [
        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Analyze', '/analyze',
           'Drawlead Analyze — Free CRO Website Analysis',
           'Enter your website URL and get a free, rule-based conversion-rate-optimization scorecard plus a rebuilt version of your page in a modern, high-converting layout.',
           'analyze')",

        "UPDATE pages SET show_in_menu = 1 WHERE slug = '/analyze'",
    ];
}

function migration_020_statements(): array
{
    return [
        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Ulagai', '/ulagai',
           'Ulagai — High-Performance Ecommerce Stores | Drawlead',
           'We engineer high-performance ecommerce stores designed to convert traffic into consistent online orders, for scaling D2C brands serious about growth.',
           'ulagai')",
    ];
}

function migration_021_statements(): array
{
    return [
        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('CRM', '/crm-solution',
           'CRM Solution | Drawlead',
           'A CRM built around how you actually sell — capture every lead, track every deal, and automate follow-ups, connected to the same ERP, ecommerce, and marketing systems you already run on.',
           'crm-solution')",

        "INSERT IGNORE INTO case_study_services (name, sort_order) VALUES ('CRM', 4)",
    ];
}

function migration_022_statements(): array
{
    return [
        "INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
         ('Home 5', '/home-5',
           'Drawlead | Intelligent Business Operating System',
           'Drawlead helps MSMEs and SMEs grow with websites, SEO, performance marketing and a unified business operating system.',
           'home5')",

        "UPDATE pages SET show_in_menu = 1 WHERE slug = '/home-5'",
    ];
}

$log = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $which = $_POST['run'] ?? '';
    $toRun = [];
    if ($which === '002' || $which === 'all') {
        $toRun['002'] = migration_002_statements();
    }
    if ($which === '003' || $which === 'all') {
        $toRun['003'] = migration_003_statements();
    }
    if ($which === '004' || $which === 'all') {
        $toRun['004'] = migration_004_statements();
    }
    if ($which === '005' || $which === 'all') {
        $toRun['005'] = migration_005_statements();
    }
    if ($which === '006' || $which === 'all') {
        $toRun['006'] = migration_006_statements();
    }
    if ($which === '007' || $which === 'all') {
        $toRun['007'] = migration_007_statements();
    }
    if ($which === '008' || $which === 'all') {
        $toRun['008'] = migration_008_statements();
    }
    if ($which === '009' || $which === 'all') {
        $toRun['009'] = migration_009_statements();
    }
    if ($which === '010' || $which === 'all') {
        $toRun['010'] = migration_010_statements();
    }
    if ($which === '011' || $which === 'all') {
        $toRun['011'] = migration_011_statements();
    }
    if ($which === '012' || $which === 'all') {
        $toRun['012'] = migration_012_statements();
    }
    if ($which === '013' || $which === 'all') {
        $toRun['013'] = migration_013_statements();
    }
    if ($which === '014' || $which === 'all') {
        $toRun['014'] = migration_014_statements();
    }
    if ($which === '015' || $which === 'all') {
        $toRun['015'] = migration_015_statements();
    }
    if ($which === '016' || $which === 'all') {
        $toRun['016'] = migration_016_statements();
    }
    if ($which === '017' || $which === 'all') {
        $toRun['017'] = migration_017_statements($pdo);
    }
    if ($which === '018' || $which === 'all') {
        $toRun['018'] = migration_018_statements();
    }
    if ($which === '019' || $which === 'all') {
        $toRun['019'] = migration_019_statements();
    }
    if ($which === '020' || $which === 'all') {
        $toRun['020'] = migration_020_statements();
    }
    if ($which === '021' || $which === 'all') {
        $toRun['021'] = migration_021_statements();
    }
    if ($which === '022' || $which === 'all') {
        $toRun['022'] = migration_022_statements();
    }

    foreach ($toRun as $name => $statements) {
        try {
            foreach ($statements as $sql) {
                $pdo->exec($sql);
            }
            $log[] = "Migration $name applied.";
        } catch (PDOException $e) {
            // Already-applied is fine (duplicate column/key) — anything else is a real problem.
            if (in_array((int) $e->errorInfo[1], [1060, 1061, 1050], true)) {
                $log[] = "Migration $name: already up to date.";
            } else {
                $error = "Migration $name failed: " . $e->getMessage();
                error_log('run-migrations: ' . $e->getMessage());
                break;
            }
        }
    }
}

$migration002Done = migration_column_exists($pdo, 'blogs', 'scheduled_at');
$migration003Done = migration_table_exists($pdo, 'booking_availability')
    && migration_table_exists($pdo, 'booking_form_fields')
    && migration_table_exists($pdo, 'bookings')
    && migration_table_exists($pdo, 'booking_notification_emails');
$migration004Done = migration_table_exists($pdo, 'whatsapp_flow_steps')
    && migration_table_exists($pdo, 'whatsapp_leads');
$migration005Done = migration_table_exists($pdo, 'case_studies');
$migration006Done = migration_column_exists($pdo, 'pages', 'focus_keyword')
    && migration_column_exists($pdo, 'blogs', 'focus_keyword')
    && migration_column_exists($pdo, 'case_studies', 'focus_keyword');
$migration007Done = migration_table_exists($pdo, 'case_study_services');
$migration008Done = migration_table_exists($pdo, 'site_popup');
$migration009Done = migration_column_exists($pdo, 'site_popup', 'trigger_delay');
$stmt010 = $pdo->prepare('SELECT COUNT(*) FROM pages WHERE slug IN (?, ?, ?)');
$stmt010->execute(['/custom-erp-solution', '/ecommerce-solutions', '/marketing-solutions']);
$migration010Done = (int) $stmt010->fetchColumn() === 3;
// The 7th page started as /platform-rd (migration 011) and gets renamed
// to /platform-inventory by migration 012 — either slug counts as "the
// 7th page exists" so this check stays accurate before and after 012 runs.
$stmt011a = $pdo->prepare('SELECT COUNT(*) FROM pages WHERE slug IN (?, ?, ?, ?, ?, ?)');
$stmt011a->execute(['/platform-management', '/platform-sales', '/platform-marketing', '/platform-operations', '/platform-finance', '/platform-hr']);
$stmt011b = $pdo->query("SELECT COUNT(*) FROM pages WHERE slug IN ('/platform-rd', '/platform-inventory')");
$migration011Done = (int) $stmt011a->fetchColumn() === 6 && (int) $stmt011b->fetchColumn() >= 1;
$stmt012 = $pdo->prepare('SELECT COUNT(*) FROM pages WHERE slug = ?');
$stmt012->execute(['/platform-inventory']);
$migration012Done = (int) $stmt012->fetchColumn() >= 1;
$stmt013 = $pdo->query("SELECT COUNT(*) FROM pages WHERE slug LIKE '/industry-%'");
$migration013Done = (int) $stmt013->fetchColumn() >= 20;
$migration014Done = migration_table_exists($pdo, 'site_sidebar');
$stmt015 = $pdo->prepare('SELECT COUNT(*) FROM pages WHERE slug = ?');
$stmt015->execute(['/home-2']);
$migration015Done = (int) $stmt015->fetchColumn() >= 1;
$migration016Done = migration_column_exists($pdo, 'pages', 'status')
    && migration_column_exists($pdo, 'pages', 'show_in_menu');
$stmt017 = $pdo->query(
    "SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'site_popup' AND COLUMN_NAME = 'image'"
);
$migration017Done = strtolower((string) $stmt017->fetchColumn()) === 'text';
$migration018Done = migration_table_exists($pdo, 'analyze_reports');
$stmt019 = $pdo->prepare('SELECT COUNT(*) FROM pages WHERE slug = ?');
$stmt019->execute(['/analyze']);
$migration019Done = (int) $stmt019->fetchColumn() >= 1;
$stmt020 = $pdo->prepare('SELECT COUNT(*) FROM pages WHERE slug = ?');
$stmt020->execute(['/ulagai']);
$migration020Done = (int) $stmt020->fetchColumn() >= 1;
$stmt021 = $pdo->prepare('SELECT COUNT(*) FROM pages WHERE slug = ?');
$stmt021->execute(['/crm-solution']);
$migration021Done = (int) $stmt021->fetchColumn() >= 1;
$stmt022 = $pdo->prepare('SELECT COUNT(*) FROM pages WHERE slug = ?');
$stmt022->execute(['/home-5']);
$migration022Done = (int) $stmt022->fetchColumn() >= 1;

$pageTitle = 'Run Migrations';
$pageSub = 'One-time database updates for new features.';
$activeNav = 'migrations';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php foreach ($log as $l): ?><div class="alert alert-success"><?= h($l) ?></div><?php endforeach; ?>

<div class="card">
  <div class="card-title">002 — Blog image alt text &amp; scheduling</div>
  <div class="card-desc">Adds featured_image_alt and scheduled_at columns to the blogs table.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration002Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration002Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration002Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="002">
    <button type="submit" class="btn btn-primary">Run Migration 002</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">003 — Consultation booking system</div>
  <div class="card-desc">Creates booking_availability, booking_notification_emails, booking_form_fields, and bookings tables.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration003Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration003Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration003Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="003">
    <button type="submit" class="btn btn-primary">Run Migration 003</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">004 — WhatsApp lead-capture chat widget</div>
  <div class="card-desc">Creates whatsapp_flow_steps and whatsapp_leads tables, and seeds the first question.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration004Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration004Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration004Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="004">
    <button type="submit" class="btn btn-primary">Run Migration 004</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">005 — Case Studies module</div>
  <div class="card-desc">Creates the case_studies table and extends user_access to support per-user Case Studies module permissions.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration005Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration005Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration005Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="005">
    <button type="submit" class="btn btn-primary">Run Migration 005</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">006 — SEO fields for Pages, Blogs &amp; Case Studies</div>
  <div class="card-desc">Adds focus keyword, canonical URL, robots meta, and Open Graph/Twitter fields to pages, blogs, and case_studies — powers the new SEO panel in each edit form.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration006Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration006Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration006Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="006">
    <button type="submit" class="btn btn-primary">Run Migration 006</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">007 — Manageable Case Study services list</div>
  <div class="card-desc">Creates case_study_services (seeded with the existing 3) so the Departments/Services checklist can grow from the Case Study edit screen instead of being a fixed list.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration007Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration007Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration007Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="007">
    <button type="submit" class="btn btn-primary">Run Migration 007</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">008 — Site-wide consultation popup</div>
  <div class="card-desc">Creates site_popup (disabled by default) — the admin-managed popup shown when a visitor opens the site.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration008Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration008Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration008Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="008">
    <button type="submit" class="btn btn-primary">Run Migration 008</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">009 — Popup trigger controls</div>
  <div class="card-desc">Adds the "when should it appear" checkboxes (3-second delay, every new page, every refresh, 4th section scroll) to the popup settings.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration009Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration009Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration009Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="009">
    <button type="submit" class="btn btn-primary">Run Migration 009</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">010 — Service landing pages</div>
  <div class="card-desc">Adds Custom ERP Solution, Ecommerce Solutions, and Marketing Solutions as real pages (/custom-erp-solution, /ecommerce-solutions, /marketing-solutions), editable from Admin → Pages like Home and About Us.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration010Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration010Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration010Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="010">
    <button type="submit" class="btn btn-primary">Run Migration 010</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">011 — Platform module pages</div>
  <div class="card-desc">Adds the 7 Platform module pages (Management, Sales, Marketing, Operations, Finance, HR, R&amp;D) shown in the new Platform mega menu.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration011Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration011Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration011Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="011">
    <button type="submit" class="btn btn-primary">Run Migration 011</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">012 — Rename R&amp;D module to Inventory Management</div>
  <div class="card-desc">Renames the /platform-rd page to /platform-inventory (in place if it already exists, or creates it fresh otherwise).</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration012Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration012Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration012Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="012">
    <button type="submit" class="btn btn-primary">Run Migration 012</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">013 — Industry pages</div>
  <div class="card-desc">Adds all 20 Industry pages shown in the homepage's Industries section, editable from Admin → Pages like Home and About Us.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration013Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration013Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration013Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="013">
    <button type="submit" class="btn btn-primary">Run Migration 013</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">014 — Admin-manageable sidebar (Text/Image/CTA)</div>
  <div class="card-desc">Creates site_sidebar, so the CTA block in the blog/case study sidebar (below Recent Posts) is editable from Admin → Sidebar instead of hardcoded.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration014Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration014Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration014Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="014">
    <button type="submit" class="btn btn-primary">Run Migration 014</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">015 — Home 2.0 page</div>
  <div class="card-desc">Adds a second homepage variant at /home-2 (same content, different card-based SaaS-style UI), editable from Admin → Pages and linked from the main nav.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration015Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration015Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration015Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="015">
    <button type="submit" class="btn btn-primary">Run Migration 015</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">016 — Draft status &amp; menu visibility for Pages</div>
  <div class="card-desc">Adds a Draft/Published status and a "Show in Menu" toggle to every page in Admin → Pages. Draft pages 404 for visitors and drop out of the sitemap; unchecking "Show in Menu" removes Home, Home 2.0, or About Us from the main nav.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration016Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration016Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration016Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="016">
    <button type="submit" class="btn btn-primary">Run Migration 016</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">017 — Consultation popup: real content &amp; image</div>
  <div class="card-desc">Widens site_popup.image to support a built-in graphic (no upload needed), and replaces the popup's title, description, points, and image with real Drawlead content. The content replacement only ever applies once — safe to re-run later (including via "Run All Pending Migrations" for a future migration) without overwriting anything you edit in Admin → Popup afterward.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration017Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration017Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration017Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="017">
    <button type="submit" class="btn btn-primary">Run Migration 017</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">018 — Drawlead Analyze</div>
  <div class="card-desc">Creates analyze_reports, so the new /analyze tool (URL in, CRO scorecard + rebuilt page out) can save shareable results.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration018Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration018Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration018Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="018">
    <button type="submit" class="btn btn-primary">Run Migration 018</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">019 — Analyze page in Admin → Pages</div>
  <div class="card-desc">Adds Analyze as a real page row (/analyze), so it shows up in Admin → Pages with its own Draft/Published toggle, meta title/description, and Show in Menu checkbox — same as Home, Home 2.0, and About Us.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration019Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration019Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration019Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="019">
    <button type="submit" class="btn btn-primary">Run Migration 019</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">020 — Ulagai page</div>
  <div class="card-desc">Adds Ulagai as a real page (/ulagai) — a dark/violet ecommerce-agency-style landing hero, editable Draft/Published like any other page in Admin → Pages.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration020Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration020Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration020Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="020">
    <button type="submit" class="btn btn-primary">Run Migration 020</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">021 — CRM Solution page</div>
  <div class="card-desc">Adds CRM as a real page (/crm-solution) and "CRM" as a Departments/Service tag, so case studies can be tagged with it and show up on the new page's Case Studies section.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration021Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration021Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration021Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="021">
    <button type="submit" class="btn btn-primary">Run Migration 021</button>
  </form>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">022 — Home 5 page</div>
  <div class="card-desc">Adds Home 5 as a real page (/home-5), rendered by templates/home5-body.php and shown in the main nav next to About Us. Editable Draft/Published, meta title/description, and Show in Menu from Admin → Pages like Home, Home 2.0, and About Us.</div>
  <p style="margin-bottom:1rem"><span class="badge <?= $migration022Done ? 'badge-published' : 'badge-draft' ?>"><?= $migration022Done ? 'Applied' : 'Pending' ?></span></p>
  <?php if (!$migration022Done): ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="022">
    <button type="submit" class="btn btn-primary">Run Migration 022</button>
  </form>
  <?php endif; ?>
</div>

<?php if (!$migration002Done || !$migration003Done || !$migration004Done || !$migration005Done || !$migration006Done || !$migration007Done || !$migration008Done || !$migration009Done || !$migration010Done || !$migration011Done || !$migration012Done || !$migration013Done || !$migration014Done || !$migration015Done || !$migration016Done || !$migration017Done || !$migration018Done || !$migration019Done || !$migration020Done || !$migration021Done || !$migration022Done): ?>
<div class="card">
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="run" value="all">
    <button type="submit" class="btn btn-black">Run All Pending Migrations</button>
  </form>
</div>
<?php else: ?>
<div class="access-note">Everything is up to date. This page is safe to leave here — re-running an applied migration is a no-op.</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
