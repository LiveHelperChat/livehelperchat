ALTER TABLE `lh_abstract_widget_theme`
ADD `minimize_image` varchar(250) COLLATE 'utf8_general_ci' NOT NULL,
ADD `minimize_image_path` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `minimize_image`,
ADD `restore_image` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `minimize_image_path`,
ADD `restore_image_path` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `restore_image`,
ADD `close_image` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `restore_image_path`,
ADD `close_image_path` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `close_image`,
ADD `popup_image` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `close_image_path`,
ADD `popup_image_path` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `popup_image`,
ADD `hide_close` int NOT NULL AFTER `popup_image_path`,
ADD `hide_popup` int NOT NULL AFTER `hide_close`,
COMMENT='';

ALTER TABLE `lh_abstract_widget_theme`
ADD `header_height` int(11) NOT NULL,
ADD `header_padding` int(11) NOT NULL AFTER `header_height`,
COMMENT='';

ALTER TABLE `lh_abstract_widget_theme`
ADD `widget_border_width` int(11) NOT NULL,
COMMENT='';