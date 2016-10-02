ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `tag` varchar(50) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD INDEX `tag` (`tag`);