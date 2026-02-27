CREATE TABLE `lh_abstract_proactive_chat_invitation_one_time` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invitation_id` bigint(20) unsigned NOT NULL,
  `vid_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invitation_id_vid_id` (`invitation_id`,`vid_id`),
  KEY `vid_id` (`vid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;