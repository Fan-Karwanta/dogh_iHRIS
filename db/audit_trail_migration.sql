-- Audit Trail Migration for DTR System
-- This creates the audit_trail table to track all DTR record modifications

CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `action_type` enum('CREATE','UPDATE','DELETE') NOT NULL,
  `field_name` varchar(50) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `personnel_email` varchar(100) DEFAULT NULL,
  `personnel_name` varchar(150) DEFAULT NULL,
  `admin_user_id` int(11) UNSIGNED NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `reason` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_table_record` (`table_name`, `record_id`),
  KEY `idx_personnel_email` (`personnel_email`),
  KEY `idx_admin_user_id` (`admin_user_id`),
  KEY `idx_action_type` (`action_type`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`admin_user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create index for faster queries on personnel audit history
CREATE INDEX `idx_personnel_audit` ON `audit_trail` (`personnel_email`, `created_at` DESC);

-- Create index for admin activity tracking
CREATE INDEX `idx_admin_activity` ON `audit_trail` (`admin_user_id`, `created_at` DESC);
