ALTER TABLE `lh_users` ADD `auto_accept` tinyint(1) NOT NULL, COMMENT='';
ALTER TABLE `lh_users` ADD `max_active_chats` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_userdep` ADD `pending_chats` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_userdep` ADD `inactive_chats` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_userdep` ADD `max_chats` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_departament` ADD `exclude_inactive_chats` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_departament` ADD `max_ac_dep_chats` int(11) NOT NULL, COMMENT='';