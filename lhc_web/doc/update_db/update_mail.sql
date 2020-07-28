ALTER TABLE `lh_transfer` ADD `transfer_scope` int(11) NOT NULL DEFAULT '0', COMMENT='';

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `lhc_mailconv_conversation`;
CREATE TABLE `lhc_mailconv_conversation` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dep_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `subject` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ctime` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `from_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_address` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_message_id` bigint(20) NOT NULL,
  `message_id` bigint(20) NOT NULL,
  `udate` bigint(20) NOT NULL,
  `date` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mailbox_id` bigint(20) NOT NULL,
  `total_messages` int(11) NOT NULL,
  `match_rule_id` int(11) NOT NULL,
  `cls_time` int(11) NOT NULL,
  `pnd_time` int(11) NOT NULL,
  `wait_time` int(11) NOT NULL,
  `accept_time` int(11) NOT NULL,
  `response_time` int(11) NOT NULL,
  `interaction_time` int(11) NOT NULL,
  `lr_time` int(11) NOT NULL,
  `tslasign` int(11) NOT NULL,
  `start_type` tinyint(1) NOT NULL DEFAULT 0,
  `transfer_uid` int(11) NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `lhc_mailconv_file`;
CREATE TABLE `lhc_mailconv_file` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) NOT NULL,
  `size` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `type` varchar(250) NOT NULL,
  `attachment_id` varchar(250) NOT NULL,
  `file_path` varchar(250) NOT NULL,
  `file_name` varchar(250) NOT NULL,
  `content_id` varchar(250) NOT NULL,
  `disposition` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lhc_mailconv_mailbox`;
CREATE TABLE `lhc_mailconv_mailbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `host` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `port` int(11) NOT NULL,
  `imap` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_sync_log` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_sync_time` bigint(20) NOT NULL,
  `mailbox_sync` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sync_status` int(11) NOT NULL,
  `sync_interval` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lhc_mailconv_match_rule`;
CREATE TABLE `lhc_mailconv_match_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dep_id` int(11) NOT NULL,
  `conditions` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `mailbox_id` varchar(250) NOT NULL,
  `from_name` text NOT NULL,
  `from_mail` text NOT NULL,
  `subject_contains` text NOT NULL,
  `priority` int(11) NOT NULL,
  `priority_rule` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lhc_mailconv_msg`;
CREATE TABLE `lhc_mailconv_msg` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `message_id` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `in_reply_to` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `references` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ctime` int(11) NOT NULL,
  `udate` int(11) NOT NULL,
  `date` text CHARACTER SET utf8 NOT NULL,
  `flagged` int(11) NOT NULL,
  `recent` int(11) NOT NULL,
  `msgno` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `size` int(11) NOT NULL,
  `from_host` varchar(250) CHARACTER SET utf8 NOT NULL,
  `from_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `from_address` varchar(250) CHARACTER SET utf8 NOT NULL,
  `sender_host` varchar(250) CHARACTER SET utf8 NOT NULL,
  `sender_name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `sender_address` varchar(250) CHARACTER SET utf8 NOT NULL,
  `to_data` text CHARACTER SET utf8 NOT NULL,
  `reply_to_data` text CHARACTER SET utf8 NOT NULL,
  `mailbox_id` bigint(20) NOT NULL,
  `response_time` bigint(20) NOT NULL,
  `cls_time` bigint(20) NOT NULL,
  `wait_time` bigint(20) NOT NULL,
  `accept_time` bigint(20) NOT NULL,
  `interaction_time` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `lr_time` bigint(20) NOT NULL,
  `response_type` int(11) NOT NULL,
  `bcc_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cc_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `dep_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`),
  KEY `response_type` (`response_type`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `lhc_mailconv_msg_internal`;
CREATE TABLE `lhc_mailconv_msg_internal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `name_support` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_msg` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_id_id` (`chat_id`,`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `lhc_mailconv_response_template`;
CREATE TABLE `lhc_mailconv_response_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;