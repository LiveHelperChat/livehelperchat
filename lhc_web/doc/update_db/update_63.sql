ALTER TABLE `lh_users`
ADD `time_zone` varchar(100) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_file`
ADD INDEX `user_id` (`user_id`);