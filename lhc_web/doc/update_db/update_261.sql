ALTER TABLE `lh_canned_msg` ADD `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD INDEX `disabled` (`disabled`);