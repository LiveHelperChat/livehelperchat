ALTER TABLE `lh_users`
ADD `job_title` varchar(100) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `operator_ids` varchar(100) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_users`
ADD `invisible_mode` tinyint(1) NOT NULL,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('hide_disabled_department','1',0,'Hide disabled department widget', '0');