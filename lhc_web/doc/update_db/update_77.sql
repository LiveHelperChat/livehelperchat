ALTER TABLE `lh_departament`
ADD `nc_cb_execute` tinyint(1) NOT NULL,
ADD `na_cb_execute` tinyint(1) NOT NULL AFTER `nc_cb_execute`,
COMMENT='';