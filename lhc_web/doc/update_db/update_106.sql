ALTER TABLE `lh_departament`
ADD `attr_int_1` int(11) NOT NULL DEFAULT '0',
ADD `attr_int_2` int(11) NOT NULL DEFAULT '0' AFTER `attr_int_1`,
ADD `attr_int_3` int(11) NOT NULL DEFAULT '0' AFTER `attr_int_2`,
COMMENT='';

ALTER TABLE `lh_departament`
ADD INDEX `attr_int_1` (`attr_int_1`),
ADD INDEX `attr_int_2` (`attr_int_2`),
ADD INDEX `attr_int_3` (`attr_int_3`);