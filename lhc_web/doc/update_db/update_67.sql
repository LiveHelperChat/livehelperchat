ALTER TABLE `lh_chat_online_user`
ADD `operation` varchar(200) NOT NULL,
ADD `screenshot_id` int NOT NULL AFTER `operation`,
COMMENT='';