ALTER TABLE `lh_chat`
ADD `chat_initiator` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `operator_name` varchar(100) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `operator_user_proactive` varchar(100) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';