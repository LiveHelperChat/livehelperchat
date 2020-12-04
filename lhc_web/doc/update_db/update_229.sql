ALTER TABLE `lh_departament` DROP INDEX `identifier`;
ALTER TABLE `lh_departament` CHANGE `identifier` `identifier` varchar(2083) NOT NULL;
ALTER TABLE `lh_departament` ADD INDEX `identifier_2` (`identifier`(191));