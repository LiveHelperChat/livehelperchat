ALTER TABLE `lh_chat`
ADD `operation_admin` varchar(150) COLLATE 'utf8_general_ci' NOT NULL,
COMMENT='';

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('need_help_tip','0',0,'Show need help tooltip?', '0');