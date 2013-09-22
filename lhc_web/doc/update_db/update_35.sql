ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `wait_message` varchar(250) NOT NULL,
ADD `wait_timeout` int NOT NULL AFTER `wait_message`,
ADD `timeout_message` varchar(250) NOT NULL AFTER `wait_timeout`,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `invitation_id` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat`
ADD `wait_timeout` int(11) NOT NULL,
ADD `wait_timeout_send` int(11) NOT NULL AFTER `wait_timeout`,
ADD `timeout_message` varchar(250) NOT NULL AFTER `wait_timeout_send`,
COMMENT='';

CREATE TABLE `lh_abstract_auto_responder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteaccess` varchar(3) NOT NULL,
  `wait_message` varchar(250) NOT NULL,
  `wait_timeout` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `timeout_message` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `siteaccess_position` (`siteaccess`,`position`)
);