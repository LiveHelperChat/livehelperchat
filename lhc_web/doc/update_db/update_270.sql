ALTER TABLE `lh_abstract_saved_search` ADD `description` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_saved_search` ADD `status` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_saved_search` ADD `sharer_user_id` bigint(20) unsigned NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_saved_search` DROP INDEX `user_id`;
ALTER TABLE `lh_abstract_saved_search` ADD INDEX `user_id_status` (`user_id`,`status`);