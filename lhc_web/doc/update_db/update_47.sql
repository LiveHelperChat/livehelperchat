ALTER TABLE `lh_chat`
ADD `nc_cb_executed` tinyint(1) NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `mod` tinyint(1) NOT NULL,
ADD `tud` tinyint(1) NOT NULL AFTER `mod`,
ADD `wed` tinyint(1) NOT NULL AFTER `tud`,
ADD `thd` tinyint(1) NOT NULL AFTER `wed`,
ADD `frd` tinyint(1) NOT NULL AFTER `thd`,
ADD `sad` tinyint(1) NOT NULL AFTER `frd`,
ADD `sud` tinyint(1) NOT NULL AFTER `sad`,
ADD `start_hour` int(2) NOT NULL AFTER `sud`,
ADD `end_hour` int(2) NOT NULL AFTER `start_hour`,
ADD `inform_options` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `end_hour`,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `online_hours_active` tinyint(1) NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `inform_delay` int NOT NULL,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES
('xmp_data',	'a:9:{i:0;b:0;s:4:\"host\";s:15:\"talk.google.com\";s:6:\"server\";s:9:\"gmail.com\";s:8:\"resource\";s:6:\"xmpphp\";s:4:\"port\";s:4:\"5222\";s:7:\"use_xmp\";i:0;s:8:\"username\";s:0:\"\";s:8:\"password\";s:0:\"\";s:11:\"xmp_message\";s:77:\"You have a new chat request\r\n{messages}\r\nClick to accept a chat\r\n{url_accept}\";}',	0,	'XMP data',	1);

INSERT INTO `lh_abstract_email_template` (`id`, `name`, `from_name`, `from_name_ac`, `from_email`, `from_email_ac`, `content`, `subject`, `subject_ac`, `reply_to`, `reply_to_ac`, `recipient`) VALUES
(4,	'New chat request',	'Live Helper Chat',	0,	'',	0,	'Hello,\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nClick to accept chat automatically\r\n{url_accept}\r\n\r\nSincerely,\r\nLive Support Team',	'New chat request',	0,	'',	0,	'');

ALTER TABLE `lh_departament`
ADD INDEX `oha_sh_eh` (`online_hours_active`, `start_hour`, `end_hour`);

CREATE TABLE `lh_chat_accept` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL,
  `hash` varchar(50) NOT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`)
);