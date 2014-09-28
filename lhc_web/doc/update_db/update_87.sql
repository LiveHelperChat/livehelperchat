INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('mheight','',0,'Messages box height',0);

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('checkstatus_timeout','0',0,'Interval between chat status checks in seconds, 0 disabled.',0);

ALTER TABLE `lh_abstract_widget_theme`
ADD `explain_text` text NOT NULL,
COMMENT='';