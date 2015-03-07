ALTER TABLE `lh_chat_online_user`
ADD `notes` varchar(250) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_auto_responder`
ADD `repeat_number` int(11) NOT NULL DEFAULT '1',
COMMENT='';

ALTER TABLE `lh_chat`
ADD `wait_timeout_repeat` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `repeat_number` int NOT NULL DEFAULT '1',
COMMENT='';

ALTER TABLE `lh_abstract_email_template`
ADD `user_mail_as_sender` tinyint(4) NOT NULL,
COMMENT='';