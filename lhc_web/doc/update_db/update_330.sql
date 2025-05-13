ALTER TABLE `lh_abstract_chat_column` ADD `chat_window_enabled` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_chat_column` ADD INDEX `chat_window_enabled` (`chat_window_enabled`);
UPDATE `lh_chat_config` SET value = '330' WHERE `identifier` = 'version_updates' LIMIT 1;