ALTER TABLE `lh_webhook` ADD `type` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_webhook` ADD `configuration` longtext NOT NULL, COMMENT='';