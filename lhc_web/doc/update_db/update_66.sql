ALTER TABLE `lh_chat`
ADD `operation` varchar(150) COLLATE 'utf8_general_ci' NOT NULL;

ALTER TABLE `lh_chat`
ADD `screenshot_id` int NOT NULL,
COMMENT='';