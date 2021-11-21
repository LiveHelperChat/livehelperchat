ALTER TABLE `lh_canned_msg` ADD `updated_at` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD `created_at` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD `active_from` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD `active_to` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD `repetitiveness` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg` ADD `days_activity` text NOT NULL, COMMENT='';