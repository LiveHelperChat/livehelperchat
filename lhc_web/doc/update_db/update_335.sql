ALTER TABLE `lh_chat_file` ADD `meta_msg` longtext NOT NULL, COMMENT='';
ALTER TABLE `lh_chat_file` ADD `width` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat_file` ADD `height` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lhc_mailconv_file` ADD `meta_msg` longtext NOT NULL, COMMENT='';
ALTER TABLE `lhc_mailconv_file` ADD `width` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lhc_mailconv_file` ADD `height` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';