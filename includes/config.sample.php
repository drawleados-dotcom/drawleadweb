<?php
/**
 * Drawlead CMS — local/server config template.
 *
 * Copy this file to "config.php" (same folder) and fill in your real
 * values. "config.php" is git-ignored on purpose — it holds real database
 * credentials and must never be committed or pushed to GitHub. Every
 * server (staging on admin.drawlead.com, and later production on
 * drawlead.com) gets its own config.php created directly on that server,
 * not deployed through git.
 */

// From Hostinger hPanel → Databases → MySQL Databases
define('DB_HOST', 'localhost');
define('DB_NAME', 'CHANGE_ME_db_name');
define('DB_USER', 'CHANGE_ME_db_user');
define('DB_PASS', 'CHANGE_ME_db_password');

// From Hostinger hPanel → Emails — create a mailbox (e.g. bookings@drawlead.com)
// and use its SMTP settings here. Used to send booking confirmations and
// admin notifications for the consultation booking system.
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_PORT', 465);          // 465 = implicit TLS, 587 = STARTTLS
define('SMTP_SECURE', 'ssl');      // 'ssl' for port 465, 'tls' for port 587
define('SMTP_USER', 'CHANGE_ME_bookings@drawlead.com');
define('SMTP_PASS', 'CHANGE_ME_mailbox_password');
define('SMTP_FROM_EMAIL', 'CHANGE_ME_bookings@drawlead.com');
define('SMTP_FROM_NAME', 'Drawlead');
