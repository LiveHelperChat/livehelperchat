CREATE TABLE `lh_chat_archive_range` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `range_from` int(11) NOT NULL,
  `range_to` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;