ALTER TABLE `lh_canned_msg` ADD `updated_at` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD `created_at` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD `active_from` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD `active_to` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD `repetitiveness` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD `days_activity` text NOT NULL, COMMENT='';
ALTER TABLE `lh_canned_msg` ADD INDEX `repetitiveness` (`repetitiveness`);

CREATE TABLE `lh_chat_action` (
                                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                  `chat_id` bigint(20) NOT NULL,
                                  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                  `created_at` bigint(20) unsigned NOT NULL,
                                  PRIMARY KEY (`id`),
                                  KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
