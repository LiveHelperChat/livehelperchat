ALTER TABLE `lh_abstract_email_template` ADD `translations` longtext NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_email_template` ADD `use_chat_locale` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';