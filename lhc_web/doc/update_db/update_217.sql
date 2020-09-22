CREATE TABLE `lh_generic_bot_command` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `command` varchar(50) NOT NULL,
  `bot_id` int(11) NOT NULL,
  `trigger_id` int(11) NOT NULL,
  `dep_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dep_id` (`dep_id`),
  KEY `command` (`command`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;