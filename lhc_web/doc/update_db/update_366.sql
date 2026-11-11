ALTER TABLE `lh_users` ADD `api_key_login_only` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `lh_users` ADD `disable_autologin` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `lh_users` ADD `passwordless_login` tinyint(1) unsigned NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `lh_users_passkey` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `credential_id` varchar(255) NOT NULL,
  `credential` longtext NOT NULL,
  `name` varchar(100) NOT NULL,
  `aaguid` varchar(36) NOT NULL,
  `counter` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `last_used_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `credential_id` (`credential_id`(191)),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('passkey_options','','0','','1');