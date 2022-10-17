CREATE TABLE `lh_abstract_saved_report` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `params` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_id` bigint(20) unsigned NOT NULL,
    `position` int(11) unsigned NOT NULL,
    `days` int(11) unsigned NOT NULL,
    `date_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `days_end` int(11) unsigned NOT NULL,
    `updated_at` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;