ALTER TABLE `lh_chat_online_user`
ADD `page_title` varchar(250) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('ignorable_ip',	'',	0,	'Which ip should be ignored in online users list, separate by comma',0);