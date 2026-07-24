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
$stmt011 = $pdo->prepare('SELECT COUNT(*) FROM pages WHERE slug IN (?, ?, ?, ?, ?, ?, ?)');
$stmt011->execute(['/platform-management', '/platform-sales', '/platform-marketing', '/platform-operations', '/platform-finance', '/platform-hr', '/platform-rd']);
$migration011Done = (int) $stmt011->fetchColumn() === 7;

$pageTitle = 'Run Migrations';
$pageSub = 'One-time database updates for new features.';
$activeNav = '';
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

<?php if (!$migration002Done || !$migration003Done || !$migration004Done || !$migration005Done || !$migration006Done || !$migration007Done || !$migration008Done || !$migration009Done || !$migration010Done || !$migration011Done): ?>
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
