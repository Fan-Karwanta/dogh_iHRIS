-- Migration to add device_code column to biometrics table
-- Run this SQL script to update the database schema

ALTER TABLE `biometrics` ADD COLUMN `device_code` VARCHAR(10) DEFAULT NULL AFTER `bio_id`;

-- Update the existing records to have a default device code if needed
-- UPDATE `biometrics` SET `device_code` = '000' WHERE `device_code` IS NULL;
