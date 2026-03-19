ALTER TABLE `lh_abstract_subject` ADD `archive` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_subject` ADD INDEX `archive` (`archive`);