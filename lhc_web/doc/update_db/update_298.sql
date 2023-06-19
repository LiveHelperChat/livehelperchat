ALTER TABLE `lh_msg` ADD `del_st` tinyint(1) unsigned NOT NULL DEFAULT '0', ALGORITHM=INPLACE, LOCK=NONE;
-- This query can take long time, run only if you need. This will update messages and mark them as messages were read.
UPDATE `lh_msg` SET `del_st` = 3;