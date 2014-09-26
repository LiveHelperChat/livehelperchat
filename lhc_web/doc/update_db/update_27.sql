ALTER TABLE `lh_chat`
ADD `lat` varchar(10) NOT NULL,
ADD `lon` varchar(10) NOT NULL AFTER `lat`,
ADD `city` varchar(100) NOT NULL AFTER `lon`,
COMMENT='';

CREATE TABLE `lh_chat_online_user_footprint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL,
  `online_user_id` int(11) NOT NULL,
  `page` varchar(250) NOT NULL,
  `vtime` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_id_vtime` (`chat_id`,`vtime`),
  KEY `online_user_id` (`online_user_id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('track_footprint',	'0',	0,	'Track users footprint. For this also online visitors tracking should be enabled',	0);