ALTER TABLE `lh_canned_msg` ADD `additional_data` text NOT NULL, COMMENT='';
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('do_no_track_ip','0','0','Do not track visitors IP','0');
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('encrypt_msg_after','0','0','After how many days anonymize messages','0');
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('encrypt_msg_op','0','0','Anonymize also operators messages','0');
ALTER TABLE `lh_chat` ADD `anonymized` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat` ADD INDEX `anonymized` (`anonymized`);
