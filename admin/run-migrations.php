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

<?php if (!$migration002Done || !$migration003Done): ?>
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
