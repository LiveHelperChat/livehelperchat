ALTER TABLE `lh_departament` CHANGE `product_configuration` `product_configuration` longtext NOT NULL;
ALTER TABLE `lh_chat_blocked_user` ADD INDEX `nick` (`nick`);