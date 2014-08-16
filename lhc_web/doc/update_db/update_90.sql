ALTER TABLE `lh_chat_online_user`
ADD `last_check_time` int(11) NOT NULL,
COMMENT='';
INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('track_is_online','0',0,'Track is user still on site, chat status checks also has to be enabled',0);