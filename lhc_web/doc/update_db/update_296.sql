ALTER TABLE `lh_incoming_webhook` ADD `log_incoming` tinyint(1) unsigned NOT NULL, COMMENT='';
ALTER TABLE `lh_incoming_webhook` ADD `log_failed_parse` tinyint(1) unsigned NOT NULL, COMMENT='';
ALTER TABLE `lh_chat_incoming` ADD UNIQUE `incoming_ext_id_uniq` (`incoming_id`, `chat_external_id`), DROP INDEX `incoming_ext_id`;