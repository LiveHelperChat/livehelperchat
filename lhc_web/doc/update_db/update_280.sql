ALTER TABLE `lh_generic_bot_tr_group` CHANGE `name` `name` varchar(100) NOT NULL;
ALTER TABLE `lh_generic_bot_tr_group` ADD `use_translation_service` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_generic_bot_tr_group` ADD `bot_lang` varchar(10) NOT NULL, COMMENT='';
ALTER TABLE `lh_generic_bot_tr_item` ADD `auto_translate` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';