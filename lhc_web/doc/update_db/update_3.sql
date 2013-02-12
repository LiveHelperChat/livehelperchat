ALTER TABLE `lh_users`
ADD `hide_online` tinyint(1) NOT NULL,
COMMENT='';

ALTER TABLE `lh_users`
ADD `all_departments` tinyint(1) NOT NULL,
COMMENT='';

ALTER TABLE `lh_users`
DROP `lastactivity`,
COMMENT='';

ALTER TABLE `lh_userdep`
ADD `last_activity` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_userdep`
ADD `hide_online` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_userdep`
ADD INDEX `last_activity_hide_online_dep_id` (`last_activity`, `hide_online`, `dep_id`);