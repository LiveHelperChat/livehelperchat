ALTER TABLE `lh_chat_online_user`
ADD `dep_id` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD INDEX `dep_id` (`dep_id`);

ALTER TABLE `lh_chat_online_user`
ADD INDEX `last_visit_dep_id` (`last_visit`, `dep_id`),
DROP INDEX `last_visit`;