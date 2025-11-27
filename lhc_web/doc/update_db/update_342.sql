ALTER TABLE `lh_abstract_msg_protection` ADD `rule_type` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_msg_protection` ADD `has_dep` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_msg_protection` ADD `name` varchar(100) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_msg_protection` ADD `dep_ids` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_msg_protection` DROP INDEX `enabled`;
ALTER TABLE `lh_abstract_msg_protection` ADD INDEX `enabled_type` (`enabled`,`rule_type`);
UPDATE `lh_chat_config` SET value = '342' WHERE `identifier` = 'version_updates' LIMIT 1;
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('guardrails_enabled','0','0','Enable guardrails for operators and visitors','1');