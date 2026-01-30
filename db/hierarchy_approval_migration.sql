-- Hierarchy Approval Migration
-- Creates the approval_hierarchy table for managing DTR approval tree structure

CREATE TABLE IF NOT EXISTS `approval_hierarchy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `position_order` int(11) DEFAULT 0,
  `level` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_personnel` (`personnel_id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_level` (`level`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_hierarchy_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_hierarchy_parent` FOREIGN KEY (`parent_id`) REFERENCES `approval_hierarchy` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add index for faster tree traversal
ALTER TABLE `approval_hierarchy` ADD INDEX `idx_parent_order` (`parent_id`, `position_order`);
