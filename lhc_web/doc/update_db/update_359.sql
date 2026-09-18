ALTER TABLE `lh_users` ADD `team_lead_user_id` int(11) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `lh_users` ADD INDEX `team_lead_user_id` (`team_lead_user_id`);
