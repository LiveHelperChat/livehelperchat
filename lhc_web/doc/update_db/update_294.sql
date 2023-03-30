CREATE TABLE `lh_userdep_alias` (
                                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                    `dep_id` bigint(20) unsigned NOT NULL,
                                    `dep_group_id` bigint(20) unsigned NOT NULL,
                                    `user_id` bigint(20) unsigned NOT NULL,
                                    `nick` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `filepath` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `filename` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `avatar` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    PRIMARY KEY (`id`),
                                    KEY `dep_id_user_id` (`dep_id`,`user_id`),
                                    KEY `dep_group_id_user_id` (`dep_group_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
