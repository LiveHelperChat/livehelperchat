ALTER TABLE `lh_chat` ADD `unanswered_chat` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat` ADD INDEX `unanswered_chat` (`unanswered_chat`);
ALTER TABLE `lh_users` ADD `attr_int_1` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_users` ADD `attr_int_2` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_users` ADD `attr_int_3` int(11) NOT NULL, COMMENT='';