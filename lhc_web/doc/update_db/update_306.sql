ALTER TABLE `lh_abstract_subject` ADD `color` varchar(100) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_subject` ADD `pinned` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_subject` ADD `widgets` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_webhook` ADD `name` varchar(50) NOT NULL, COMMENT='';