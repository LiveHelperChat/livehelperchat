INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES
('doc_sharer',	'a:10:{i:0;b:0;s:17:\"libre_office_path\";s:20:\"/usr/bin/libreoffice\";s:19:\"supported_extension\";s:51:\"ppt,pptx,doc,odp,docx,xlsx,txt,xls,xlsx,pdf,rtf,odt\";s:18:\"background_process\";i:1;s:13:\"max_file_size\";i:4;s:13:\"pdftoppm_path\";s:17:\"/usr/bin/pdftoppm\";s:13:\"PdftoppmLimit\";i:5;s:14:\"pdftoppm_limit\";i:0;s:14:\"http_user_name\";s:6:\"apache\";s:20:\"http_user_group_name\";s:6:\"apache\";}',	0,	'Libreoffice path',	1);

CREATE TABLE `lh_doc_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `desc` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `converted` int(11) NOT NULL,
  `file_name` varchar(250) NOT NULL,
  `file_path` varchar(250) NOT NULL,
  `file_name_upload` varchar(250) NOT NULL,
  `file_size` int(11) NOT NULL,
  `type` varchar(250) NOT NULL,
  `ext` varchar(250) NOT NULL,
  `pdf_file` varchar(250) NOT NULL,
  `pages_pdf_count` int(11) NOT NULL,
  `pdf_to_img_converted` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;