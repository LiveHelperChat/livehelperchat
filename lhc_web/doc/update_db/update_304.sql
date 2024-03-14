ALTER TABLE `lhc_mailconv_delete_filter` ADD `delete_policy` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_webhook` ADD `status` longtext NOT NULL, COMMENT='';