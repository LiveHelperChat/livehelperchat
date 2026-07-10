ALTER TABLE `lh_abstract_form_collected` ADD `user_id` bigint(20) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_form_collected` ADD `attr_int_1` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_form_collected` ADD `attr_int_2` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_form_collected` ADD `attr_int_3` int(11) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_form` ADD `form_type` tinyint(1) NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `lh_abstract_form_collected` DROP INDEX `form_id`;
ALTER TABLE `lh_abstract_form_collected` ADD INDEX `form_id_chat_id` (`form_id`, `chat_id`);
ALTER TABLE `lh_abstract_form` CHANGE `intro_attr` `intro_attr` varchar(400) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `name_attr`;
ALTER TABLE `lh_abstract_form_collected` ADD INDEX `chat_id` (`chat_id`);