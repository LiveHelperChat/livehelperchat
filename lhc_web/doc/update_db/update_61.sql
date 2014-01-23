INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('explicit_http_mode', '',0,'Please enter explicit http mode. Either http: or https:, do not forget : at the end.', '0');
ALTER TABLE `lh_chat`
ADD `status_sub` int NOT NULL,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('max_message_length','500',0,'Maximum message length in characters', '0');

