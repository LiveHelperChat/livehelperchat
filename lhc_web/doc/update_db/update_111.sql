INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('online_if','0','0','','0');
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('track_mouse_activity','0','0','Should mouse movement be tracked as activity measure, if not checked only basic events would be tracked','0');
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('track_activity','0','0','Track users activity on site?','0');
ALTER TABLE `lh_chat_online_user` ADD `user_active` int(11) NOT NULL, COMMENT='';
