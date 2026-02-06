-- Migration: Track which biometric fields were set via approved clock change requests
-- This table allows DTR views to highlight clock-change-embedded entries

CREATE TABLE IF NOT EXISTS `clock_change_embedded` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `bio_id` int(11) NOT NULL,
    `date` date NOT NULL,
    `field` enum('am_in','am_out','pm_in','pm_out') NOT NULL,
    `time_value` time DEFAULT NULL,
    `request_id` int(11) NOT NULL COMMENT 'References clock_change_requests.id',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_bio_date_field` (`bio_id`, `date`, `field`),
    KEY `idx_bio_date` (`bio_id`, `date`),
    KEY `idx_request_id` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
