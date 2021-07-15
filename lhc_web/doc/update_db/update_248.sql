CREATE TABLE `lh_canned_msg_subject` (
                                         `id` int(11) NOT NULL AUTO_INCREMENT,
                                         `canned_id` int(11) NOT NULL,
                                         `subject_id` int(11) NOT NULL,
                                         PRIMARY KEY (`id`),
                                         KEY `canned_id` (`canned_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;