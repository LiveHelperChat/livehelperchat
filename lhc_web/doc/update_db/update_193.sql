ALTER TABLE `lh_abstract_chat_column` ADD `chat_enabled` tinyint(1) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_chat_column` ADD `online_enabled` tinyint(1) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_chat_column` ADD INDEX `online_enabled` (`online_enabled`);
ALTER TABLE `lh_abstract_chat_column` ADD INDEX `chat_enabled` (`chat_enabled`);