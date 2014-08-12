INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES ('suggest_leave_msg','1',0,'Suggest user to leave a message then user chooses offline department',0);
UPDATE lh_departament SET start_hour  = start_hour * 100, end_hour = end_hour * 100;
INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`) VALUES ('oupdate_timeout', '', '');
INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`) VALUES ('ouser_timeout', '', '');
INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`) VALUES ('o_department', '', '');
INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`) VALUES ('omax_rows', '', '');
INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`) VALUES ('ogroup_by', '', '');
INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`) VALUES ('omap_depid', '', '');
INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`) VALUES ('omap_mtimeout', '', '');