-- Performance Indexes for DTR System
-- Run this in phpMyAdmin to significantly speed up dashboard queries
-- These indexes optimize the most common queries used in the dashboard

-- --------------------------------------------------------
-- Indexes for 'attendance' table
-- --------------------------------------------------------
ALTER TABLE `attendance` 
ADD INDEX `idx_attendance_date` (`date`),
ADD INDEX `idx_attendance_email` (`email`),
ADD INDEX `idx_attendance_date_email` (`date`, `email`);

-- --------------------------------------------------------
-- Indexes for 'biometrics' table
-- --------------------------------------------------------
ALTER TABLE `biometrics` 
ADD INDEX `idx_biometrics_date` (`date`),
ADD INDEX `idx_biometrics_bio_id` (`bio_id`),
ADD INDEX `idx_biometrics_date_bio_id` (`date`, `bio_id`);

-- --------------------------------------------------------
-- Indexes for 'personnels' table
-- --------------------------------------------------------
ALTER TABLE `personnels` 
ADD INDEX `idx_personnels_status` (`status`),
ADD INDEX `idx_personnels_email` (`email`),
ADD INDEX `idx_personnels_bio_id` (`bio_id`);

-- --------------------------------------------------------
-- Indexes for 'audit_trail' table
-- --------------------------------------------------------
ALTER TABLE `audit_trail` 
ADD INDEX `idx_audit_table_action` (`table_name`, `action_type`),
ADD INDEX `idx_audit_created_at` (`created_at`);

-- --------------------------------------------------------
-- Indexes for 'holidays' table
-- --------------------------------------------------------
ALTER TABLE `holidays` 
ADD INDEX `idx_holidays_date` (`date`);

-- --------------------------------------------------------
-- Note: If you get "Duplicate key name" errors, it means
-- the index already exists. You can safely ignore those errors.
-- --------------------------------------------------------
