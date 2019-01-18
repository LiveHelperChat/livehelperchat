ALTER TABLE `lh_admin_theme` ADD `user_id` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_admin_theme` ADD INDEX `user_id` (`user_id`);