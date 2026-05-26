ALTER TABLE `lh_users_online_session` ADD KEY `user_id_time` (`user_id`, `time`);

CREATE TABLE `lh_abstract_performance` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`type` tinyint(1) unsigned NOT NULL DEFAULT 0,
`created_at` bigint(20) unsigned NOT NULL,
`data` longtext NOT NULL,
PRIMARY KEY (`id`),
KEY `type_id` (`type`,`id` DESC),
KEY `created_at` (`created_at`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES
('statistic_performance',	'a:4:{s:7:\"columns\";a:7:{i:0;s:2:\"cr\";i:1;s:2:\"ca\";i:2;s:2:\"wt\";i:3;s:3:\"frt\";i:4;s:4:\"aart\";i:5;s:3:\"tup\";i:6;s:5:\"tdown\";}s:9:\"positions\";a:7:{s:2:\"cr\";i:1;s:2:\"ca\";i:3;s:2:\"wt\";i:3;s:3:\"frt\";i:4;s:4:\"aart\";i:5;s:3:\"tup\";i:6;s:5:\"tdown\";i:7;}s:15:\"update_interval\";i:300;s:12:\"wrap_headers\";b:0;}',	0,	'ignore',	1),
('statistic_performance_op',	'a:4:{s:7:\"columns\";a:7:{i:0;s:3:\"ton\";i:1;s:4:\"toff\";i:2;s:2:\"ca\";i:3;s:3:\"frt\";i:4;s:4:\"aart\";i:5;s:3:\"tup\";i:6;s:5:\"tdown\";}s:9:\"positions\";a:7:{s:3:\"ton\";i:1;s:4:\"toff\";i:2;s:2:\"ca\";i:3;s:3:\"frt\";i:4;s:4:\"aart\";i:5;s:3:\"tup\";i:6;s:5:\"tdown\";i:7;}s:15:\"update_interval\";i:300;s:12:\"wrap_headers\";b:0;}',	0,	'ignore',	1);