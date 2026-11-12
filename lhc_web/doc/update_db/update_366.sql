-- Passkeys (WebAuthn) support.
-- Note: if `lh_users_passkey` already exists (created by an earlier build of this file),
-- running the UPGRADE page is enough - structure.json will widen `credential_id` to
-- varbinary(1364) and replace the `credential_id` index with `credential_id_full`.
ALTER TABLE `lh_users` ADD `api_key_login_only` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `lh_users` ADD `disable_autologin` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `lh_users` ADD `passwordless_login` tinyint(1) unsigned NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `lh_users_passkey` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `credential_id` varbinary(1364) NOT NULL,
  `credential` longtext NOT NULL,
  `name` varchar(100) NOT NULL,
  `aaguid` varchar(36) NOT NULL,
  `counter` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `last_used_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `credential_id_full` (`credential_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('passkey_options','','0','','1');