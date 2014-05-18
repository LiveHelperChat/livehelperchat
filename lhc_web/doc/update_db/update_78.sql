
INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('disable_html5_storage','0',0,'Disable HMTL5 storage, check it if your site is switching between http and https', '0');
INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('automatically_reopen_chat','0',0,'Automatically reopen chat on widget open', '0');
ALTER TABLE `lh_abstract_browse_offer_invitation`
ADD `callback_content` text COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';
INSERT INTO `lh_abstract_email_template` (`id`, `name`, `from_name`, `from_name_ac`, `from_email`, `from_email_ac`, `content`, `subject`, `subject_ac`, `reply_to`, `reply_to_ac`, `recipient`, `bcc_recipients`) VALUES
(9,	'Chat was accepted',	'Live Helper Chat',	0,	'',	0,	'Hello,\r\n\r\nOperator {user_name} has accepted a chat [{chat_id}]\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nClick to accept chat automatically\r\n{url_accept}\r\n\r\nSincerely,\r\nLive Support Team',	'Chat was accepted [{chat_id}]',	0,	'',	0,	'',	'');