ALTER TABLE `lh_chat` ADD `frt` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat` ADD `aart` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat` ADD `mart` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';

ALTER TABLE `lh_chat_participant` ADD `frt` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat_participant` ADD `aart` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_chat_participant` ADD `mart` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';