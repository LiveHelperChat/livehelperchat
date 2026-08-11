CREATE TABLE `lh_abstract_offline_reason` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `name` varchar(250) NOT NULL, `description` text NOT NULL, `icon` varchar(250) NOT NULL, `pos` int(11) unsigned NOT NULL DEFAULT 0, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `lh_users_online_session` ADD `offline_reason_id` int(11) unsigned NOT NULL DEFAULT 0, COMMENT='';
ALTER TABLE `lh_users` ADD `offline_reason_id` int(11) unsigned NOT NULL DEFAULT 0, COMMENT='';
