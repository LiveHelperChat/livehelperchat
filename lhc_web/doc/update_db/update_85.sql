ALTER TABLE `lh_chat`
ADD `user_tz_identifier` varchar(50) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `visitor_tz` varchar(50) NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_widget_theme`
ADD `bor_bcolor` varchar(10) COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'e3e3e3' AFTER `onl_bcolor`,
COMMENT='';