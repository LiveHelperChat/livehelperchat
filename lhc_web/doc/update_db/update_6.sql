INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('geo_data', '', '0', '', '1');

ALTER TABLE `lh_chat_online_user`
CHANGE `user_location` `user_country_code` varchar(50) COLLATE 'utf8_general_ci' NOT NULL AFTER `user_agent`,
ADD `user_country_name` varchar(50) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD `country_code` varchar(100) COLLATE 'utf8_general_ci' NOT NULL,
ADD `country_name` varchar(100) COLLATE 'utf8_general_ci' NOT NULL AFTER `country_code`,
COMMENT='';