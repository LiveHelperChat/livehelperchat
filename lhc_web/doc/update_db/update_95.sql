ALTER TABLE `lh_cobrowse`
ADD `w` int NOT NULL,
ADD `wh` int NOT NULL AFTER `w`,
ADD `x` int NOT NULL AFTER `wh`,
ADD `y` int NOT NULL AFTER `x`,
COMMENT='';

ALTER TABLE `lh_chat`
CHANGE `operation` `operation` text COLLATE 'utf8_general_ci' NOT NULL AFTER `status_sub`,
COMMENT='';