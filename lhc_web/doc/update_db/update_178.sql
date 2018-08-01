CREATE TABLE `lh_abstract_proactive_chat_campaign` ( `id` bigint(20) NOT NULL AUTO_INCREMENT, `name` varchar(50) NOT NULL, `text` text NOT NULL, PRIMARY KEY (`id`)) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lh_abstract_proactive_chat_campaign_conv` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `device_type` tinyint(11) NOT NULL,
  `invitation_type` tinyint(1) NOT NULL,
  `invitation_status` tinyint(1) NOT NULL,
  `chat_id` bigint(20) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `invitation_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `con_time` int(11) NOT NULL,
  `vid_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `invitation_id` (`invitation_id`),
  KEY `invitation_status` (`invitation_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `lh_chat_online_user` ADD `conversion_id` int(11) NOT NULL, COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `campaign_id` int(11) NOT NULL, COMMENT='';