ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `dep_id` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD INDEX `identifier` (`identifier`),
ADD INDEX `dep_id` (`dep_id`);