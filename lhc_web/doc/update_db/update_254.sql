CREATE TABLE `lh_abstract_saved_search` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `params` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_id` bigint(20) unsigned NOT NULL,
    `position` int(11) unsigned NOT NULL,
    `scope` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `days` int(11) unsigned NOT NULL,
    `updated_at` bigint(20) unsigned NOT NULL,
    `requested_at` bigint(20) unsigned NOT NULL,
    `total_records` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `scope` (`scope`),
    KEY `updated_at` (`updated_at`),
    KEY `requested_at` (`requested_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;