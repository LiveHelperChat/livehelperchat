CREATE TABLE `lh_abstract_survey_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `stars` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ftime` int(11) NOT NULL,
  `dep_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`),
  KEY `chat_id` (`chat_id`),
  KEY `user_id` (`user_id`),
  KEY `dep_id` (`dep_id`),
  KEY `ftime` (`ftime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lh_abstract_survey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `max_stars` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
