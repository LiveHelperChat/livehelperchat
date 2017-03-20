INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('inform_unread_message',	'0',	0,	'Inform visitor about unread messages from operator, value in minutes. 0 - disabled',	0);

ALTER TABLE `lh_chat` ADD `last_op_msg_time` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat` ADD `has_unread_op_messages` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat` ADD `unread_op_messages_informed` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat` ADD INDEX `unread_operator` (`has_unread_op_messages`, `unread_op_messages_informed`);

INSERT INTO `lh_abstract_email_template` (`id`,`name`,`from_name`,`from_name_ac`,`from_email`,`from_email_ac`,`content`,`subject`,`bcc_recipients`,`subject_ac`,`reply_to`,`reply_to_ac`,`recipient`) VALUES (11, 'You have unread messages',	'Live Helper Chat',	0,	'',	0,	'Hello,\r\n\r\nOperator {operator} has answered to you\r\n\r\n{messages}\r\n\r\nSincerely,\r\nLive Support Team','Operator has answered to your request','',0,'',0,'');

ALTER TABLE `lh_abstract_survey` ADD `max_stars_1_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `max_stars_2_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `max_stars_3_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `max_stars_4_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `max_stars_5_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `question_options_1_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `question_options_2_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `question_options_3_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `question_options_4_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `question_options_5_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `question_plain_1_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `question_plain_2_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `question_plain_3_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `question_plain_4_req` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD `question_plain_5_req` int(11) NOT NULL, COMMENT='';