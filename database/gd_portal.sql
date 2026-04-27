CREATE DATABASE IF NOT EXISTS `bdGdPortal` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bdGdPortal`;

DROP TABLE IF EXISTS `poi_records`;
DROP TABLE IF EXISTS `gd_status_logs`;
DROP TABLE IF EXISTS `general_diaries`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(160) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('citizen','police','admin') NOT NULL DEFAULT 'citizen',
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `general_diaries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `assigned_officer_id` INT UNSIGNED DEFAULT NULL,
  `reference_no` VARCHAR(40) NOT NULL,
  `verification_token` VARCHAR(64) NOT NULL,
  `gd_type` ENUM('lost_document','theft','missing_person','cyber_complaint','threat','other') NOT NULL,
  `subject` VARCHAR(180) NOT NULL,
  `description` TEXT NOT NULL,
  `incident_date` DATE NOT NULL,
  `location` VARCHAR(220) NOT NULL,
  `evidence_path` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('submitted','under_review','investigating','resolved','rejected') NOT NULL DEFAULT 'submitted',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gd_reference_unique` (`reference_no`),
  UNIQUE KEY `gd_token_unique` (`verification_token`),
  KEY `gd_user_fk` (`user_id`),
  KEY `gd_officer_fk` (`assigned_officer_id`),
  CONSTRAINT `gd_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gd_officer_fk` FOREIGN KEY (`assigned_officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gd_status_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `gd_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `status` ENUM('submitted','under_review','investigating','resolved','rejected') NOT NULL,
  `note` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `log_gd_fk` (`gd_id`),
  KEY `log_user_fk` (`user_id`),
  CONSTRAINT `log_gd_fk` FOREIGN KEY (`gd_id`) REFERENCES `general_diaries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `log_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `poi_records` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `gd_id` INT UNSIGNED DEFAULT NULL,
  `name` VARCHAR(140) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `poi_gd_fk` (`gd_id`),
  KEY `poi_user_fk` (`created_by`),
  CONSTRAINT `poi_gd_fk` FOREIGN KEY (`gd_id`) REFERENCES `general_diaries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `poi_user_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`name`, `email`, `phone`, `address`, `password_hash`, `role`) VALUES
('System Admin', 'admin@gd.test', '01700000001', 'Dhaka', '$2y$10$3jHjzMsB0ZmpDrKEhTSWEOtyG5hdSBCEWWa2pErS34dMm71yuHBku', 'admin'),
('Duty Officer', 'police@gd.test', '01700000002', 'Model Police Station', '$2y$10$3jHjzMsB0ZmpDrKEhTSWEOtyG5hdSBCEWWa2pErS34dMm71yuHBku', 'police'),
('Demo Citizen', 'citizen@gd.test', '01700000003', 'Dhaka, Bangladesh', '$2y$10$3jHjzMsB0ZmpDrKEhTSWEOtyG5hdSBCEWWa2pErS34dMm71yuHBku', 'citizen');

INSERT INTO `general_diaries` (`user_id`, `reference_no`, `verification_token`, `gd_type`, `subject`, `description`, `incident_date`, `location`, `status`) VALUES
(3, 'GD-20260426-DEMO01', 'demo-verification-token-001', 'lost_document', 'Lost National ID photocopy', 'A document copy was misplaced while travelling near Shahbag.', '2026-04-25', 'Shahbag, Dhaka', 'under_review');

INSERT INTO `gd_status_logs` (`gd_id`, `user_id`, `status`, `note`) VALUES
(1, 3, 'submitted', 'GD submitted by citizen.'),
(1, 2, 'under_review', 'Duty officer started initial review.');
