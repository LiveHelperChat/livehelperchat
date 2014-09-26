DROP TABLE IF EXISTS `lh_chat_config`;
CREATE TABLE `lh_chat_config` (
  `identifier` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `explain` varchar(250) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`identifier`)
);

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES
('tracked_users_cleanup',	'7',	0,	'How many days keep records of online users.',	0),
('track_online_visitors',	'0',	0,	'Enable online site visitors tracking, 0 - no, 1 - yes',	0);

DROP TABLE IF EXISTS `lh_chat_online_user`;
CREATE TABLE `lh_chat_online_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vid` varchar(50) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `current_page` varchar(250) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `last_visit` int(11) NOT NULL,
  `user_agent` varchar(250) NOT NULL,
  `user_location` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `last_visit` (`last_visit`),
  KEY `vid` (`vid`)
);