ALTER TABLE `lh_departament` ADD `max_load` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament` ADD `bot_chats_counter` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament` ADD INDEX `bot_chats_counter` (`bot_chats_counter`);
ALTER TABLE `lh_departament` DROP IF EXISTS `closed_chats_counter`,COMMENT='';

ALTER TABLE `lh_departament_group` ADD `achats_cnt` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament_group` ADD `pchats_cnt` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament_group` ADD `bchats_cnt` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament_group` ADD `max_load` int(11) NOT NULL DEFAULT '0', COMMENT='';

ALTER TABLE `lh_users` DROP IF EXISTS `active_chats_counter`,COMMENT='';
ALTER TABLE `lh_users` DROP IF EXISTS `closed_chats_counter`,COMMENT='';
ALTER TABLE `lh_users` DROP IF EXISTS `pending_chats_counter`,COMMENT='';

ALTER TABLE `lh_departament` ADD `max_load_h` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament_group` ADD `max_load_h` int(11) NOT NULL DEFAULT '0', COMMENT='';