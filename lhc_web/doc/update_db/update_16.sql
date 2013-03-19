ALTER TABLE `lh_msg`
ADD INDEX `user_id_status_chat_id` (`user_id`, `status`, `chat_id`);