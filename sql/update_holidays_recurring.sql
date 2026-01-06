-- Update existing holidays to use year 2000 as base year for recurring holidays
-- This ensures they work for any year based on month-day matching

-- First, update all fixed recurring holidays to use year 2000
UPDATE `holidays` SET `date` = CONCAT('2000-', DATE_FORMAT(`date`, '%m-%d')) 
WHERE `recurring` = 1 AND `holiday_type` = 'fixed';

-- If the table is empty or you want to reset, you can truncate and re-insert:
-- TRUNCATE TABLE `holiday_departments`;
-- TRUNCATE TABLE `holidays`;

-- Then run the insert statements from holidays_table.sql
