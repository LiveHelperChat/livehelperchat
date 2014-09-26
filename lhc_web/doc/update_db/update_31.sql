ALTER TABLE `lh_departament`
ADD `priority` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD `priority` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD INDEX `status_dep_id_priority_id` (`status`, `dep_id`, `priority`, `id`);

ALTER TABLE `lh_chat`
ADD INDEX `status_priority_id` (`status`, `priority`, `id`);