-- Migration: Create clock_change_requests and clock_change_request_items tables
-- Feature: Failure to Clock and Time Changes Form

CREATE TABLE IF NOT EXISTS `clock_change_requests` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `control_no` varchar(20) NOT NULL,
    `personnel_id` int(11) NOT NULL,
    `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
    `approver_id` int(11) DEFAULT NULL,
    `approved_at` datetime DEFAULT NULL,
    `approval_remarks` text DEFAULT NULL,
    `approved_by_admin` tinyint(1) DEFAULT 0,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_control_no` (`control_no`),
    KEY `idx_personnel_id` (`personnel_id`),
    KEY `idx_status` (`status`),
    KEY `idx_approver_id` (`approver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `clock_change_request_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `request_id` int(11) NOT NULL,
    `date` date NOT NULL,
    `am_pm` enum('AM','PM') NOT NULL DEFAULT 'AM',
    `time_in` tinyint(1) DEFAULT 0,
    `time_out` tinyint(1) DEFAULT 0,
    `time_change` varchar(100) DEFAULT NULL,
    `reason` text DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_request_id` (`request_id`),
    KEY `idx_date` (`date`),
    CONSTRAINT `fk_ccri_request` FOREIGN KEY (`request_id`) REFERENCES `clock_change_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
