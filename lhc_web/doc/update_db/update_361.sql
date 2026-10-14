ALTER TABLE `lh_abstract_offline_reason` ADD `mode` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_offline_reason` ADD `disable_auto_assign` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_offline_reason` ADD `disable_transfer` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_users_online_session` ADD `online_reason_id` int(11) unsigned NOT NULL DEFAULT 0, COMMENT='';
