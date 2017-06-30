ALTER TABLE `lh_chat` ADD `auto_responder_id` int(11) NOT NULL DEFAULT '0', COMMENT='';

CREATE TABLE `lh_abstract_auto_responder_chat` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `chat_id` int(11) NOT NULL,
                  `auto_responder_id` int(11) NOT NULL,
                  `wait_timeout_send` int(11) NOT NULL,
                  `pending_send_status` int(11) NOT NULL,
                  `active_send_status` int(11) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `chat_id` (`chat_id`)
                ) DEFAULT CHARSET=utf8;

ALTER TABLE `lh_abstract_auto_responder` ADD `wait_timeout_2` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `timeout_message_2` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `wait_timeout_3` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `timeout_message_3` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `wait_timeout_4` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `timeout_message_4` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `wait_timeout_5` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `timeout_message_5` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `wait_timeout_reply_1` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `timeout_reply_message_1` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `wait_timeout_reply_2` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `timeout_reply_message_2` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `wait_timeout_reply_3` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `timeout_reply_message_3` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `wait_timeout_reply_4` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `timeout_reply_message_4` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `wait_timeout_reply_5` text NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `timeout_reply_message_5` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_auto_responder` ADD `ignore_pa_chat` int(11) NOT NULL, COMMENT='';

ALTER TABLE `lh_chat` DROP `wait_timeout`,COMMENT='';
ALTER TABLE `lh_chat` DROP `wait_timeout_send`,COMMENT='';
ALTER TABLE `lh_chat` DROP `timeout_message`,COMMENT='';
ALTER TABLE `lh_chat` DROP `wait_timeout_repeat`,COMMENT='';