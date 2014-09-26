ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `requires_email` int NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `requires_email` int(11) NOT NULL,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('disable_popup_restore', 0, 0, 'Disable option in widget to open new window. 0 - no, 1 - restore icon will be hidden',	0);
