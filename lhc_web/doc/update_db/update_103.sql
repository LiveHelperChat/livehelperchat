ALTER TABLE `lh_abstract_widget_theme`
ADD `support_joined` varchar(250) COLLATE 'utf8_general_ci' NOT NULL,
ADD `support_closed` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `support_joined`,
ADD `pending_join` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `support_closed`,
ADD `noonline_operators` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `pending_join`,
ADD `noonline_operators_offline` varchar(250) COLLATE 'utf8_general_ci' NOT NULL AFTER `noonline_operators`,
COMMENT='';