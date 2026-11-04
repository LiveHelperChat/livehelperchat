ALTER TABLE `lh_abstract_form_collected` ADD INDEX `evaluated_user_id_ctime` (`evaluated_user_id`, `ctime`, `form_id`, `attr_int_1`);
ALTER TABLE `lh_abstract_rest_api_key` ADD INDEX `api_key_active` (`api_key`, `active`);
ALTER TABLE `lh_abstract_rest_api_key` DROP INDEX `api_key`;
ALTER TABLE `lh_chat_blocked_user` ADD INDEX `nick_btype` (`nick`, `btype`);
