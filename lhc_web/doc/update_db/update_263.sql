ALTER TABLE `lh_abstract_subject` ADD `internal` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_subject` ADD `internal_type` varchar(20) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_subject` ADD INDEX `internal` (`internal`);
ALTER TABLE `lh_abstract_subject` ADD INDEX `internal_type` (`internal_type`);