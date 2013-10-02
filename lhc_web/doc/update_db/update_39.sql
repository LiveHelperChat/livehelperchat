ALTER TABLE `lh_departament`
ADD `department_transfer_id` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `transfer_timeout` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD `transfer_timeout_ts` int(11) NOT NULL,
ADD `transfer_timeout_ac` int(11) NOT NULL AFTER `transfer_timeout_ts`,
ADD `transfer_if_na` int(11) NOT NULL AFTER `transfer_timeout_ac`,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('run_departments_workflow', 0, 0, 'Should cronjob run departments tranfer workflow, even if user leaves a chat, 0 - no, 1 - yes',	0);
INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('run_unaswered_chat_workflow', 0, 0, 'Should cronjob run unanswered chats workflow and execute unaswered chats callback, 0 - no, any other number bigger than 0 is a minits how long chat have to be not accepted before executing callback.',0);

ALTER TABLE `lh_chat`
ADD `na_cb_executed` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `identifier` varchar(50) NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD INDEX `identifier` (`identifier`);