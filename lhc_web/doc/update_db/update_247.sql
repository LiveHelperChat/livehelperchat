ALTER TABLE `lh_abstract_chat_priority` ADD `sort_priority` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_chat_priority` ADD `dest_dep_id` int(11) NOT NULL DEFAULT '0', COMMENT='';
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('vwait_to_long','120','0','How long we should wait before we inform operator about unanswered chat.','0');