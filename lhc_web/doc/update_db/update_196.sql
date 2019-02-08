INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('footprint_background','0','0','Footprint updates should be processed in the background. Make sure you are running workflow background cronjob.','0');

CREATE TABLE `lh_chat_online_user_footprint_update` (
  `online_user_id` bigint(20) NOT NULL,
  `command` varchar(20) NOT NULL,
  `args` varchar(250) NOT NULL,
  `ctime` int(11) NOT NULL,
  KEY `online_user_id` (`online_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;