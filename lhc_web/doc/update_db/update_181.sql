ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `design_data` longtext NOT NULL, COMMENT='';
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('reverse_pending','0','0','Make default pending chats order from old to new','0');
ALTER TABLE `lh_abstract_widget_theme` ADD `pending_join_queue` varchar(250) NOT NULL, COMMENT='';