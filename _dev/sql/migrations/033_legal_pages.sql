-- Migration 033 — Privacy Policy, Refund Policy and Terms & Conditions pages
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- Adds three published pages, rendered by templates/privacy-policy-body.php,
-- refund-policy-body.php and terms-and-conditions-body.php (shared layout in
-- templates/partials/legal-top.php and legal-bottom.php, styles in
-- /assets/legal.css). The slugs are the old WordPress site's, so its links
-- and search results land on the new pages. Not in the menu: every footer
-- links them.

INSERT IGNORE INTO pages (name, slug, meta_title, meta_description, template) VALUES
('Privacy Policy', '/privacy-policy',
  'Privacy Policy | Drawlead',
  'How Drawlead collects, uses and protects personal data from drawlead.com: consultation bookings, chat enquiries, analytics, cookies and your rights.',
  'privacy-policy'),
('Refund Policy', '/refund-policy',
  'Refund and Cancellation Policy | Drawlead',
  'How cancellations and refunds work for Drawlead projects, retainers and subscriptions, what is not refundable, and how to request a refund.',
  'refund-policy'),
('Terms & Conditions', '/terms-and-conditions',
  'Terms and Conditions | Drawlead',
  'The terms for using drawlead.com and its free tools, including acceptable use, our content, liability and governing law.',
  'terms-and-conditions');
