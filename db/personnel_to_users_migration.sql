-- Personnel to User Accounts Migration
-- This script migrates all existing personnel to user_accounts table
-- Each personnel will be able to login using their email and default password: dogh2025
-- All accounts will be pre-approved with status 'approved'
-- Profile images will be synced from personnels table

-- --------------------------------------------------------
-- IMPORTANT: Run this migration AFTER the multi_user_migration.sql
-- --------------------------------------------------------

-- Default password: dogh2025
-- BCrypt hash for 'dogh2025' (generated with PASSWORD_BCRYPT)
-- Hash: $2y$10$z3INe/oYz19NS.3FCkhpOe2VYPICgBkwulm7ZRTvGH8UxqMtk8HWW

-- --------------------------------------------------------
-- Insert all personnel into user_accounts
-- Skip personnel that already have user accounts (based on personnel_id)
-- Skip personnel without email addresses
-- Username is made unique by appending personnel_id to avoid duplicates
-- --------------------------------------------------------

INSERT INTO `user_accounts` (
    `personnel_id`,
    `username`,
    `password`,
    `email`,
    `status`,
    `created_at`,
    `approved_at`,
    `approved_by`,
    `admin_notes`,
    `profile_image`
)
SELECT 
    p.id AS personnel_id,
    -- Username: use email prefix + personnel_id to ensure uniqueness
    CONCAT(
        LOWER(
            COALESCE(
                SUBSTRING_INDEX(p.email, '@', 1),
                CONCAT(LOWER(COALESCE(p.firstname, 'user')), '.', LOWER(COALESCE(p.lastname, 'unknown')))
            )
        ),
        '_',
        p.id
    ) AS username,
    -- Default password: dogh2025 (bcrypt hashed)
    '$2y$10$z3INe/oYz19NS.3FCkhpOe2VYPICgBkwulm7ZRTvGH8UxqMtk8HWW' AS password,
    -- Email from personnel record
    p.email AS email,
    -- Status: approved
    'approved' AS status,
    -- Created at: current timestamp
    NOW() AS created_at,
    -- Approved at: current timestamp
    NOW() AS approved_at,
    -- Approved by: system (admin id 2)
    2 AS approved_by,
    -- Admin notes
    'Auto-migrated from personnel records. Default password: dogh2025' AS admin_notes,
    -- Profile image: copy from personnels table
    p.profile_image AS profile_image
FROM `personnels` p
WHERE 
    -- Only personnel with valid email addresses
    p.email IS NOT NULL 
    AND p.email != ''
    AND p.email LIKE '%@%'
    -- Only active personnel
    AND p.status = 1
    -- Skip personnel that already have user accounts
    AND p.id NOT IN (SELECT personnel_id FROM user_accounts WHERE personnel_id IS NOT NULL);

-- --------------------------------------------------------
-- Summary query to verify migration
-- --------------------------------------------------------
-- Run this after migration to verify:
-- SELECT 
--     ua.id, 
--     ua.username, 
--     ua.email, 
--     ua.status,
--     p.firstname, 
--     p.lastname, 
--     p.position,
--     p.profile_image AS personnel_image,
--     ua.profile_image AS user_image
-- FROM user_accounts ua
-- JOIN personnels p ON p.id = ua.personnel_id
-- ORDER BY ua.id;

-- --------------------------------------------------------
-- Count of migrated users
-- --------------------------------------------------------
-- SELECT COUNT(*) AS migrated_users FROM user_accounts WHERE admin_notes LIKE '%Auto-migrated%';

