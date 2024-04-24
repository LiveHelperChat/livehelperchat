ALTER TABLE `lh_generic_bot_command` ADD `enabled_display` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_generic_bot_command` ADD `name` varchar(50) NOT NULL, COMMENT='';
ALTER TABLE `lh_generic_bot_command` ADD `fields` text NOT NULL, COMMENT='';