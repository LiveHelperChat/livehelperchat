ALTER TABLE `lh_group`
ADD `disabled` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_group`
ADD INDEX `disabled` (`disabled`);

ALTER TABLE `lh_abstract_widget_theme`
ADD `name_company` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `name`,
COMMENT='';

ALTER TABLE `lh_users`
ADD `rec_per_req` tinyint(1) NOT NULL,
COMMENT='';

ALTER TABLE `lh_users`
ADD INDEX `rec_per_req` (`rec_per_req`);

INSERT INTO `lh_abstract_email_template` (`id`, `name`, `from_name`, `from_name_ac`, `from_email`, `from_email_ac`, `content`, `subject`, `subject_ac`, `reply_to`, `reply_to_ac`, `recipient`, `bcc_recipients`, `user_mail_as_sender`) VALUES
(10,	'Permission request',	'Live Helper Chat',	0,	'',	0,	'Hello,\r\n\r\nOperator {user} has requested these permissions\n\r\n{permissions}\r\n\r\nSincerely,\r\nLive Support Team',	'Permission request from {user}',	0,	'',	0,	'',	'',	0);

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('sharing_nodejs_path','',0,'socket.io path, optional',0);
