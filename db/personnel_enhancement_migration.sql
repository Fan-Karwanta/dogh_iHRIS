-- Personnel Enhancement Migration
-- Adds new fields to support comprehensive personnel data from CSV

-- Add new columns to personnels table
ALTER TABLE `personnels` 
ADD COLUMN `timestamp` DATETIME NULL COMMENT 'Registration timestamp from CSV',
ADD COLUMN `employment_type` ENUM('Regular', 'Contract of Service', 'COS / JO') NOT NULL DEFAULT 'Regular' COMMENT 'Type of employment',
ADD COLUMN `salary_grade` INT(11) NULL COMMENT 'Salary grade level',
ADD COLUMN `schedule_type` VARCHAR(100) DEFAULT '8:00 AM - 5:00 PM' COMMENT 'Work schedule type',
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record update timestamp';

-- Update existing records to have default values
UPDATE `personnels` SET 
    `employment_type` = 'Regular',
    `schedule_type` = '8:00 AM - 5:00 PM'
WHERE `employment_type` IS NULL OR `schedule_type` IS NULL;

-- Add indexes for better performance
ALTER TABLE `personnels` 
ADD INDEX `idx_employment_type` (`employment_type`),
ADD INDEX `idx_salary_grade` (`salary_grade`),
ADD INDEX `idx_bio_id` (`bio_id`),
ADD INDEX `idx_email` (`email`);

-- Update the role column to be more flexible
ALTER TABLE `personnels` 
MODIFY COLUMN `role` VARCHAR(100) DEFAULT NULL COMMENT 'Personnel role/department';
