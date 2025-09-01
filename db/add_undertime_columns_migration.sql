-- Migration to add undertime columns to biometrics table
-- Run this SQL to add undertime tracking functionality

ALTER TABLE `biometrics` 
ADD COLUMN `undertime_hours` INT DEFAULT 0 COMMENT 'Total undertime hours for the day',
ADD COLUMN `undertime_minutes` INT DEFAULT 0 COMMENT 'Total undertime minutes for the day';

-- Update existing records to have 0 undertime initially
UPDATE `biometrics` SET `undertime_hours` = 0, `undertime_minutes` = 0 WHERE `undertime_hours` IS NULL OR `undertime_minutes` IS NULL;
