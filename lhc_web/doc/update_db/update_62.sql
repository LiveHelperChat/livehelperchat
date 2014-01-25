ALTER TABLE `lh_departament`
ADD `delay_lm` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `hide_after_ntimes` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `invitation_seen_count` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `referrer` varchar(250) NOT NULL,
COMMENT='';