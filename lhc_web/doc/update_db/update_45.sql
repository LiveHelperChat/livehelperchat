ALTER TABLE `lh_users`
ADD `filepath` varchar(200) COLLATE 'utf8_general_ci' NOT NULL,
ADD `filename` varchar(200) COLLATE 'utf8_general_ci' NOT NULL AFTER `filepath`,
COMMENT='';

ALTER TABLE `lh_chat`
ADD `fbst` tinyint(1) NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `show_random_operator` int(11) NOT NULL,
COMMENT='';