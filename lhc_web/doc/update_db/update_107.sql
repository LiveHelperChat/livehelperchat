ALTER TABLE `lh_canned_msg`
ADD `attr_int_1` int NOT NULL,
ADD `attr_int_2` int NOT NULL AFTER `attr_int_1`,
ADD `attr_int_3` int NOT NULL AFTER `attr_int_2`,
COMMENT='';

ALTER TABLE `lh_canned_msg`
ADD INDEX `attr_int_1` (`attr_int_1`),
ADD INDEX `attr_int_2` (`attr_int_2`),
ADD INDEX `attr_int_3` (`attr_int_3`);

ALTER TABLE `lh_canned_msg`
ADD `title` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `id`,
ADD `explain` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `title`,
ADD `fallback_msg` text COLLATE 'utf8_general_ci' NOT NULL AFTER `msg`,
COMMENT='';