ALTER TABLE `lh_chat`
ADD `session_referrer` text NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `referrer` text NOT NULL,
COMMENT='';