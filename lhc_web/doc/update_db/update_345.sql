ALTER TABLE `lh_generic_bot_bot` ADD `short_name` varchar(50) NOT NULL DEFAULT '' COMMENT '';
UPDATE `lh_chat_config` SET value = '345' WHERE `identifier` = 'version_updates' LIMIT 1;