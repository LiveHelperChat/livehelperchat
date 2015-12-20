INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('hide_button_dropdown','0','0','Hide close button in dropdown','0');
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('on_close_exit_chat','0','0','On chat close exit chat','0');
ALTER TABLE `lh_departament` ADD `sort_priority` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_departament` ADD INDEX `sort_priority_name` (`sort_priority`,`name`);