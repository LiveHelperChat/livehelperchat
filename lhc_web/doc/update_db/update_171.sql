ALTER TABLE `lh_generic_bot_chat_workflow` ADD `time` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_widget_theme` ADD `bot_configuration` longtext NOT NULL, COMMENT='';
ALTER TABLE `lh_generic_bot_bot` ADD `nick` varchar(100) NOT NULL, COMMENT='';