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

ALTER TABLE `lh_transfer`
CHANGE `user_id` `dep_id` int(11) NOT NULL AFTER `chat_id`,
COMMENT='';

DELETE FROM `lh_transfer`;

ALTER TABLE `lh_transfer`
ADD `transfer_user_id` int(11) NOT NULL,
ADD `from_dep_id` int(11) NOT NULL AFTER `transfer_user_id`,
COMMENT='';

ALTER TABLE `lh_transfer`
ADD `transfer_to_user_id` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_transfer`
ADD INDEX `transfer_user_id` (`transfer_user_id`),
ADD INDEX `dep_id` (`dep_id`);

ALTER TABLE `lh_transfer`
ADD INDEX `transfer_user_id_dep_id` (`transfer_user_id`, `dep_id`),
DROP INDEX `transfer_user_id`;

ALTER TABLE `lh_transfer`
ADD INDEX `transfer_to_user_id` (`transfer_to_user_id`);

ALTER TABLE `lh_msg`
ADD INDEX `chat_id_user_id_status` (`chat_id`, `user_id`, `status`);
