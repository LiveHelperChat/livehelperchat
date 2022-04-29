CREATE TABLE `lh_generic_bot_rest_api_cache` (
                                                 `hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                                                 `rest_api_id` bigint(20) unsigned NOT NULL,
                                                 `response` text NOT NULL,
                                                 `ctime` bigint(20) NOT NULL,
                                                 UNIQUE KEY `rest_api_id_hash` (`rest_api_id`,`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;