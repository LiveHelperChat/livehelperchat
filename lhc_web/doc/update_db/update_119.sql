CREATE TABLE `lh_abstract_rest_api_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_key` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `api_key` (`api_key`),
  KEY `user_id` (`user_id`)
) DEFAULT CHARSET=utf8;