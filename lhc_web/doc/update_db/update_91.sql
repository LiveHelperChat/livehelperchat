ALTER TABLE `lh_abstract_widget_theme`
ADD `intro_operator_text` varchar(250) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_widget_theme`
ADD `operator_image` varchar(250) COLLATE 'utf8_general_ci' NOT NULL,
ADD `operator_image_path` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `operator_image`,
COMMENT='';