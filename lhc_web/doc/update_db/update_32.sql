INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('pro_active_show_if_offline',	'0',	0,	'Should invitation logic be executed if there is no online operators, 0 - no, 1 - yes',	0);

ALTER TABLE `lh_chat`
ADD `user_typing_txt` varchar(50) NOT NULL,
COMMENT='';