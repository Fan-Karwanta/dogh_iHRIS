-- Multi-User System Migration
-- Adds support for regular users (employees) separate from admin users
-- Run this migration in phpMyAdmin

-- --------------------------------------------------------
-- 1. Add 'user' group for regular employees
-- --------------------------------------------------------
INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(3, 'user', 'Regular Employee User');

-- --------------------------------------------------------
-- 2. Create user_accounts table for employee user accounts
-- This links to personnels table and stores login credentials
-- --------------------------------------------------------
CREATE TABLE `user_accounts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `personnel_id` int(11) NOT NULL COMMENT 'Links to personnels table',
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(254) NOT NULL,
  `status` ENUM('pending', 'approved', 'disapproved', 'blocked') NOT NULL DEFAULT 'pending' COMMENT 'Account approval status',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approved_at` DATETIME NULL COMMENT 'When account was approved',
  `approved_by` int(11) UNSIGNED NULL COMMENT 'Admin who approved the account',
  `last_login` DATETIME NULL,
  `remember_token` varchar(255) NULL,
  `profile_image` varchar(255) NULL COMMENT 'Profile image filename',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_username` (`username`),
  UNIQUE KEY `uc_email` (`email`),
  UNIQUE KEY `uc_personnel_id` (`personnel_id`),
  KEY `idx_status` (`status`),
  KEY `idx_personnel_id` (`personnel_id`),
  CONSTRAINT `fk_user_accounts_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Employee user accounts for DTR portal';

-- --------------------------------------------------------
-- 3. Create user_login_attempts table for security
-- --------------------------------------------------------
CREATE TABLE `user_login_attempts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Login attempts for employee users';

-- --------------------------------------------------------
-- 4. Create user_sessions table for session management
-- --------------------------------------------------------
CREATE TABLE `user_sessions` (
  `id` varchar(128) NOT NULL,
  `user_account_id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `last_activity` int(11) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_account_id` (`user_account_id`),
  KEY `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='User session tracking';

-- --------------------------------------------------------
-- 5. Add admin notes column to user_accounts for approval/disapproval reasons
-- --------------------------------------------------------
ALTER TABLE `user_accounts` 
ADD COLUMN `admin_notes` TEXT NULL COMMENT 'Notes from admin regarding approval/disapproval' AFTER `approved_by`;

-- --------------------------------------------------------
-- 6. Create notifications table for user notifications
-- --------------------------------------------------------
CREATE TABLE `user_notifications` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_account_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` ENUM('info', 'warning', 'success', 'danger') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `read_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_account_id` (`user_account_id`),
  KEY `idx_is_read` (`is_read`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_account_id`) REFERENCES `user_accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='User notifications';

-- --------------------------------------------------------
-- 7. Add department_id to personnels if not exists (for better organization)
-- --------------------------------------------------------
-- Check if column exists before adding
SET @dbname = DATABASE();
SET @tablename = 'personnels';
SET @columnname = 'department_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE personnels ADD COLUMN department_id INT(11) NULL AFTER role'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- --------------------------------------------------------
-- Summary of changes:
-- 1. Added 'user' group (id=3) for regular employees
-- 2. Created user_accounts table for employee login credentials
-- 3. Created user_login_attempts table for security
-- 4. Created user_sessions table for session management
-- 5. Created user_notifications table for notifications
-- --------------------------------------------------------
