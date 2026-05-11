USE `bdGdPortal`;

ALTER TABLE `users`
  ADD COLUMN `nid_number` VARCHAR(17) DEFAULT NULL AFTER `phone`,
  ADD COLUMN `nid_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `nid_number`,
  ADD COLUMN `nid_verified_at` TIMESTAMP NULL DEFAULT NULL AFTER `nid_verified`,
  ADD UNIQUE KEY `users_nid_unique` (`nid_number`);
