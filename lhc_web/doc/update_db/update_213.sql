ALTER TABLE `lh_chat_online_user`
ADD `device_type` tinyint(1) NOT NULL DEFAULT '0',
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD INDEX `show_on_mobile` (`show_on_mobile`);