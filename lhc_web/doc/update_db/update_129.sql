ALTER TABLE `lh_departament` ADD `inform_close_all` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_departament` ADD `inform_close_all_email` varchar(250) NOT NULL, COMMENT='';
INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('product_show_departament',	'0',	0,	'Enable products show by departments',	1);
ALTER TABLE `lh_departament` ADD `product_configuration` varchar(250) NOT NULL, COMMENT='';
