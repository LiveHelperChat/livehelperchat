ALTER TABLE `lh_abstract_chat_column` ADD `icon_mode` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_chat_column` ADD `has_popup` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_chat_column` ADD `popup_content` longtext NOT NULL, COMMENT='';