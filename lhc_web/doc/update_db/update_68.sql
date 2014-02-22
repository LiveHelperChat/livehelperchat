ALTER TABLE `lh_faq`
ADD `email` varchar(50) NOT NULL,
ADD `identifier` varchar(10) NOT NULL AFTER `email`,
COMMENT='';

ALTER TABLE `lh_faq`
ADD INDEX `identifier` (`identifier`);