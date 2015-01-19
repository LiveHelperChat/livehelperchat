ALTER TABLE `lh_abstract_auto_responder`
ADD `dep_id` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
CHANGE `operator_message` `operator_message` text COLLATE 'utf8_general_ci' NOT NULL AFTER `user_country_name`,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('banned_ip_range','',0,'Which ip should not be allowed to chat',0);

ALTER TABLE `lh_chat`
ADD `user_closed_ts` int NOT NULL,
COMMENT='';