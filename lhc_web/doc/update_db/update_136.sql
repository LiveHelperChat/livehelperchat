ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `dynamic_invitation` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD INDEX `dynamic_invitation` (`dynamic_invitation`);
ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `iddle_for` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `event_type` int(11) NOT NULL, COMMENT='';