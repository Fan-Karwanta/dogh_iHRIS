-- Holidays Table for DTR System
-- This table stores holidays/events that are considered no duty days
-- Supports department-specific filtering

CREATE TABLE IF NOT EXISTS `holidays` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Holiday name (e.g., New Year''s Day)',
    `date` DATE NOT NULL COMMENT 'Specific date of the holiday',
    `holiday_type` ENUM('fixed', 'variable') DEFAULT 'fixed' COMMENT 'Fixed = same date every year, Variable = changes yearly',
    `recurring` TINYINT(1) DEFAULT 1 COMMENT '1 = Repeats every year on same month-day, 0 = One-time event',
    `description` TEXT DEFAULT NULL COMMENT 'Optional description',
    `applies_to_all` TINYINT(1) DEFAULT 1 COMMENT '1 = Applies to all departments, 0 = Specific departments only',
    `status` TINYINT(1) DEFAULT 1 COMMENT '1 = Active, 0 = Inactive',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_date` (`date`),
    INDEX `idx_status` (`status`),
    INDEX `idx_recurring` (`recurring`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Holiday-Department mapping table for department-specific holidays
CREATE TABLE IF NOT EXISTS `holiday_departments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `holiday_id` INT(11) NOT NULL,
    `department_id` INT(11) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_holiday_dept` (`holiday_id`, `department_id`),
    INDEX `idx_holiday_id` (`holiday_id`),
    INDEX `idx_department_id` (`department_id`),
    CONSTRAINT `fk_holiday_departments_holiday` FOREIGN KEY (`holiday_id`) REFERENCES `holidays` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_holiday_departments_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default Philippine holidays
INSERT INTO `holidays` (`name`, `date`, `holiday_type`, `recurring`, `description`, `applies_to_all`, `status`) VALUES
('New Year''s Day', '2025-01-01', 'fixed', 1, 'First day of the year', 1, 1),
('EDSA People Power Revolution Anniversary', '2025-02-25', 'fixed', 1, 'Commemoration of the 1986 EDSA Revolution', 1, 1),
('Araw ng Kagitingan (Day of Valor)', '2025-04-09', 'fixed', 1, 'Honors Filipino and American soldiers who fought in WWII', 1, 1),
('Labor Day', '2025-05-01', 'fixed', 1, 'International Workers'' Day', 1, 1),
('Independence Day', '2025-06-12', 'fixed', 1, 'Philippine Independence Day', 1, 1),
('Ninoy Aquino Day', '2025-08-21', 'fixed', 1, 'Death anniversary of Benigno Aquino Jr.', 1, 1),
('National Heroes Day', '2025-08-25', 'variable', 1, 'Last Monday of August', 1, 1),
('Davao Occidental Araw', '2025-10-28', 'fixed', 1, 'Davao Occidental Foundation Day', 1, 1),
('All Souls'' Evening', '2025-10-31', 'fixed', 1, 'Eve of All Saints'' Day', 1, 1),
('Araw ng Malita', '2025-11-17', 'fixed', 1, 'Malita Foundation Day', 1, 1),
('Bonifacio Day', '2025-11-30', 'fixed', 1, 'Birth anniversary of Andres Bonifacio', 1, 1),
('Christmas Day', '2025-12-25', 'fixed', 1, 'Christmas celebration', 1, 1),
('Rizal Day', '2025-12-30', 'fixed', 1, 'Death anniversary of Jose Rizal', 1, 1),
('New Year''s Eve', '2025-12-31', 'fixed', 1, 'Last day of the year', 1, 1),
('Maundy Thursday', '2025-04-17', 'variable', 0, 'Holy Week - Maundy Thursday 2025', 1, 1),
('Good Friday', '2025-04-18', 'variable', 0, 'Holy Week - Good Friday 2025', 1, 1),
('Maundy Thursday', '2026-04-02', 'variable', 0, 'Holy Week - Maundy Thursday 2026', 1, 1),
('Good Friday', '2026-04-03', 'variable', 0, 'Holy Week - Good Friday 2026', 1, 1);
