INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('mod_geo_ip_country_code', 'GEOIP_COUNTRY_CODE', '0', '', '1');
INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('mod_geo_ip_country_name', 'GEOIP_COUNTRY_NAME', '0', '', '1');
INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('geo_detection_enabled', '0', '0', '', '1');
INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('geo_service_identifier', '', '0', '', '1');


ALTER TABLE `lh_chat_online_user`
CHANGE `user_location` `user_country_code` varchar(50) COLLATE 'utf8_general_ci' NOT NULL AFTER `user_agent`,
ADD `user_country_name` varchar(50) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';