ALTER TABLE `lh_generic_bot_trigger` ADD `default_always` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_generic_bot_trigger` ADD INDEX `default_always` (`default_always`);