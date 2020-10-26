ALTER TABLE `lh_users_session` ADD `notifications_status` int(11) NOT NULL DEFAULT '1', COMMENT='';
ALTER TABLE `lh_users_session` ADD `error` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_users_session` ADD `last_error` varchar(255) NOT NULL, COMMENT='';
ALTER TABLE `lh_users_session` ADD INDEX `error` (`error`);
INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES
('mobile_options',	'a:2:{s:13:\"notifications\";i:0;s:7:\"fcm_key\";s:152:\"AAAAiF8DeNk:APA91bFVHu2ybhBUTtlEtQrUEPpM2fb-5ovgo0FVNm4XxK3cYJtSwRcd-pqcBot_422yDOzHyw2p9ZFplkHrmNXjm8f5f-OIzfalGmpsypeXvnPxhU6Db1B2Z1Acc-TamHUn2F4xBJkP\";}',	0,	'',	1);