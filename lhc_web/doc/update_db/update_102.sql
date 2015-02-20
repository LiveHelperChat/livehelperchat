ALTER TABLE `lh_group`
ADD `disabled` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_group`
ADD INDEX `disabled` (`disabled`);