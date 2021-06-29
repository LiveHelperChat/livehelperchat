CREATE TABLE `lh_canned_msg_dep` (
                                     `id` bigint(20) NOT NULL AUTO_INCREMENT,
                                     `canned_id` int(11) NOT NULL,
                                     `dep_id` int(11) NOT NULL,
                                     PRIMARY KEY (`id`),
                                     KEY `canned_id` (`canned_id`),
                                     KEY `dep_id` (`dep_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `lh_users`
    CHANGE `username` `username` varchar(80) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `id`,
    COMMENT='';

ALTER TABLE `lh_users` CHANGE `departments_ids` `departments_ids` varchar(500) NOT NULL;