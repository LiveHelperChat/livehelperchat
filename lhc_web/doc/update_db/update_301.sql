ALTER TABLE `lh_chat_archive_range`
    CHANGE `range_from` `range_from` bigint(20) unsigned NOT NULL,
    CHANGE `range_to` `range_to` bigint(20) unsigned NOT NULL,
    CHANGE `last_id` `last_id` bigint(20) NOT NULL,
    CHANGE `first_id` `first_id` bigint(20) NOT NULL;

CREATE TABLE `lh_mail_archive_range` (
                                         `id` int(11) NOT NULL AUTO_INCREMENT,
                                         `range_from` bigint(20) unsigned NOT NULL,
                                         `range_to` bigint(20) unsigned NOT NULL,
                                         `older_than` int(11) NOT NULL,
                                         `last_id` bigint(20) NOT NULL,
                                         `first_id` bigint(20) NOT NULL,
                                         `year_month` int(11) NOT NULL,
                                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;