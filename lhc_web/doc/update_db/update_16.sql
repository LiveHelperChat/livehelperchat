ALTER TABLE `lh_chat`
ADD `has_unread_messages` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD `last_user_msg_time` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD INDEX `has_unread_messages` (`has_unread_messages`);

INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`)
VALUES ('enable_pending_list', '', '');

INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`)
VALUES ('enable_active_list', '', '');

INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`)
VALUES ('enable_close_list', '', '');

INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`)
VALUES ('enable_unread_list', '', '');