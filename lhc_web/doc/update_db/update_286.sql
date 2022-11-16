ALTER TABLE `lh_abstract_proactive_chat_campaign_conv` ADD `conv_int_expires` bigint(20) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_campaign_conv` ADD `conv_int_time` bigint(20) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_campaign_conv` ADD `conv_event` varchar(20) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_campaign_conv` ADD INDEX `conv_event_vid_id` (`conv_event`, `vid_id`);
ALTER TABLE `lh_abstract_proactive_chat_campaign_conv` CHANGE `variation_id` `variation_id` bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `lh_abstract_proactive_chat_campaign_conv` ADD `unique_id` varchar(20) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_campaign_conv` ADD INDEX `unique_id` (`unique_id`);
ALTER TABLE `lh_abstract_proactive_chat_campaign_conv` ADD INDEX `conv_int_time` (`conv_int_time`);