CREATE TABLE `lh_users_session` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `token` varchar(40) NOT NULL,
                  `device_type` int(11) NOT NULL,
                  `device_token` varchar(255) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  `created_on` int(11) NOT NULL,
                  `updated_on` int(11) NOT NULL,
                  `expires_on` int(11) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `device_token_device_type` (`device_token`,`device_type`),
                  KEY `token` (`token`)
                ) DEFAULT CHARSET=utf8;

ALTER TABLE `lh_msg` ADD INDEX `user_id` (`user_id`);