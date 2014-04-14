ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `dep_id` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD INDEX `identifier` (`identifier`),
ADD INDEX `dep_id` (`dep_id`);

ALTER TABLE `lh_chat`
ADD `unread_messages_informed` int NOT NULL,
ADD `reinform_timeout` int NOT NULL AFTER `unread_messages_informed`,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `inform_unread` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `inform_unread_delay` int(11) NOT NULL,
COMMENT='';