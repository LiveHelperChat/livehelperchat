ALTER TABLE `lh_departament` ADD `pending_max` int(11) NOT NULL, COMMENT='';
ALTER TABLE `lh_departament` ADD `pending_group_max` int(11) NOT NULL, COMMENT='';

CREATE TABLE `lh_departament_limit_group_member` (  
   `id` int(11) NOT NULL AUTO_INCREMENT,  
   `dep_id` int(11) NOT NULL,  
   `dep_limit_group_id` int(11) NOT NULL,  
   PRIMARY KEY (`id`),  
   KEY `dep_limit_group_id` (`dep_limit_group_id`)) 
DEFAULT CHARSET=utf8;

CREATE TABLE `lh_departament_limit_group` (  
   `id` int(11) NOT NULL AUTO_INCREMENT,  
   `name` varchar(50) NOT NULL,
   `pending_max` int(11) NOT NULL,  
   PRIMARY KEY (`id`)) 
DEFAULT CHARSET=utf8;   	       