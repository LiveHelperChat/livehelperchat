ALTER TABLE `lh_chat`
ADD `additional_data` varchar(250) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD `mail_send` int NOT NULL,
COMMENT='';

CREATE TABLE `lh_abstract_email_template` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(250) NOT NULL,
  `from_name` varchar(150) NOT NULL,
  `from_email` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `subject` varchar(250) NOT NULL
) COMMENT='';

INSERT INTO `lh_abstract_email_template` (`id`, `name`, `from_name`, `from_email`, `content`, `subject`)
VALUES ('1','Send mail to user', '{name_surname} has responded to your request', '', 'Dear {user_chat_nick},\r\n\r\n{additional_message}\r\n\r\nLive Support response:\r\n{messages_content}\r\n\r\nSincerely,\r\nLive Support Team\r\n', 'Live Support');