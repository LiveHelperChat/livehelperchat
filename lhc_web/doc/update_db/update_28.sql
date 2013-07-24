ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `identifier` varchar(50) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `identifier` varchar(50) NOT NULL,
COMMENT='';