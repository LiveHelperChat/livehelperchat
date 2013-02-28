ALTER TABLE `lh_chat_online_user`
ADD `operator_message` varchar(250) COLLATE 'utf8_general_ci' NOT NULL,
ADD `operator_user_id` int NOT NULL AFTER `operator_message`,
ADD `message_seen` int NOT NULL AFTER `operator_user_id`,
COMMENT='';