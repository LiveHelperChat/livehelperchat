ALTER TABLE `lh_users_online_session` ADD KEY `user_id_time` (`user_id`, `time`);

CREATE TABLE `lh_abstract_performance` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `created_at` bigint(20) unsigned NOT NULL,
  `data` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_id` (`type`,`id` DESC)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;