ALTER TABLE `lh_userdep` ADD `lastd_activity` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament` ADD `archive` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_group_chat` ADD `chat_id` bigint(20) NOT NULL DEFAULT '0', COMMENT='';