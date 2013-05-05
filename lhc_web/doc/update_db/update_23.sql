ALTER TABLE `lh_abstract_email_template`
ADD `recipient` varchar(150) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `email` varchar(100) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

INSERT INTO `lh_abstract_email_template` (`id`, `name`, `from_name`, `from_name_ac`, `from_email`, `from_email_ac`, `content`, `subject`, `subject_ac`, `reply_to`, `reply_to_ac`, `recipient`) VALUES
(2,	'Support request from user',	'',	0,	'',	0,	'Hello,\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nAdditional data, if any:\r\n{additional_data}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nSincerely,\r\nLive Support Team',	'Support request from user',	0,	'',	0,	'');