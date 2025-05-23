ALTER TABLE `lh_departament` ADD `max_load_op` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament` ADD `max_load_op_h` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament_group` ADD `max_load_op` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament_group` ADD `max_load_op_h` int(11) unsigned NOT NULL DEFAULT '0', COMMENT='';
UPDATE `lh_chat_config` SET value = '332' WHERE `identifier` = 'version_updates' LIMIT 1;