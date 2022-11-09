ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `parent_id` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD INDEX `parent_id` (`parent_id`);

ALTER TABLE `lh_abstract_proactive_chat_campaign_conv` ADD `variation_id` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_campaign_conv` DROP INDEX `invitation_id`;
ALTER TABLE `lh_abstract_proactive_chat_campaign_conv` ADD INDEX `invitation_id_variation_id` (`invitation_id`, `variation_id`);