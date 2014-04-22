CREATE TABLE `lh_abstract_browse_offer_invitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteaccess` varchar(10) NOT NULL,
  `time_on_site` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `lhc_iframe_content` tinyint(4) NOT NULL,
  `custom_iframe_url` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `identifier` varchar(50) NOT NULL,
  `executed_times` int(11) NOT NULL,
  `url` varchar(250) NOT NULL,
  `active` int(11) NOT NULL,
  `has_url` int(11) NOT NULL,
  `is_wildcard` int(11) NOT NULL,
  `referrer` varchar(250) NOT NULL,
  `priority` varchar(250) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `identifier` (`identifier`)
);

ALTER TABLE `lh_departament`
ADD `hidden` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD INDEX `disabled_hidden` (`disabled`, `hidden`),
DROP INDEX `disabled`;