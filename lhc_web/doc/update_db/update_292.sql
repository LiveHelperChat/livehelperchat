ALTER TABLE `lh_canned_msg` ADD `delete_on_exp` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD INDEX `delete_on_exp` (`delete_on_exp`);