-- DTR Edit Requests Table Migration
-- This table stores DTR edit requests from personnel for approval

CREATE TABLE IF NOT EXISTS `dtr_edit_requests` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `personnel_id` int(11) NOT NULL,
    `bio_id` varchar(50) DEFAULT NULL,
    `request_date` date NOT NULL COMMENT 'The date of the DTR entry being edited',
    `request_month` varchar(7) NOT NULL COMMENT 'Format: YYYY-MM',
    
    -- Original values (from biometrics/imported data)
    `original_am_in` time DEFAULT NULL,
    `original_am_out` time DEFAULT NULL,
    `original_pm_in` time DEFAULT NULL,
    `original_pm_out` time DEFAULT NULL,
    
    -- Requested new values
    `requested_am_in` time DEFAULT NULL,
    `requested_am_out` time DEFAULT NULL,
    `requested_pm_in` time DEFAULT NULL,
    `requested_pm_out` time DEFAULT NULL,
    
    -- Edit type: 'repositioned' (drag-drop existing) or 'manual' (new entry)
    `edit_type` enum('repositioned','manual') NOT NULL DEFAULT 'manual',
    
    -- Which fields were modified
    `modified_fields` text COMMENT 'JSON array of modified field names',
    
    -- Request details
    `reason` text COMMENT 'Reason for the edit request',
    `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
    
    -- Approval workflow
    `approver_id` int(11) DEFAULT NULL COMMENT 'Personnel ID of the approver',
    `approved_by_admin` tinyint(1) DEFAULT 0 COMMENT 'If approved by admin directly',
    `approval_remarks` text,
    `approved_at` datetime DEFAULT NULL,
    
    -- Timestamps
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `idx_personnel_id` (`personnel_id`),
    KEY `idx_bio_id` (`bio_id`),
    KEY `idx_request_date` (`request_date`),
    KEY `idx_request_month` (`request_month`),
    KEY `idx_status` (`status`),
    KEY `idx_approver_id` (`approver_id`),
    KEY `idx_edit_type` (`edit_type`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DTR Edit Request Logs for audit trail
CREATE TABLE IF NOT EXISTS `dtr_edit_request_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `request_id` int(11) NOT NULL,
    `action` varchar(50) NOT NULL COMMENT 'created, updated, approved, rejected, cancelled',
    `action_by` int(11) DEFAULT NULL COMMENT 'Personnel ID who performed the action',
    `action_by_admin` int(11) DEFAULT NULL COMMENT 'Admin user ID if action by admin',
    `old_values` text COMMENT 'JSON of old values',
    `new_values` text COMMENT 'JSON of new values',
    `remarks` text,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `idx_request_id` (`request_id`),
    KEY `idx_action` (`action`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
