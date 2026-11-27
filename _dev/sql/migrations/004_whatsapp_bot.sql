-- Migration 004: WhatsApp-style lead-capture chat widget
-- Run once in phpMyAdmin → your database → SQL tab, or via
-- admin/run-migrations.php inside the app. Safe on an existing database.

CREATE TABLE IF NOT EXISTS whatsapp_flow_steps (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  step_order   INT NOT NULL DEFAULT 0,
  message      TEXT NOT NULL,
  step_type    ENUM('choice','text') NOT NULL DEFAULT 'choice',
  options      TEXT NULL,           -- JSON array of choice labels, for step_type='choice'
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- WHERE NOT EXISTS guard: safe to run this file more than once without
-- creating duplicate seed rows (there's no unique constraint on message).
INSERT INTO whatsapp_flow_steps (step_order, message, step_type, options)
SELECT 1, 'Hi! Welcome to Drawlead — your digital solutions partner. What problem do you need solved?', 'choice',
       '["Custom ERP Solution / Software","Ecommerce Solutions","Marketing Solutions"]'
WHERE NOT EXISTS (SELECT 1 FROM whatsapp_flow_steps);

CREATE TABLE IF NOT EXISTS whatsapp_leads (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  answers     LONGTEXT NOT NULL,    -- JSON: [{"question":"...","answer":"..."}, ...]
  phone       VARCHAR(40) NOT NULL,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
