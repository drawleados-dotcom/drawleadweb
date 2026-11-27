# Drawlead website

The Drawlead marketing site and its admin panel (plain PHP + MySQL).
Every push to `main` uploads the site to Hostinger through GitHub Actions.

## Folders

```
drawleadweb/
│
├── .github/         Deploy workflow (push to main → Hostinger over FTP)
│
├── admin/           Admin panel, served at /admin/
├── api/             Form, booking and WhatsApp endpoints, served at /api/
├── assets/          CSS, JavaScript, images and fonts, served at /assets/
├── includes/        Shared PHP: database, SEO, mailer, helpers
│                    (includes/config.php holds the real passwords: server only, never in git)
├── templates/       Page templates and partials (header, footer, sections)
├── uploads/         Images uploaded in the admin (server only, never in git)
├── .htaccess        Sends every page URL to index.php
├── index.php        Front controller: looks up the page for each URL
│
└── _dev/            Everything that is NOT the website. Never uploaded to the server.
    ├── design/      Original static designs and the drawlead-website design source
    ├── docs/        SETUP.md (first install) and DEPLOYMENT.md (Hostinger + GitHub)
    ├── sql/         schema.sql and the numbered migrations
    └── tools/       Build scripts, e.g. perl _dev/tools/sync-home8.pl
```

Everything above `_dev/` goes live on the next push. Anything put inside
`_dev/` stays in GitHub only.
