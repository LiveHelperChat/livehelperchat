CREATE TABLE `lh_canned_msg_replace` (
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
     `identifier` varchar(50) NOT NULL,
     `default` text NOT NULL, `conditions` longtext NOT NULL,
     PRIMARY KEY (`id`),
     KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;