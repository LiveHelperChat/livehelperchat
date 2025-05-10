ALTER TABLE `lh_users` ADD INDEX `username` (`username`);
INSERT IGNORE INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES  ('audit_configuration',	'a:7:{s:8:\"days_log\";i:90;s:11:\"log_objects\";a:0:{}s:6:\"log_js\";i:0;s:9:\"log_block\";i:0;s:11:\"log_routing\";i:0;s:9:\"log_files\";i:0;s:8:\"log_user\";i:0;}',	0,	'',	1);
