# Deployment Guide — Staging on admin.drawlead.com → GitHub CI/CD

## The plan

1. Everything (marketing site + admin panel) goes live first at
   **admin.drawlead.com** — a subdomain on your existing Hostinger account
   for drawlead.com. This keeps the live root domain untouched while you
   test.
2. Code lives on **GitHub**. Every `git push` to `main` automatically
   deploys to that subdomain (GitHub Actions → FTP).
3. Once you're happy with everything on admin.drawlead.com, you cut over
   to the root domain (**step 9** below) — no code changes needed, because
   nothing in this codebase hardcodes a domain name; it's all relative
   paths (`/`, `/about-us`, `/blog`, …).

Steps 1–5 are one-time account setup you do in your browser (Hostinger +
GitHub). I can't do these for you — I don't have your login. Everything
after that is automated.

---

## Step 1 — Create the subdomain on Hostinger

1. Log into **hPanel** → **Domains** → find `drawlead.com` → **Subdomains**.
2. Create a new subdomain: `admin` (so it becomes `admin.drawlead.com`).
3. Hostinger auto-creates a folder for it, usually something like
   `/domains/drawlead.com/public_html/admin.drawlead.com` or
   `/public_html/admin/` — **hPanel shows you the exact path when you
   create it. Write that path down — you need it in Step 5.**
4. DNS is automatic since it's the same provider — usually live within a
   few minutes, occasionally up to an hour.

## Step 2 — Create the MySQL database

Same as in `SETUP.md`:
1. hPanel → **Databases** → **MySQL Databases** → create a database + user,
   attach the user with all privileges.
2. Note the database host (usually `localhost`), name, username, password.
3. Open **phpMyAdmin** for that database → **Import** → select
   `sql/schema.sql` from this project → Go.

## Step 3 — Create the GitHub repository

1. Go to github.com (as `drawleados-dotcom`, since that's the account your
   SSH key on this Mac is already authenticated as) → **New repository**.
2. Name it (e.g. `drawlead-website`). Recommend **Private** — this repo
   will contain deploy configuration.
3. Do **not** initialize with a README/.gitignore/license — this project
   already has all of those locally.
4. Create it, then tell me the repo name (or the URL it gives you). I'll
   wire up the remote and push everything that's already committed
   locally.

## Step 4 — Get FTP credentials

hPanel → **Files** → **FTP Accounts**. You can use the main account or
create one scoped to the subdomain's folder. Note down:
- FTP hostname (often `ftp.drawlead.com` or an IP Hostinger shows you)
- Username
- Password

## Step 5 — Add GitHub Actions secrets

In your new GitHub repo → **Settings** → **Secrets and variables** →
**Actions**:

**Secrets** (Repository secrets — click "New repository secret" for each):
| Name | Value |
|---|---|
| `FTP_SERVER` | the FTP hostname from Step 4 |
| `FTP_USERNAME` | the FTP username from Step 4 |
| `FTP_PASSWORD` | the FTP password from Step 4 |

**Variables** (same screen, "Variables" tab) — not needed for this setup,
the workflow already defaults to the right folder (`/drawlead_webapp/`,
relative to the FTP account's own root at `/public_html/`). Only add
`DEPLOY_SERVER_DIR` if that folder ever changes, or `DEPLOY_PROTOCOL` =
`sftp` + `DEPLOY_PORT` = `22` if you later switch to SSH-based deploys.

## Step 6 — Push and watch the first deploy

Once you've told me the repo, I'll push. You can watch it run under the
repo's **Actions** tab. First run uploads the whole project — it'll take
a minute or two.

## Step 7 — Create includes/config.php on the server (one time, manual)

This file holds your real database password and is deliberately **never**
committed to git or touched by the CI/CD pipeline — so it has to be
created directly on the server, once:

1. hPanel → **File Manager**, navigate to the subdomain folder from Step 1.
2. You'll see `includes/config.sample.php` (deployed by the pipeline).
   Duplicate it and rename the copy to `includes/config.php`.
3. Edit it, fill in the real `DB_HOST` / `DB_NAME` / `DB_USER` / `DB_PASS`
   from Step 2, save.

Future deploys will never overwrite or delete this file.

## Step 8 — Verify

1. Visit `https://admin.drawlead.com/` — homepage should load.
2. Visit `https://admin.drawlead.com/admin/` — since no users exist yet,
   it sends you to the signup screen to create your first admin account.
3. Log in, check Pages/Blogs/Users/Analytics all load.

## Step 9 — Later: moving to the root domain (drawlead.com)

When you're ready to go live at the real domain, no code changes are
needed — just:
1. Point `drawlead.com`'s document root at the same deployed folder (or
   repeat Steps 1–7 pointing at drawlead.com's own folder instead of the
   subdomain's), and/or update `DEPLOY_SERVER_DIR` if you want the CI/CD
   pipeline to deploy there instead.
2. Re-run Step 7 for the new location (each server needs its own
   `includes/config.php`).
3. Keep `admin.drawlead.com` around afterward as a staging environment for
   testing future changes before they go live — or retire it, your call.
