CREATE TABLE `lh_users_remember` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `mtime` int(11) NOT NULL,
 PRIMARY KEY (`id`)
);

ALTER TABLE `lh_chat_online_user`
ADD `first_visit` int(11) NOT NULL,
COMMENT='';

ALTER TABLE `lh_chat_online_user`
ADD `pages_count` int(11) NOT NULL,
COMMENT='';