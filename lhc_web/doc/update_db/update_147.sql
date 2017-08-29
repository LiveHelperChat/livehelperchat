ALTER TABLE `lh_abstract_auto_responder` ADD `only_proactive` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `name` varchar(50) NOT NULL, COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `autoresponder_id` int(11) NOT NULL, COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation` DROP `wait_message`,COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` DROP `wait_timeout`,COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` DROP `timeout_message`,COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` DROP `repeat_number`,COMMENT='';