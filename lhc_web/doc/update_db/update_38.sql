ALTER TABLE `lh_chat_online_user`
ADD `total_visits` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `message_seen_ts` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD `online_user_id` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `tt_pages_count` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `invitation_count` int(11) NOT NULL,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('message_seen_timeout', 24, 0, 'Proactive message timeout in hours. After how many hours proactive chat mesasge should be shown again.',	0);

ALTER TABLE `lh_chat`
ADD INDEX `online_user_id` (`online_user_id`);