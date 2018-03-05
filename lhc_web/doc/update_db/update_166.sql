ALTER TABLE `lh_group` ADD `required` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_group` CHANGE `disabled` `disabled` tinyint(1) NOT NULL;