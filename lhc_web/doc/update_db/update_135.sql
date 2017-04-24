ALTER TABLE `lh_chat` ADD `sender_user_id` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat` ADD INDEX `user_id_sender_user_id` (`user_id`, `sender_user_id`);
ALTER TABLE `lh_chat` ADD INDEX `sender_user_id` (`sender_user_id`);
ALTER TABLE `lh_chat` DROP INDEX `user_id`;