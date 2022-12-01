ALTER TABLE `lh_chat` ADD `iwh_id` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat` ADD INDEX `iwh_id` (`iwh_id`);
ALTER TABLE `lh_incoming_webhook` ADD `icon` varchar(50) NOT NULL, COMMENT='';
ALTER TABLE `lh_incoming_webhook` ADD `icon_color` varchar(50) NOT NULL, COMMENT='';