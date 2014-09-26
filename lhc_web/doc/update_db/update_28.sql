ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `identifier` varchar(50) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `identifier` varchar(50) NOT NULL,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('pro_active_invite',	'0',	0,	'Is pro active chat invitation active. Online users tracking also has to be enabled',	0);