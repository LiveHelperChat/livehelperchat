ALTER TABLE `lh_group_chat_member` ADD `type` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_group_chat_member` ADD INDEX `type` (`type`);
ALTER TABLE `lh_users` ADD INDEX `disabled` (`disabled`);