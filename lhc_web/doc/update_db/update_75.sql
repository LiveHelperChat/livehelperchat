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

INSERT INTO `lh_abstract_email_template` (`id`, `name`, `from_name`, `from_name_ac`, `from_email`, `from_email_ac`, `content`, `subject`, `subject_ac`, `reply_to`, `reply_to_ac`, `recipient`, `bcc_recipients`) VALUES
(7,	'New unread message',	'Live Helper Chat',	0,	'',	0,	'Hello,\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nClick to accept chat automatically\r\n{url_accept}\r\n\r\nSincerely,\r\nLive Support Team',	'New unread message',	0,	'',	0,	'',	'');

ALTER TABLE `lh_chat_online_user`
ADD `requires_username` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `requires_username` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `online_attr` varchar(250) NOT NULL,
COMMENT='';