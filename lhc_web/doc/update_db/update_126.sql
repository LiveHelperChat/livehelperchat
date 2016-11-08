CREATE TABLE IF NOT EXISTS `lh_departament_custom_work_hours` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `dep_id` int(11) NOT NULL,
				  `date_from` int(11) NOT NULL,
				  `date_to` int(11) NOT NULL,
				  `start_hour` int(11) NOT NULL,
				  `end_hour` int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `dep_id` (`dep_id`),
				  KEY `date_from` (`date_from`),
				  KEY `search_active` (`date_from`, `date_to`, `dep_id`)
				) DEFAULT CHARSET=utf8;