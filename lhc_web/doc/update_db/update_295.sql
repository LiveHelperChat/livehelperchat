CREATE TABLE `lh_abstract_msg_protection` (
                                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                              `pattern` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                              `enabled` int(11) NOT NULL DEFAULT 1,
                                              `remove` int(11) NOT NULL DEFAULT 0,
                                              `v_warning` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                              PRIMARY KEY (`id`),
                                              KEY `enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
