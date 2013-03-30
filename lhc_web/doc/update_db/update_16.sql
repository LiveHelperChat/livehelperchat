ALTER TABLE `lh_chat`
ADD `has_unread_messages` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD `last_user_msg_time` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD INDEX `has_unread_messages_dep_id_id` (`has_unread_messages`, `dep_id`, `id`);

ALTER TABLE `lh_chat`
ADD INDEX `status_dep_id_id` (`status`, `dep_id`, `id`);

INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`)
VALUES ('enable_pending_list', '', '');

INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`)
VALUES ('enable_active_list', '', '');

INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`)
VALUES ('enable_close_list', '', '');

INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`)
VALUES ('enable_unread_list', '', '');

ALTER TABLE `lh_msg`
ADD INDEX `chat_id_user_id_status` (`chat_id`, `user_id`, `status`);

ALTER TABLE `lh_msg`
ADD INDEX `chat_id_id` (`chat_id`, `id`);

DROP TABLE `lh_transfer`;

CREATE TABLE `lh_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL,
  `dep_id` int(11) NOT NULL,
  `transfer_user_id` int(11) NOT NULL,
  `from_dep_id` int(11) NOT NULL,
  `transfer_to_user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dep_id` (`dep_id`),
  KEY `transfer_user_id_dep_id` (`transfer_user_id`,`dep_id`),
  KEY `transfer_to_user_id` (`transfer_to_user_id`)
);


