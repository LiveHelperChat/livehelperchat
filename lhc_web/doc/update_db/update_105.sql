ALTER TABLE `lh_cobrowse`
ADD `online_user_id` int(11) NOT NULL AFTER `chat_id`,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
CHANGE `operation` `operation` text COLLATE 'utf8_general_ci' NOT NULL AFTER `reopen_chat`,
COMMENT='';