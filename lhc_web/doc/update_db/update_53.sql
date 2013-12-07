ALTER TABLE `lh_departament`
ADD `disabled` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD INDEX `disabled` (`disabled`);