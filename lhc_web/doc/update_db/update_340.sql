ALTER TABLE `lh_userdep` ADD `only_priority` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
UPDATE `lh_chat_config` SET value = '340' WHERE `identifier` = 'version_updates' LIMIT 1;
ALTER TABLE `lh_departament_group_user` ADD `only_priority` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';