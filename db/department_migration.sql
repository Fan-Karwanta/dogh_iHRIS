-- Department Management Migration
-- Run this SQL to add department support to the system

-- Create departments table
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#3498db',
  `icon` varchar(50) DEFAULT 'fas fa-building',
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default departments (Main, Admin, Dialysis)
INSERT INTO `departments` (`name`, `code`, `description`, `color`, `icon`, `status`) VALUES
('Main', 'MAIN', 'Main Department - General hospital operations', '#3498db', 'fas fa-hospital', 1),
('Admin', 'ADMIN', 'Administrative Department - Office and administrative staff', '#27ae60', 'fas fa-user-tie', 1),
('Dialysis', 'DIALYSIS', 'Dialysis Department - Dialysis unit personnel', '#e74c3c', 'fas fa-heartbeat', 1);

-- Add department_id column to personnels table
ALTER TABLE `personnels` 
ADD COLUMN `department_id` int(11) DEFAULT NULL AFTER `status`,
ADD INDEX `idx_department_id` (`department_id`),
ADD CONSTRAINT `fk_personnels_department` FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Note: Existing personnel will have NULL department_id
-- You can assign them to departments using the new Department Management feature
