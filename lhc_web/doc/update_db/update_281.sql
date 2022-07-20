ALTER TABLE `lh_canned_msg_replace` ADD `active_from` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg_replace` ADD `active_to` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg_replace` ADD `repetitiveness` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_canned_msg_replace` ADD `days_activity` text NOT NULL, COMMENT='';
ALTER TABLE `lh_canned_msg_replace` ADD `time_zone` varchar(100) NOT NULL, COMMENT='';