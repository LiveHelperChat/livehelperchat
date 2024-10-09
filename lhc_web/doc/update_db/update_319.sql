ALTER TABLE `lh_departament` CHANGE `dep_offline` `dep_offline` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `lh_departament` ADD INDEX `dep_offline` (`dep_offline`);