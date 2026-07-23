# Drawlead CMS — Setup Guide (Hostinger / cPanel)

This turns the site into a small WordPress-style system: a login-protected
admin panel, editable page meta tags, a blog, user accounts with per-page
access, and Google Analytics / Search Console connection — running on plain
PHP + MySQL, which is what shared hosting (Hostinger, GoDaddy, cPanel, etc.)
provides out of the box.

## 1. Requirements

- PHP 7.4+ (PHP 8.1+ recommended) with the `pdo_mysql` extension — on by default on Hostinger.
- One MySQL database.

## 2. Create the database

In hPanel / cPanel → **MySQL Databases**:

1. Create a new database (e.g. `u123456_drawlead`).
2. Create a new database user with a strong password.
3. Attach the user to the database with **All Privileges**.
4. Note down: database host (usually `localhost`), database name, username, password.

## 3. Import the schema

Open **phpMyAdmin** for that database → **Import** → choose `sql/schema.sql`
from this project → Go. This creates all tables and seeds the Home and
About Us page rows.

## 4. Configure the app

Open `includes/bootstrap.php` and fill in the four `define()` lines near the top:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u123456_drawlead');
define('DB_USER', 'u123456_dbuser');
define('DB_PASS', 'your-database-password');
```

## 5. Upload

Upload the **entire contents** of this folder to your domain's web root
(usually `public_html`), keeping the folder structure intact:

```
public_html/
  .htaccess
  index.php
  includes/
  templates/
  admin/
  uploads/
  sql/            (safe to delete after import, or keep as a backup)
```

Make sure `uploads/` is writable (permissions `755` is normally enough on
Hostinger — you generally don't need `777`).

The original static files (`Drawlead New Website .html`, `about-us.html`)
can stay in the folder untouched as a backup, or be deleted once you've
confirmed the new site works — they're no longer linked from anywhere.

## 6. Create your first admin account

Visit `https://yourdomain.com/admin/` (or `/admin/login.php`). Since there
are no users yet, you'll be sent to the signup screen automatically —
this only ever happens once. Create your name, email, and password there.

After that first account exists, the signup screen locks itself — all
further users must be created by an admin from **Users** inside the panel.

## 7. Using the admin panel

- **Pages** — click a page name to edit its Name, URL, Meta Title, and
  Meta Description. Saving updates the live site immediately.
- **Blogs** — write posts with a built-in rich text editor, upload a
  featured image, set SEO fields, and mark Draft/Published.
- **Users** — create additional admin/editor accounts. Editors only see
  the specific pages and/or the Blogs module you grant them access to
  (open a user → **Manage Access**).
- **Analytics & Search Console** — paste your GA4 Measurement ID
  (`G-XXXXXXXXXX`) and the `<meta name="google-site-verification" ...>`
  tag Search Console gives you. Both apply site-wide automatically.

## 8. Security notes

- Passwords are hashed with bcrypt (`password_hash`), never stored in plain text.
- All admin forms are CSRF-protected.
- The `uploads/` folder has a `.htaccess` that blocks PHP execution, even
  if a bad file somehow got uploaded there.
- Blog post HTML is passed through an allowlist sanitizer on save
  (`sanitize_blog_html()` in `includes/bootstrap.php`) — only trusted,
  logged-in users with Blogs access can write this content, but it's
  stripped of scripts and event handlers regardless.
- Keep `display_errors` off in production (already set in
  `includes/bootstrap.php`) so database errors never leak to visitors.

## 9. If something doesn't route correctly

The whole public site runs through one front controller (`index.php`)
using an Apache rewrite rule in `.htaccess`. If pages 404 unexpectedly,
confirm `mod_rewrite` is enabled for your hosting account (it is by
default on Hostinger) and that `.htaccess` uploaded correctly (some FTP
clients hide dot-files — make sure "show hidden files" is on).
