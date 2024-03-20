CREATE TABLE `lh_mail_continuous_event` (
    `webhook_id` bigint(20) unsigned NOT NULL,
    `message_id` bigint(20) unsigned NOT NULL,
    `created_at` bigint(20) unsigned NOT NULL,
    UNIQUE KEY `webhook_id_message_id` (`webhook_id`,`message_id`),
    KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;