-- Migration 003: self-hosted consultation booking system
-- Run once in phpMyAdmin → your database → SQL tab. Safe on an existing
-- database — only adds new tables, does not touch pages/blogs/users/etc.

CREATE TABLE IF NOT EXISTS booking_availability (
  id                     INT PRIMARY KEY DEFAULT 1,
  days_of_week           VARCHAR(20) NOT NULL DEFAULT '1,2,3,4,5', -- 0=Sun..6=Sat
  start_time             TIME NOT NULL DEFAULT '10:00:00',
  end_time               TIME NOT NULL DEFAULT '18:00:00',
  slot_interval_minutes  INT NOT NULL DEFAULT 30,
  range_start            DATE NULL,
  range_end              DATE NULL,
  updated_at             DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO booking_availability (id, range_start, range_end)
VALUES (1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY));

CREATE TABLE IF NOT EXISTS booking_notification_emails (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  email  VARCHAR(190) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS booking_form_fields (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  field_key             VARCHAR(100) NOT NULL UNIQUE,
  label                 VARCHAR(190) NOT NULL,
  field_type            ENUM('text','email','phone','textarea','select','radio','checkbox','date') NOT NULL DEFAULT 'text',
  field_role            ENUM('none','name','email') NOT NULL DEFAULT 'none', -- tells the mailer which submitted value is the booker's name/email
  options               TEXT NULL,          -- JSON array of choices, for select/radio/checkbox
  placeholder           VARCHAR(190) NOT NULL DEFAULT '',
  is_required            TINYINT(1) NOT NULL DEFAULT 1,
  sort_order            INT NOT NULL DEFAULT 0,
  conditional_field_id  INT NULL,           -- show this field only if that field...
  conditional_value     VARCHAR(190) NULL,  -- ...equals this value
  created_at            DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conditional_field_id) REFERENCES booking_form_fields(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO booking_form_fields (field_key, label, field_type, field_role, placeholder, is_required, sort_order) VALUES
('name', 'Full Name', 'text', 'name', 'Your name', 1, 1),
('email', 'Email Address', 'email', 'email', 'you@company.com', 1, 2),
('phone', 'Phone Number', 'phone', 'none', '+91 98765 43210', 1, 3),
('company', 'Company Name', 'text', 'none', 'Your business name', 0, 4);

CREATE TABLE IF NOT EXISTS bookings (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  booking_date  DATE NOT NULL,
  booking_time  TIME NOT NULL,
  form_data     LONGTEXT NOT NULL,          -- JSON: {field_key: value, ...}
  name          VARCHAR(190) NOT NULL DEFAULT '',
  email         VARCHAR(190) NOT NULL DEFAULT '',
  status        ENUM('confirmed','cancelled') NOT NULL DEFAULT 'confirmed',
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_slot (booking_date, booking_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
