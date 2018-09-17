ALTER TABLE `lh_admin_theme` ADD `css_attributes` longtext NOT NULL, COMMENT='';
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('mheight_op','200','0','Messages box height for operator','0');
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('listd_op','10','0','Default number of online operators to show','0');