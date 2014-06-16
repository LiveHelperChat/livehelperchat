INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('autoclose_timeout','0', 0, 'Automatic chats closing. 0 - disabled, n > 0 time in minutes before chat is automatically closed', '0');
INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('autopurge_timeout','0', 0, 'Automatic chats purging. 0 - disabled, n > 0 time in minutes before chat is automatically deleted', '0');

ALTER TABLE `lh_userdep`
ADD `last_accepted` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `active_balancing` tinyint(1) NOT NULL,
ADD `max_active_chats` int NOT NULL AFTER `active_balancing`,
ADD `max_timeout_seconds` int NOT NULL AFTER `max_active_chats`,
COMMENT='';

ALTER TABLE `lh_chat`
ADD `tslasign` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_userdep`
ADD `active_chats` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD INDEX `status_user_id` (`status`, `user_id`),
DROP INDEX `status`;

ALTER TABLE `lh_canned_msg`
ADD `auto_send` tinyint(1) NOT NULL,
COMMENT='';