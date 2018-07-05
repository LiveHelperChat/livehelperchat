ALTER TABLE `lh_abstract_auto_responder` ADD `bot_configuration` text NOT NULL, COMMENT='';
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('list_unread','0','0','List unread chats','0');
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('list_closed','0','0','List closed chats','0');
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('disable_live_autoassign','0','0','Disable live auto assign','0');
ALTER TABLE `lh_generic_bot_bot` ADD `attr_str_1` varchar(100) NOT NULL, COMMENT='';
ALTER TABLE `lh_generic_bot_bot` ADD `attr_str_2` varchar(100) NOT NULL, COMMENT='';
ALTER TABLE `lh_generic_bot_bot` ADD `attr_str_3` varchar(100) NOT NULL, COMMENT='';
ALTER TABLE `lh_chat` ADD INDEX `status` (`status`);