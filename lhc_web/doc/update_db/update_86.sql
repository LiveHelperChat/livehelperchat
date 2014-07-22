ALTER TABLE `lh_abstract_proactive_chat_invitation`
ADD `requires_phone` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `requires_phone` int NOT NULL,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('min_phone_length','8',0,'Minimum phone number length',0);