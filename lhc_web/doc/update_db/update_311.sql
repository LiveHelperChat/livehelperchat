ALTER TABLE `lh_departament` ADD `ignore_op_status` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_departament` ADD INDEX `ignore_op_status` (`ignore_op_status`);