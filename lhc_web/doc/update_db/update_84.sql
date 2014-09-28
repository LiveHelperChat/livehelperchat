INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('update_ip','127.0.0.1',0,'Which ip should be allowed to update DB by executing http request, separate by comma?',0);
INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('track_if_offline','0',0,'Track online visitors even if there is no online operators',0);

ALTER TABLE `lh_abstract_widget_theme` ADD `copyright_image` varchar(250) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_widget_theme` ADD `copyright_image_path` varchar(250) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_widget_theme` ADD `widget_copyright_url` varchar(250) NOT NULL, COMMENT='';
ALTER TABLE `lh_abstract_widget_theme` ADD `show_copyright` int(11) NOT NULL DEFAULT '1', COMMENT='';