ALTER TABLE `lh_chat`
ADD `wait_time` int NOT NULL,
ADD `chat_duration` int NOT NULL AFTER `wait_time`,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('pro_active_limitation',	'-1',	0,	'Pro active chats invitations limitation based on pending chats, (-1) do not limit, (0,1,n+1) number of pending chats can be for invitation to be shown.',	0);

ALTER TABLE `lh_chat`
ADD `chat_variables` text NOT NULL,
COMMENT='';