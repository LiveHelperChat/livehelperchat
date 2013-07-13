ALTER TABLE `lh_chat_online_user`
ADD `time_on_site` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `tt_time_on_site` int(11) NOT NULL,
COMMENT='';

CREATE TABLE `lh_abstract_proactive_chat_invitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteaccess` varchar(10) NOT NULL,
  `time_on_site` int(11) NOT NULL,
  `pageviews` int(11) NOT NULL,
  `message` text NOT NULL,
  `executed_times` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time_on_site_pageviews_siteaccess_position` (`time_on_site`,`pageviews`,`siteaccess`,`position`)
) DEFAULT CHARSET=utf8;