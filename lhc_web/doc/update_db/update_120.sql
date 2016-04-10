CREATE TABLE `lh_admin_theme` (
       `id` int(11) NOT NULL AUTO_INCREMENT,
       `name` varchar(100) NOT NULL,
       `static_content` longtext NOT NULL,
       `static_js_content` longtext NOT NULL,
       `static_css_content` longtext NOT NULL,
       `header_content` text NOT NULL,
       `header_css` text NOT NULL,
       PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('default_admin_theme_id',	'0',	0,	'Default admin theme',	1);