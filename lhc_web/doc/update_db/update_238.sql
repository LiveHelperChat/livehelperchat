ALTER TABLE `lh_canned_msg` ADD `unique_id` varchar(20) NOT NULL, COMMENT='';
UPDATE `lh_canned_msg` SET `unique_id` = `id`;
ALTER TABLE `lh_canned_msg` ADD INDEX `unique_id` (`unique_id`);