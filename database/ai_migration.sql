-- Migration: Enable AI feature flag and create AI request logs
-- Run this on your MySQL instance: mysql -u<user> -p <database_name> < ai_migration.sql

SET FOREIGN_KEY_CHECKS = 0;

-- Add ai_enabled flag to users
ALTER TABLE `users`
  ADD COLUMN `ai_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `can_write_prescription`;

-- Create table to log AI requests and responses
CREATE TABLE IF NOT EXISTS `ai_requests` (
  `id` VARCHAR(36) NOT NULL,
  `user_id` VARCHAR(36) NOT NULL,
  `prompt` TEXT NOT NULL,
  `response` LONGTEXT NULL,
  `model` VARCHAR(100) NULL,
  `request_metadata` JSON NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
