ALTER TABLE `lh_webhook` ADD `type` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_webhook` ADD `configuration` longtext NOT NULL, COMMENT='';

CREATE TABLE `lh_abstract_stats` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `lupdate` bigint(20) NOT NULL,
  `object_id` bigint(20) NOT NULL,
  `stats` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_object_id` (`type`,`object_id`)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;