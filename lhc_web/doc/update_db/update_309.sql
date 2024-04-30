ALTER TABLE `lh_generic_bot_command` ADD `position` int(11) unsigned NOT NULL DEFAULT '1000', COMMENT='';

CREATE TABLE `lhc_mailconv_sent_copy` (
                                          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                          `mailbox_id` bigint(20) unsigned NOT NULL,
                                          `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
                                          `body` longblob NOT NULL,
                                          PRIMARY KEY (`id`),
                                          KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
