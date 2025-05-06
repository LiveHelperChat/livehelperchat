ALTER TABLE `lh_transfer` ADD INDEX `chat_id_transfer` (`chat_id`,`transfer_scope`), ALGORITHM=INPLACE, LOCK=NONE;
ALTER TABLE `lh_userdep` ADD INDEX `online_op_widget_2` (`dep_id`, `last_activity`, `user_id`), ALGORITHM=INPLACE, LOCK=NONE;
ALTER TABLE `lh_userdep` ADD INDEX `online_op_widget_3` (`user_id`, `active_chats`), ALGORITHM=INPLACE, LOCK=NONE;