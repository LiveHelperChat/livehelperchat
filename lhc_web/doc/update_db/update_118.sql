ALTER TABLE `lh_users` ADD `chat_nickname` varchar(100) NOT NULL, COMMENT='';
ALTER TABLE `lh_chat` ADD INDEX `product_id` (`product_id`);
UPDATE `lh_chat_config` SET `hidden` = '1' WHERE `identifier` = 'product_enabled_module';