CREATE TABLE `lh_generic_bot_pending_event` ( `id` bigint(20) NOT NULL AUTO_INCREMENT, `chat_id` bigint(20) NOT NULL, `trigger_id` int(11) NOT NULL, PRIMARY KEY (`id`), KEY `chat_id` (`chat_id`)) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `lh_generic_bot_exception` ( `id` bigint(20) NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL, `priority` int(11) NOT NULL, `active` tinyint(1) NOT NULL, PRIMARY KEY (`id`)) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `lh_generic_bot_exception_message` ( `id` bigint(20) NOT NULL AUTO_INCREMENT, `code` varchar(20) NOT NULL, `exception_group_id` int(11) NOT NULL, `priority` int(11) NOT NULL, `active` tinyint(1) NOT NULL, `message` text NOT NULL, PRIMARY KEY (`id`), KEY `code` (`code`), KEY `exception_group_id` (`exception_group_id`)) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `lh_generic_bot_bot` ADD `filepath` varchar(250) NOT NULL, COMMENT='';
ALTER TABLE `lh_generic_bot_bot` ADD `filename` varchar(250) NOT NULL, COMMENT='';
ALTER TABLE `lh_generic_bot_bot` ADD `configuration` longtext NOT NULL, COMMENT='';