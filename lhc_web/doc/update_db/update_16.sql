ALTER TABLE `lh_chat`
ADD `has_unread_messages` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD `last_user_msg_time` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD INDEX `has_unread_messages` (`has_unread_messages`);