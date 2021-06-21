CREATE TABLE `lh_chat_voice_video` (
   `id` bigint(20) NOT NULL AUTO_INCREMENT,
   `chat_id` bigint(20) NOT NULL,
   `user_id` bigint(20) NOT NULL,
   `op_status` tinyint(4) NOT NULL,
   `vi_status` tinyint(4) NOT NULL,
   `voice` tinyint(4) NOT NULL,
   `video` tinyint(4) NOT NULL,
   `screen_share` tinyint(4) NOT NULL,
   `status` tinyint(4) NOT NULL,
   `ctime` int(11) NOT NULL,
   `token` varchar(200) NOT NULL,
   PRIMARY KEY (`id`),
   KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
