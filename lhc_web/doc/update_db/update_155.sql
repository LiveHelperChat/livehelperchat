ALTER TABLE `lh_canned_msg` ADD `languages` text NOT NULL, COMMENT='';
ALTER TABLE `lh_speech_language_dialect` ADD `short_code` varchar(4) NOT NULL, COMMENT='';
ALTER TABLE `lh_speech_language_dialect` ADD INDEX `short_code` (`short_code`);
ALTER TABLE `lh_speech_language_dialect` ADD INDEX `lang_code` (`lang_code`);
ALTER TABLE `lh_abstract_auto_responder` ADD `languages` text NOT NULL, COMMENT='';