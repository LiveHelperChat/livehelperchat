ALTER TABLE `lh_users_online_session` ADD INDEX `lactivity` (`lactivity`);
ALTER TABLE `lh_notification_subscriber` ADD INDEX `subscriber_hash` (`subscriber_hash`);
ALTER TABLE `lh_chat` ADD INDEX `nick` (`nick`);
ALTER TABLE `lh_chat` ADD INDEX `email` (`email`);
ALTER TABLE `lh_canned_msg_use` ADD INDEX IF NOT EXISTS `chat_id` (`chat_id`);