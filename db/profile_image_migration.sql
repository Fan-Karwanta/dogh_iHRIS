-- Profile Image Migration
-- Adds profile_image field to personnels table for storing profile pictures

-- Add profile_image column to personnels table
ALTER TABLE `personnels` 
ADD COLUMN `profile_image` VARCHAR(255) NULL COMMENT 'Profile image filename' AFTER `bio_id`;

-- Add index for better performance
ALTER TABLE `personnels` 
ADD INDEX `idx_profile_image` (`profile_image`);
