ALTER TABLE `lh_departament` ADD `dep_offline` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament` ADD INDEX `dep_offline` (`dep_offline`);