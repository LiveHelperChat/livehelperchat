CREATE TABLE `lh_abstract_proactive_chat_invitation_dep` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `invitation_id` int(11) NOT NULL,
    `dep_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `invitation_id` (`invitation_id`),
    KEY `dep_id` (`dep_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;