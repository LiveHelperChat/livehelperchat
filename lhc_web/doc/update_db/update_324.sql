ALTER TABLE `lh_audits` ADD `user_id` bigint(20) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_audits` ADD INDEX `user_id` (`user_id`);