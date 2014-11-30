ALTER TABLE `lh_abstract_form_collected`
ADD `identifier` varchar(250) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `message_returning` text NOT NULL,
ADD `message_returning_nick` varchar(100) COLLATE 'utf8_general_ci' NOT NULL AFTER `message_returning`,
COMMENT='';