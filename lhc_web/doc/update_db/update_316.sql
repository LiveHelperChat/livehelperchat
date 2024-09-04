ALTER TABLE `lh_abstract_auto_responder` ADD `disabled` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD INDEX `disabled` (`disabled`);