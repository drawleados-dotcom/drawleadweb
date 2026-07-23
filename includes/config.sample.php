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
