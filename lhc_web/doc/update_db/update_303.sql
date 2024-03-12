ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `active_from` bigint(20) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `active_to` bigint(20) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `repetitiveness` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `days_activity` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `url_present` varchar(100) NOT NULL, COMMENT='';

CREATE TABLE `lhc_mailconv_delete_item` (
                                            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                            `conversation_id` bigint(20) unsigned NOT NULL,
                                            `filter_id` bigint(20) unsigned NOT NULL,
                                            `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
                                            PRIMARY KEY (`id`),
                                            KEY `filter_id_status` (`filter_id`,`status`),
                                            KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lhc_mailconv_delete_filter` (
                                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                              `updated_at` bigint(20) unsigned NOT NULL,
                                              `created_at` bigint(20) unsigned NOT NULL,
                                              `user_id` bigint(20) unsigned NOT NULL,
                                              `archive_id` int(11) unsigned NOT NULL DEFAULT 0,
                                              `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
                                              `last_id` bigint(20) unsigned NOT NULL DEFAULT 0,
                                              `started_at` bigint(20) unsigned NOT NULL DEFAULT 0,
                                              `finished_at` bigint(20) unsigned NOT NULL DEFAULT 0,
                                              `processed_records` bigint(20) unsigned NOT NULL DEFAULT 0,
                                              `filter` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                              `filter_input` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                              PRIMARY KEY (`id`),
                                              KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `lhc_mailconv_mailbox` ADD `delete_on_archive` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lhc_mailconv_mailbox` ADD `delete_policy` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';