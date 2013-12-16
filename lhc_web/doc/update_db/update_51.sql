ALTER TABLE `lh_chat_accept`
ADD `wused` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `xmpp_recipients` varchar(250) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_departament`
ADD `xmpp_group_recipients` varchar(250) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_users`
ADD `xmpp_username` varchar(100) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

ALTER TABLE `lh_users`
ADD INDEX `email` (`email`),
ADD INDEX `xmpp_username` (`xmpp_username`);

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('accept_chat_link_timeout',	'300',	0,	'How many seconds accept chat link is valid. Set 0 to force login all the time manually.',	0);
