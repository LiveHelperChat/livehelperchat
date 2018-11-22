CREATE TABLE `lh_abstract_chat_column` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `column_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `variable` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `conditions` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `column_icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `column_identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lh_abstract_chat_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `dep_id` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dep_id` (`dep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;