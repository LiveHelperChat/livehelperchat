CREATE TABLE `lh_abstract_product` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(250) NOT NULL, `disabled` int(11) NOT NULL, `priority` int(11) NOT NULL, `departament_id` int(11) NOT NULL, KEY `departament_id` (`departament_id`), PRIMARY KEY (`id`)) DEFAULT CHARSET=utf8;
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('product_enabled_module','0','0','Product module is enabled','1');
ALTER TABLE `lh_chat` ADD `product_id` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_users` CHANGE `password` `password` varchar(200) NOT NULL;
CREATE TABLE `lh_chat_paid` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `hash` varchar(250) NOT NULL, `chat_id` int(11) NOT NULL,  PRIMARY KEY (`id`),  KEY `hash` (`hash`), KEY `chat_id` (`chat_id`)) DEFAULT CHARSET=utf8;
INSERT INTO `lh_chat_config` (`identifier`,`value`,`type`,`explain`,`hidden`) VALUES ('paidchat_data','','0','Paid chat configuration','1');