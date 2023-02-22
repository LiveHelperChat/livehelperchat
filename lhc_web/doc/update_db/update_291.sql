ALTER TABLE `lh_abstract_survey_item` ADD `online_user_id` bigint(20) unsigned NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey_item` ADD INDEX `online_user_id` (`online_user_id`);

ALTER TABLE `lh_abstract_survey` ADD `identifier` varchar(50) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_survey` ADD INDEX `identifier` (`identifier`);