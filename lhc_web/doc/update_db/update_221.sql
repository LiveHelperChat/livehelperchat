ALTER TABLE `lh_userdep` ADD `lastd_activity` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament` ADD `archive` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_group_chat` ADD `chat_id` bigint(20) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat_blocked_user` ADD `btype` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat_blocked_user` ADD `expires` int(11) NOT NULL DEFAULT '0', COMMENT='';

ALTER TABLE `lh_departament` ADD INDEX `archive` (`archive`);
ALTER TABLE `lh_group_chat` ADD INDEX `chat_id` (`chat_id`);