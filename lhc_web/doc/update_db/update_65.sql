ALTER TABLE `lh_canned_msg`
ADD `department_id` int(11) NOT NULL,
ADD `user_id` int(11) NOT NULL AFTER `department_id`,
COMMENT='';

ALTER TABLE `lh_canned_msg`
ADD INDEX `department_id` (`department_id`),
ADD INDEX `user_id` (`user_id`);

ALTER TABLE `lh_chat_online_user`
ADD `reopen_chat` int(11) NOT NULL,
COMMENT='';