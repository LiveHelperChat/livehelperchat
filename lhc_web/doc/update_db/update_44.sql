ALTER TABLE `lh_faq`
ADD `is_wildcard` tinyint(1) NOT NULL,
COMMENT='';

ALTER TABLE `lh_faq`
ADD INDEX `is_wildcard` (`is_wildcard`);