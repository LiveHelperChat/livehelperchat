CREATE TABLE `lh_chat_start_settings` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(50) NOT NULL, `data` longtext NOT NULL, `department_id` int(11) NOT NULL, PRIMARY KEY (`id`), KEY `department_id` (`department_id`)) DEFAULT CHARSET=utf8;

ALTER TABLE `lh_departament` ADD INDEX `active_chats_counter` (`active_chats_counter`);
ALTER TABLE `lh_departament` ADD INDEX `pending_chats_counter` (`pending_chats_counter`);
ALTER TABLE `lh_departament` ADD INDEX `closed_chats_counter` (`closed_chats_counter`);
