ALTER TABLE `lh_chat` ADD INDEX `time` (`time`);
ALTER TABLE `lh_audits` ADD INDEX `time` (`time`);
ALTER TABLE `lh_departament_group_user` ADD `assign_priority` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament_group_user` ADD `chat_min_priority` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament_group_user` ADD `chat_max_priority` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_userdep` ADD `assign_priority` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_userdep` ADD `chat_max_priority` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_userdep` ADD `chat_min_priority` int(11) NOT NULL DEFAULT '0', COMMENT='';