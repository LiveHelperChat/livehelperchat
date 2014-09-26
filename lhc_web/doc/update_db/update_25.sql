ALTER TABLE `lh_chat_online_user`
ADD `lat` varchar(10) COLLATE 'utf8_general_ci' NOT NULL,
ADD `lon` varchar(10) COLLATE 'utf8_general_ci' NOT NULL AFTER `lat`,
ADD `city` varchar(100) COLLATE 'utf8_general_ci' NOT NULL AFTER `lon`,
COMMENT='';