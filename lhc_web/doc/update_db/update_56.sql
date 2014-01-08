ALTER TABLE `lh_chat_online_user`
ADD `dep_id` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD INDEX `dep_id` (`dep_id`);