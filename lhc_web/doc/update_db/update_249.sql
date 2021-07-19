ALTER TABLE `lh_msg` CHANGE `id` `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT FIRST;
ALTER TABLE `lh_chat` CHANGE `id` `id` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `lh_chat_accept` CHANGE `chat_id` `chat_id` bigint(20) NOT NULL;
ALTER TABLE `lh_chat_online_user` CHANGE `id` `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `lh_chat_online_user` CHANGE `chat_id` `chat_id` bigint(20) NOT NULL;
ALTER TABLE `lh_chat_online_user_footprint` CHANGE `id` `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, CHANGE `chat_id` `chat_id` bigint(20) NOT NULL;
ALTER TABLE `lh_chat_file` CHANGE `chat_id` `chat_id` bigint(20) NOT NULL;