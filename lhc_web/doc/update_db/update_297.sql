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

ALTER TABLE `lh_abstract_chat_priority` ADD `role_destination` varchar(50) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_chat_priority` ADD `present_role_is` varchar(50) NOT NULL, COMMENT='';

CREATE TABLE `lh_brand` (
                            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                            `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lh_brand_member` (
                                   `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                   `dep_id` bigint(20) unsigned NOT NULL,
                                   `brand_id` bigint(20) unsigned NOT NULL,
                                   `role` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                                   PRIMARY KEY (`id`),
                                   KEY `dep_id` (`dep_id`),
                                   KEY `brand_id_role` (`brand_id`,`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;