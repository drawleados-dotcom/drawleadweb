-- Migration 030 — Lead Source tracking
-- Run this via admin/run-migrations.php (recommended) or paste into
-- phpMyAdmin if you prefer to apply it manually.
--
-- One row per lead (calendar booking or WhatsApp chat) saying where it
-- came from: the channel (Google Ads, Google Search, Instagram, Direct…),
-- referrer, UTM tags, the landing page, the page the form was sent from and
-- what opened the form — plus the visitor's very first visit (first_*).
-- Admin → Lead Source lists them. Leads saved before this have no row.

CREATE TABLE IF NOT EXISTS lead_sources (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  lead_type           VARCHAR(20)  NOT NULL,                  -- booking | whatsapp
  lead_id             INT          NOT NULL,                  -- bookings.id / whatsapp_leads.id
  channel             VARCHAR(60)  NOT NULL DEFAULT 'Direct',
  source_detail       VARCHAR(190) NOT NULL DEFAULT '',       -- referring site or campaign
  page_url            VARCHAR(500) NOT NULL DEFAULT '',       -- page the form was sent from
  page_title          VARCHAR(255) NOT NULL DEFAULT '',
  trigger_label       VARCHAR(190) NOT NULL DEFAULT '',       -- button / popup that opened the form
  landing_page        VARCHAR(500) NOT NULL DEFAULT '',
  referrer            VARCHAR(500) NOT NULL DEFAULT '',
  utm_source          VARCHAR(190) NOT NULL DEFAULT '',
  utm_medium          VARCHAR(190) NOT NULL DEFAULT '',
  utm_campaign        VARCHAR(190) NOT NULL DEFAULT '',
  utm_term            VARCHAR(190) NOT NULL DEFAULT '',
  utm_content         VARCHAR(190) NOT NULL DEFAULT '',
  click_id            VARCHAR(20)  NOT NULL DEFAULT '',       -- gclid | fbclid | msclkid … (the type only)
  first_channel       VARCHAR(60)  NOT NULL DEFAULT '',
  first_detail        VARCHAR(190) NOT NULL DEFAULT '',
  first_landing_page  VARCHAR(500) NOT NULL DEFAULT '',
  first_referrer      VARCHAR(500) NOT NULL DEFAULT '',
  first_utm_campaign  VARCHAR(190) NOT NULL DEFAULT '',
  first_seen_at       DATETIME     NULL,
  device              VARCHAR(20)  NOT NULL DEFAULT '',
  created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_lead (lead_type, lead_id),
  KEY idx_channel (channel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
