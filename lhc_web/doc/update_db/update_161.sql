ALTER TABLE `lh_userdep` ADD `ro` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat_online_user_footprint` ADD INDEX `chat_id` (`chat_id`);
ALTER TABLE `lh_chat_online_user_footprint` DROP INDEX `chat_id_vtime`;