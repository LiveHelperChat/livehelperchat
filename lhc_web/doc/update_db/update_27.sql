ALTER TABLE `lh_chat`
ADD `lat` varchar(10) COLLATE 'utf8_general_ci' NOT NULL,
ADD `lon` varchar(10) COLLATE 'utf8_general_ci' NOT NULL AFTER `lat`,
ADD `city` varchar(100) COLLATE 'utf8_general_ci' NOT NULL AFTER `lon`,
COMMENT='';

CREATE TABLE `lh_chat_online_user_footprint` (
  `id` int NOT NULL,
  `chat_id` int NOT NULL,
  `online_user_id` int NOT NULL,
  `page` varchar(250) NOT NULL,
  `vtime` varchar(250) NOT NULL
) COMMENT='';