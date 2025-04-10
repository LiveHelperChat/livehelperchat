CREATE TABLE `lh_notification_op_subscriber` ( `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `user_id` bigint(20) unsigned NOT NULL, `device_type` tinyint(1) NOT NULL, `ctime` bigint(20) unsigned NOT NULL, `utime` bigint(20) unsigned NOT NULL, `status` tinyint(1) unsigned NOT NULL, `achat` tinyint(1) unsigned NOT NULL, `pchat` tinyint(1) unsigned NOT NULL, `params` text COLLATE utf8mb4_unicode_ci NOT NULL, `last_error` text COLLATE utf8mb4_unicode_ci NOT NULL, `subscriber_hash` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL, PRIMARY KEY (`id`), KEY `status` (`status`), KEY `user_id` (`user_id`), KEY `subscriber_hash` (`subscriber_hash`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;