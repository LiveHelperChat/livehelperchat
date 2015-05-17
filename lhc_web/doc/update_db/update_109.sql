ALTER TABLE `lh_users`
ADD `active_chats_counter` int NOT NULL,
ADD `closed_chats_counter` int NOT NULL AFTER `active_chats_counter`,
ADD `pending_chats_counter` int NOT NULL AFTER `closed_chats_counter`,
COMMENT='';

ALTER TABLE `lh_users`
ADD `departments_ids` varchar(100) NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `active_chats_counter` int(11) NOT NULL,
ADD `pending_chats_counter` int(11) NOT NULL AFTER `active_chats_counter`,
ADD `closed_chats_counter` int(11) NOT NULL AFTER `pending_chats_counter`,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('dashboard_order', 'online_operators,departments_stats|pending_chats,unread_chats|active_chats,closed_chats', '0', 'Home page dashboard widgets order', '0');