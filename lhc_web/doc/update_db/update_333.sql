ALTER TABLE `lh_abstract_chat_variable` ADD `case_insensitive` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
UPDATE `lh_abstract_chat_variable` SET `case_insensitive` = 1 WHERE `type` = 4;
UPDATE `lh_abstract_chat_variable` SET `type` = 0 WHERE `type` = 4;
UPDATE `lh_chat_config` SET value = '333' WHERE `identifier` = 'version_updates' LIMIT 1;