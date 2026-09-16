CREATE TABLE `lh_mcp_session` (
  `session_id` varchar(36) NOT NULL,
  `data` longtext NOT NULL,
  `ctime` int(11) unsigned NOT NULL,
  `utime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `utime` (`utime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
