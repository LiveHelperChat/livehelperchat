ALTER TABLE `lhc_mailconv_conversation` ADD `from_address_clean` varchar(250) NOT NULL DEFAULT '', COMMENT='';
ALTER TABLE `lhc_mailconv_conversation` ADD INDEX `from_address_clean` (`from_address_clean`);
UPDATE `lhc_mailconv_conversation` SET `from_address_clean` = LOWER(CONCAT(replace(regexp_replace(`from_address`, '(@+)(.*)', ''),'.',''),'@',regexp_replace(`from_address`, '(.*)(@+)', '')));

-- Just to verify correct replacement
-- SELECT from_address_clean,from_address FROM `lhc_mailconv_conversation` WHERE from_address_clean != from_address
