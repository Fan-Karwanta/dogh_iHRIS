-- Leave Applications Migration
-- Creates tables for CS Form No. 6 - Application for Leave

-- Main leave applications table
CREATE TABLE IF NOT EXISTS `leave_applications` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `personnel_id` INT(11) UNSIGNED NOT NULL COMMENT 'Reference to personnels table',
    `application_number` VARCHAR(50) NULL COMMENT 'Auto-generated application number',
    `date_received` DATE NULL COMMENT 'Date received by HR',
    
    -- Section 1-5: Basic Information (auto-filled from personnel)
    `office_department` ENUM('Medical', 'Nursing', 'Ancillary', 'Administrative') NOT NULL,
    `date_of_filing` DATE NOT NULL,
    `salary_grade` INT(2) NOT NULL COMMENT 'SG 1-33',
    
    -- Section 6.A: Type of Leave
    `leave_type` ENUM(
        'vacation_leave',
        'mandatory_forced_leave',
        'sick_leave',
        'maternity_leave',
        'paternity_leave',
        'special_privilege_leave',
        'solo_parent_leave',
        'study_leave',
        'vawc_leave',
        'rehabilitation_privilege',
        'special_leave_benefits_women',
        'special_emergency_calamity',
        'adoption_leave',
        'others'
    ) NOT NULL,
    `leave_type_others` VARCHAR(255) NULL COMMENT 'If others is selected',
    
    -- Section 6.B: Details of Leave
    -- For Vacation/Special Privilege Leave
    `vacation_special_within_ph` VARCHAR(255) NULL,
    `vacation_special_abroad` VARCHAR(255) NULL,
    
    -- For Sick Leave
    `sick_in_hospital` VARCHAR(255) NULL,
    `sick_out_patient` VARCHAR(255) NULL,
    
    -- For Special Leave Benefits for Women
    `special_women_illness` VARCHAR(255) NULL,
    
    -- For Study Leave
    `study_completion_masters` TINYINT(1) DEFAULT 0,
    `study_bar_review` TINYINT(1) DEFAULT 0,
    
    -- Other Purpose
    `other_purpose_monetization` TINYINT(1) DEFAULT 0,
    `other_purpose_terminal_leave` TINYINT(1) DEFAULT 0,
    
    -- Section 6.C: Number of Working Days
    `working_days_applied` DECIMAL(5,2) NOT NULL COMMENT 'Number of working days',
    `inclusive_date_from` DATE NOT NULL,
    `inclusive_date_to` DATE NOT NULL,
    
    -- Section 6.D: Commutation
    `commutation_requested` TINYINT(1) DEFAULT 0 COMMENT '0=Not Requested, 1=Requested',
    
    -- Applicant signature (stored as timestamp when submitted)
    `applicant_signature_date` DATETIME NULL,
    
    -- Section 7.A: Certification of Leave Credits (filled by HR)
    `certification_as_of` DATE NULL,
    `vacation_leave_total_earned` DECIMAL(10,3) NULL,
    `vacation_leave_less_application` DECIMAL(10,3) NULL,
    `vacation_leave_balance` DECIMAL(10,3) NULL,
    `sick_leave_total_earned` DECIMAL(10,3) NULL,
    `sick_leave_less_application` DECIMAL(10,3) NULL,
    `sick_leave_balance` DECIMAL(10,3) NULL,
    `certified_by_id` INT(11) UNSIGNED NULL COMMENT 'HR Officer who certified',
    `certified_date` DATETIME NULL,
    
    -- Section 7.B: Recommendation
    `recommendation` ENUM('for_approval', 'for_disapproval') NULL,
    `recommendation_disapproval_reason` TEXT NULL,
    `recommended_by_id` INT(11) UNSIGNED NULL COMMENT 'Authorized Official',
    `recommended_date` DATETIME NULL,
    
    -- Section 7.C: Approved For
    `approved_days_with_pay` DECIMAL(5,2) NULL,
    `approved_days_without_pay` DECIMAL(5,2) NULL,
    `approved_others` VARCHAR(255) NULL,
    
    -- Section 7.D: Disapproved Due To
    `disapproval_reason` TEXT NULL,
    
    -- Final approval
    `approved_by_id` INT(11) UNSIGNED NULL COMMENT 'Medical Center Chief',
    `approved_date` DATETIME NULL,
    
    -- Status tracking
    `status` ENUM('draft', 'pending', 'certified', 'recommended', 'approved', 'disapproved', 'cancelled') DEFAULT 'draft',
    
    -- Timestamps
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_personnel_id` (`personnel_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_date_of_filing` (`date_of_filing`),
    INDEX `idx_leave_type` (`leave_type`),
    INDEX `idx_application_number` (`application_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Leave credits tracking table
-- VL and SL: +1.25/month, cumulative (not reset yearly)
-- SPL: 3 days/year, reset yearly
-- Mandatory/Forced Leave: Uses VL balance (max 5 days)
CREATE TABLE IF NOT EXISTS `leave_credits` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `personnel_id` INT(11) UNSIGNED NOT NULL,
    `leave_type` ENUM('vacation', 'sick', 'special_privilege', 'solo_parent', 'vawc', 'maternity', 'paternity') NOT NULL,
    `year` YEAR NOT NULL,
    `earned` DECIMAL(10,3) DEFAULT 0 COMMENT 'Total earned credits',
    `used` DECIMAL(10,3) DEFAULT 0 COMMENT 'Total used credits',
    `balance` DECIMAL(10,3) DEFAULT 0 COMMENT 'Current balance (earned - used)',
    `carried_over` DECIMAL(10,3) DEFAULT 0 COMMENT 'Credits carried over from previous year (VL/SL only)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_personnel_leave_year` (`personnel_id`, `leave_type`, `year`),
    INDEX `idx_personnel_id` (`personnel_id`),
    INDEX `idx_year` (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add carried_over column if table already exists
ALTER TABLE `leave_credits` ADD COLUMN IF NOT EXISTS `carried_over` DECIMAL(10,3) DEFAULT 0 COMMENT 'Credits carried over from previous year (VL/SL only)' AFTER `balance`;

-- Leave application audit trail
CREATE TABLE IF NOT EXISTS `leave_application_logs` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `leave_application_id` INT(11) UNSIGNED NOT NULL,
    `action` VARCHAR(100) NOT NULL COMMENT 'submitted, certified, recommended, approved, disapproved, cancelled',
    `action_by_id` INT(11) UNSIGNED NOT NULL,
    `action_date` DATETIME NOT NULL,
    `remarks` TEXT NULL,
    `old_status` VARCHAR(50) NULL,
    `new_status` VARCHAR(50) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_leave_application_id` (`leave_application_id`),
    INDEX `idx_action_by_id` (`action_by_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default leave credits for existing personnel (15 VL, 15 SL per year)
-- This is optional and can be run separately
-- INSERT INTO leave_credits (personnel_id, leave_type, year, earned, used, balance)
-- SELECT id, 'vacation', YEAR(CURDATE()), 15, 0, 15 FROM personnels WHERE status = 1;
-- INSERT INTO leave_credits (personnel_id, leave_type, year, earned, used, balance)
-- SELECT id, 'sick', YEAR(CURDATE()), 15, 0, 15 FROM personnels WHERE status = 1;
