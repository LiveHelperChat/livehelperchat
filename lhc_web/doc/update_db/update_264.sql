ALTER TABLE `lh_generic_bot_trigger` ADD `in_progress` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_generic_bot_trigger` ADD INDEX `in_progress` (`in_progress`);