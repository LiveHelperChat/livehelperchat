ALTER TABLE `lh_group` ADD `required` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_group` CHANGE `disabled` `disabled` tinyint(1) NOT NULL;
ALTER TABLE `lh_abstract_widget_theme` ADD `modern_look` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';