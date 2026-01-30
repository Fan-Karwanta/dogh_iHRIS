-- Migration for Main Department Biometrics Raw Logs
-- This table stores raw biometric punch data from Main department's hardware
-- Different from the existing biometrics table which stores processed attendance records

-- Create table for raw biometric logs from Main department
CREATE TABLE IF NOT EXISTS `main_biometrics_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `staff_code` INT(11) NOT NULL COMMENT 'Last 4 digits of Staff Code from CSV, leading zeros removed',
    `personnel_id` INT(11) DEFAULT NULL COMMENT 'Linked personnel ID if matched',
    `department` VARCHAR(100) DEFAULT NULL COMMENT 'Department from CSV',
    `week_day` VARCHAR(20) DEFAULT NULL COMMENT 'Day of week from CSV',
    `log_date` DATE NOT NULL COMMENT 'Date of the punch',
    `log_time` TIME NOT NULL COMMENT 'Time of the punch',
    `remark` ENUM('IN', 'OUT') NOT NULL COMMENT 'IN or OUT punch',
    `import_batch` VARCHAR(50) DEFAULT NULL COMMENT 'Batch identifier for tracking imports',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_staff_code` (`staff_code`),
    INDEX `idx_log_date` (`log_date`),
    INDEX `idx_personnel_id` (`personnel_id`),
    INDEX `idx_remark` (`remark`),
    INDEX `idx_import_batch` (`import_batch`),
    INDEX `idx_staff_date` (`staff_code`, `log_date`),
    UNIQUE KEY `unique_punch` (`staff_code`, `log_date`, `log_time`, `remark`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Raw biometric logs from Main department hardware';

-- Create table for tracking import history
CREATE TABLE IF NOT EXISTS `main_biometrics_imports` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `batch_id` VARCHAR(50) NOT NULL COMMENT 'Unique batch identifier',
    `filename` VARCHAR(255) NOT NULL COMMENT 'Original filename',
    `total_records` INT(11) DEFAULT 0 COMMENT 'Total records in CSV',
    `imported_records` INT(11) DEFAULT 0 COMMENT 'Successfully imported records',
    `matched_personnel` INT(11) DEFAULT 0 COMMENT 'Records matched to personnel',
    `unmatched_personnel` INT(11) DEFAULT 0 COMMENT 'Records not matched to personnel',
    `duplicate_skipped` INT(11) DEFAULT 0 COMMENT 'Duplicate records skipped',
    `imported_by` INT(11) DEFAULT NULL COMMENT 'User ID who imported',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_batch_id` (`batch_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Import history for Main department biometrics';

-- Add index to personnels table for faster bio_id lookups if not exists
-- This helps with matching staff_code to personnel
ALTER TABLE `personnels` ADD INDEX IF NOT EXISTS `idx_bio_id` (`bio_id`);
