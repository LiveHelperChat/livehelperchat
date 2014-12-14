ALTER TABLE `lh_abstract_auto_responder`
ADD `dep_id` int NOT NULL,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('banned_ip_range','',0,'Which ip should not be allowed to chat',0);