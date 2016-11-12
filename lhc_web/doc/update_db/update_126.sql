ALTER TABLE `lh_departament` DROP `mod`,COMMENT=''
ALTER TABLE `lh_departament` DROP `tud`,COMMENT=''
ALTER TABLE `lh_departament` DROP `wed`,COMMENT=''
ALTER TABLE `lh_departament` DROP `thd`,COMMENT=''
ALTER TABLE `lh_departament` DROP `frd`,COMMENT=''
ALTER TABLE `lh_departament` DROP `sad`,COMMENT=''
ALTER TABLE `lh_departament` DROP `sud`,COMMENT=''
ALTER TABLE `lh_departament` DROP `start_hour`,COMMENT=''
ALTER TABLE `lh_departament` DROP `end_hour`,COMMENT=''
ALTER TABLE `lh_departament` ADD `mod_start_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `mod_end_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `tud_start_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `tud_end_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `wed_start_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `wed_end_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `thd_start_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `thd_end_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `frd_start_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `frd_end_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `sad_start_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `sad_end_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `sud_start_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD `sud_end_hour` int(4) NOT NULL DEFAULT '-1', COMMENT='';
ALTER TABLE `lh_departament` ADD INDEX `active_mod` (`online_hours_active`,`mod_start_hour`,`mod_end_hour`);
ALTER TABLE `lh_departament` ADD INDEX `active_tud` (`online_hours_active`,`tud_start_hour`,`tud_end_hour`);
ALTER TABLE `lh_departament` ADD INDEX `active_wed` (`online_hours_active`,`wed_start_hour`,`wed_end_hour`);
ALTER TABLE `lh_departament` ADD INDEX `active_thd` (`online_hours_active`,`thd_start_hour`,`thd_end_hour`);
ALTER TABLE `lh_departament` ADD INDEX `active_frd` (`online_hours_active`,`frd_start_hour`,`frd_end_hour`);
ALTER TABLE `lh_departament` ADD INDEX `active_sad` (`online_hours_active`,`sad_start_hour`,`sad_end_hour`);
ALTER TABLE `lh_departament` ADD INDEX `active_sud` (`online_hours_active`,`sud_start_hour`,`sud_end_hour`);
ALTER TABLE `lh_departament` DROP INDEX `oha_sh_eh`;
CREATE TABLE IF NOT EXISTS `lh_departament_custom_work_hours` (`id` int(11) NOT NULL AUTO_INCREMENT,`dep_id` int(11) NOT NULL,`date_from` int(11) NOT NULL,`date_to` int(11) NOT NULL,`start_hour` int(11) NOT NULL,`end_hour` int(11) NOT NULL,PRIMARY KEY (`id`),KEY `dep_id` (`dep_id`),KEY `date_from` (`date_from`),KEY `search_active` (`date_from`, `date_to`, `dep_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `lh_userdep` ADD `type` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_userdep` ADD `dep_group_id` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_userdep` ADD INDEX `user_id_type` (`user_id`, `type`);
ALTER TABLE `lh_userdep` DROP INDEX `user_id`;

CREATE TABLE `lh_departament_group_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dep_group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dep_group_id` (`dep_group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lh_departament_group_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dep_id` int(11) NOT NULL,
  `dep_group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dep_group_id` (`dep_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lh_departament_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;