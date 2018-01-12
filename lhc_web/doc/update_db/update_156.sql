ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `delay_init` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `delay` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_proactive_chat_invitation` ADD `show_instant` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_chat_online_user` DROP `show_on_mobile`;