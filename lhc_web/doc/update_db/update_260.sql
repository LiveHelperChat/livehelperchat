ALTER TABLE `lh_departament` ADD `alias` varchar(50) NOT NULL, COMMENT='';
ALTER TABLE `lh_departament` ADD INDEX `alias` (`alias`);

ALTER TABLE `lh_abstract_widget_theme` ADD `alias` varchar(50) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_widget_theme` ADD INDEX `alias` (`alias`);