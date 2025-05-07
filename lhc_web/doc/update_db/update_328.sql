INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('del_on_close_no_msg','0','0','Delete chat on close if there are no messages from the visitor','0');

CREATE TABLE `lh_userdep_disabled` (
                              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                              `user_id` int(11) NOT NULL,
                              `dep_id` int(11) NOT NULL,
                              `last_activity` bigint(20) unsigned NOT NULL,
                              `hide_online` int(11) NOT NULL,
                              `last_accepted` bigint(20) unsigned NOT NULL DEFAULT 0,
                              `active_chats` int(11) NOT NULL DEFAULT 0,
                              `type` int(11) NOT NULL DEFAULT 0,
                              `dep_group_id` int(11) NOT NULL DEFAULT 0,
                              `hide_online_ts` bigint(20) unsigned NOT NULL DEFAULT 0,
                              `pending_chats` int(11) NOT NULL DEFAULT 0,
                              `inactive_chats` int(11) NOT NULL DEFAULT 0,
                              `max_chats` int(11) NOT NULL DEFAULT 0,
                              `exclude_autoasign` tinyint(1) NOT NULL DEFAULT 0,
                              `ro` tinyint(1) NOT NULL DEFAULT 0,
                              `always_on` tinyint(1) NOT NULL DEFAULT 0,
                              `lastd_activity` bigint(20) unsigned NOT NULL DEFAULT 0,
                              `exc_indv_autoasign` tinyint(1) NOT NULL DEFAULT 0,
                              `exclude_autoasign_mails` tinyint(1) NOT NULL DEFAULT 0,
                              `active_mails` int(11) NOT NULL DEFAULT 0,
                              `pending_mails` int(11) NOT NULL DEFAULT 0,
                              `max_mails` int(11) NOT NULL DEFAULT 0,
                              `last_accepted_mail` bigint(20) unsigned NOT NULL DEFAULT 0,
                              `assign_priority` int(11) NOT NULL DEFAULT 0,
                              `chat_max_priority` int(11) NOT NULL DEFAULT 0,
                              `chat_min_priority` int(11) NOT NULL DEFAULT 0,
                              PRIMARY KEY (`id`),
                              KEY `dep_id` (`dep_id`),
                              KEY `user_id_type` (`user_id`,`type`),
                              KEY `last_activity_hide_online_dep_id` (`last_activity`,`hide_online`,`dep_id`),
                              KEY `online_op_widget_2` (`dep_id`,`last_activity`,`user_id`),
                              KEY `online_op_widget_3` (`user_id`,`active_chats`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lh_departament_group_user_disabled` (
                                             `id` int(11) NOT NULL AUTO_INCREMENT,
                                             `dep_group_id` int(11) NOT NULL,
                                             `user_id` int(11) NOT NULL,
                                             `read_only` tinyint(1) unsigned NOT NULL DEFAULT 0,
                                             `exc_indv_autoasign` tinyint(1) unsigned NOT NULL DEFAULT 0,
                                             `assign_priority` int(11) NOT NULL DEFAULT 0,
                                             `chat_min_priority` int(11) NOT NULL DEFAULT 0,
                                             `chat_max_priority` int(11) NOT NULL DEFAULT 0,
                                             PRIMARY KEY (`id`),
                                             KEY `dep_group_id` (`dep_group_id`),
                                             KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `lh_userdep_disabled` SELECT * FROM `lh_userdep` WHERE `lh_userdep`.`user_id` IN (SELECT `id` FROM `lh_users` WHERE `disabled` = 1);
DELETE FROM `lh_userdep` WHERE `lh_userdep`.`user_id` IN (SELECT `id` FROM `lh_users` WHERE `disabled` = 1);

INSERT IGNORE INTO `lh_departament_group_user_disabled` SELECT * FROM `lh_departament_group_user` WHERE `lh_departament_group_user`.`user_id` IN (SELECT `id` FROM `lh_users` WHERE `disabled` = 1);
DELETE FROM `lh_departament_group_user` WHERE `lh_departament_group_user`.`user_id` IN (SELECT `id` FROM `lh_users` WHERE `disabled` = 1);

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('version_updates',	'328',	0,	'',	1);