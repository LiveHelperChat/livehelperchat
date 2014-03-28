ALTER TABLE `lh_users`
ADD `job_title` varchar(100) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `operator_ids` varchar(100) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';