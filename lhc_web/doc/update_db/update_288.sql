ALTER TABLE `lh_chat` ADD `theme_id` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat` ADD INDEX `theme_id` (`theme_id`);