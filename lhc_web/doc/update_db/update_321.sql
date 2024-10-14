ALTER TABLE `lh_users_online_session` ADD `type` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament_custom_work_hours` ADD `repetitiveness` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament_custom_work_hours` ADD INDEX `repetitiveness` (`repetitiveness`);