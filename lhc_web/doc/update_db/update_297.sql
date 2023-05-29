CREATE TABLE `lh_chat_participant` (
                                       `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                       `chat_id` bigint(20) NOT NULL,
                                       `user_id` bigint(20) NOT NULL,
                                       `duration` int(11) unsigned NOT NULL,
                                       `time` bigint(20) unsigned NOT NULL,
                                       `dep_id` bigint(20) unsigned NOT NULL,
                                       PRIMARY KEY (`id`),
                                       KEY `chat_id` (`chat_id`),
                                       KEY `time` (`time`),
                                       KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;