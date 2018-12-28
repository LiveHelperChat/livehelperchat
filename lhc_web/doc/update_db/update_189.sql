ALTER TABLE `lh_generic_bot_trigger_event` ADD `pattern_exc` varchar(250) NOT NULL, COMMENT='';
ALTER TABLE `lh_generic_bot_trigger_event` CHANGE `pattern` `pattern` varchar(250) NOT NULL;
ALTER TABLE `lh_generic_bot_trigger_event` ADD `configuration` text NOT NULL, COMMENT='';