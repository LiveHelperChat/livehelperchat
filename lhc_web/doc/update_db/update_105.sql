ALTER TABLE `lh_cobrowse`
ADD `online_user_id` int(11) NOT NULL AFTER `chat_id`,
COMMENT='';

ALTER TABLE `lh_cobrowse`
ADD INDEX `online_user_id` (`online_user_id`);

ALTER TABLE `lh_chat_online_user`
CHANGE `operation` `operation` text COLLATE 'utf8_general_ci' NOT NULL AFTER `reopen_chat`,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `online_attr_system` text COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `operation_chat` text COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_file`
ADD `online_user_id` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_file`
ADD INDEX `online_user_id` (`online_user_id`);

