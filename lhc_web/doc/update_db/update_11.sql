ALTER TABLE `lh_chat`
ADD `user_typing` int NOT NULL,
ADD `operator_typing` int NOT NULL AFTER `user_typing`,
COMMENT='';