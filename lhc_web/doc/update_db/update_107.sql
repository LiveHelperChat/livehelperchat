ALTER TABLE `lh_canned_msg`
ADD `attr_int_1` int NOT NULL,
ADD `attr_int_2` int NOT NULL AFTER `attr_int_1`,
ADD `attr_int_3` int NOT NULL AFTER `attr_int_2`,
COMMENT='';

ALTER TABLE `lh_canned_msg`
ADD INDEX `attr_int_1` (`attr_int_1`),
ADD INDEX `attr_int_2` (`attr_int_2`),
ADD INDEX `attr_int_3` (`attr_int_3`);