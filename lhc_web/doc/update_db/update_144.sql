ALTER TABLE `lh_chat` ADD `transfer_uid` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_transfer` ADD `ctime` int(11) NOT NULL, COMMENT='';
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('transfer_configuration','0','0','Transfer configuration','1');