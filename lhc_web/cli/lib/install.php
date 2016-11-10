<?php

class Install
{
    function __construct($ini_file)
    {
        openlog("livehelperchat", LOG_PID | LOG_PERROR, LOG_LOCAL0);
        syslog(LOG_DEBUG, "Start installation"); 
        $this->settings = parse_ini_file($ini_file, true, INI_SCANNER_TYPED);
    }

    function __destruct()
    {
        syslog(LOG_DEBUG, "Finish installation"); 
        closelog();
    }

    function step1() {
        $Errors = array();
        $directories = $this->_scandir('cache');
        $this->file_is_writable(array('cache'),'', $Errors);
        $this->file_is_writable($directories, 'cache/', $Errors);
        $this->file_is_writable(array('settings'), '', $Errors);
        $var_directories = array(
            'var/storage',
            'var/storageform',
            'var/storagetheme',
            'var/storageadmintheme',
            'var/tmpfiles',
            'var/userphoto',
        );
        $this->file_is_writable(array('var'),'', $Errors);
        $this->file_is_writable($var_directories, '', $Errors);

        if (!extension_loaded ('pdo_mysql' ))
            $Errors[] = "php-pdo extension not detected. Please install php extension";

        if (!extension_loaded('curl'))
            $Errors[] = "php_curl extension not detected. Please install php extension";	

        if (!extension_loaded('mbstring'))
            $Errors[] = "mbstring extension not detected. Please install php extension";	

        if (!extension_loaded('gd'))
            $Errors[] = "gd extension not detected. Please install php extension";	

        if (!function_exists('json_encode'))
            $Errors[] = "json support not detected. Please install php extension";	

        if (version_compare(PHP_VERSION, '5.4.0','<')) {
            $Errors[] = "Minimum 5.4.0 PHP version is required";	
        }

        if (count($Errors) == 0){
            return true;
        } else {
            return $Errors;
        }
    }

    function step2() {
        $Errors = array();
        $database = $this->settings['db'];
        foreach ($database as $key => $value) {
            if (!filter_var($database[$key], FILTER_UNSAFE_RAW)) {
                $Errors[] = "Please enter database $key";
            }
        }
        if (!filter_var($database['database'], FILTER_SANITIZE_STRING))
        {
            $Errors[] = 'Please enter database name';
        }

        if (count($Errors) == 0) {
            try {
                $db = ezcDbFactory::create( "mysql://{$database['user']}:{$database['password']}@{$database['host']}:{$database['port']}/{$database['database']}" );
            } catch (Exception $e) {
                $Errors[] = "Cannot login with provided logins. Returned message: ".$e->getMessage();
            }
        }

        if (count($Errors) == 0) {
            $cfgSite = erConfigClassLhConfig::getInstance();
            foreach ($database as $key => $value) {
                $cfgSite->setSetting( 'db', $key, $value);
            }
            $cfgSite->setSetting( 'site', 'secrethash', substr(md5(time() . ":" . mt_rand()),0,10));
            return true;
        } else {
            return $Errors;
        }
    }

    function step3() {

        $Errors = array();

        $form = (object)$this->settings['admin'];
        if (!filter_var($form->AdminUsername, FILTER_UNSAFE_RAW))
        {
            $Errors[] = 'Please enter admin username';
        }

        if (!empty($form->AdminUsername) && strlen($form->AdminUsername) > 40)
        {
            $Errors[] = 'Maximum 40 characters for admin username';
        }

        if (!filter_var($form->AdminPassword, FILTER_UNSAFE_RAW))
        {
            $Errors[] = 'Please enter admin password';
        }

        if (!empty($form->AdminPassword) && strlen($form->AdminPassword) > 40)
        {
            $Errors[] = 'Maximum 40 characters for admin password';
        }

        if (!filter_var($form->AdminEmail, FILTER_VALIDATE_EMAIL))
        {
            $Errors[] = 'Wrong email address';
        }

        if (!filter_var($form->DefaultDepartament, FILTER_SANITIZE_STRING))
        {
            $Errors[] = 'Please enter default department name';
        }

        if (count($Errors) == 0) {
            $adminEmail = $form->AdminEmail;

            /*DATABASE TABLES SETUP*/
            $db = ezcDbInstance::get();

            $db->query("CREATE TABLE IF NOT EXISTS `lh_chat` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `nick` varchar(50) NOT NULL,
                `status` int(11) NOT NULL DEFAULT '0',
                `status_sub` int(11) NOT NULL DEFAULT '0',
                `time` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `hash` varchar(40) NOT NULL,
                `referrer` text NOT NULL,
                `session_referrer` text NOT NULL,
                `chat_variables` text NOT NULL,
                `remarks` text NOT NULL,
                `ip` varchar(100) NOT NULL,
                `dep_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `user_status` int(11) NOT NULL DEFAULT '0',
                `user_closed_ts` int(11) NOT NULL DEFAULT '0',
                `support_informed` int(11) NOT NULL DEFAULT '0',
                `unread_messages_informed` int(11) NOT NULL DEFAULT '0',
                `reinform_timeout` int(11) NOT NULL DEFAULT '0',
                `email` varchar(100) NOT NULL,
                `country_code` varchar(100) NOT NULL,
                `country_name` varchar(100) NOT NULL,
                `unanswered_chat` int(11) NOT NULL,
                `user_typing` int(11) NOT NULL,
                `user_typing_txt` varchar(50) NOT NULL,
                `operator_typing` int(11) NOT NULL,
                `operator_typing_id` int(11) NOT NULL,
                `phone` varchar(100) NOT NULL,
                `has_unread_messages` int(11) NOT NULL,
                `last_user_msg_time` int(11) NOT NULL,
                `fbst` tinyint(1) NOT NULL,
                `online_user_id` int(11) NOT NULL,
                `last_msg_id` int(11) NOT NULL,
                `additional_data` text NOT NULL,
                `timeout_message` varchar(250) NOT NULL,
                `user_tz_identifier` varchar(50) NOT NULL,
                `lat` varchar(10) NOT NULL,
                `lon` varchar(10) NOT NULL,
                `city` varchar(100) NOT NULL,
                `operation` text NOT NULL,
                `operation_admin` varchar(200) NOT NULL,
                `chat_locale` varchar(10) NOT NULL,
                `chat_locale_to` varchar(10) NOT NULL,
                `mail_send` int(11) NOT NULL,
                `screenshot_id` int(11) NOT NULL,
                `wait_time` int(11) NOT NULL,
                `wait_timeout` int(11) NOT NULL,
                `wait_timeout_send` int(11) NOT NULL,
                `wait_timeout_repeat` int(11) NOT NULL,
                `chat_duration` int(11) NOT NULL,
                `tslasign` int(11) NOT NULL,
                `priority` int(11) NOT NULL,
                `chat_initiator` int(11) NOT NULL,
                `transfer_timeout_ts` int(11) NOT NULL,
                `transfer_timeout_ac` int(11) NOT NULL,
                `transfer_if_na` int(11) NOT NULL,
                `na_cb_executed` int(11) NOT NULL,
                `nc_cb_executed` tinyint(1) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `status_user_id` (`status`,`user_id`),
            KEY `user_id` (`user_id`),
            KEY `unanswered_chat` (`unanswered_chat`),
            KEY `online_user_id` (`online_user_id`),
            KEY `dep_id` (`dep_id`),
            KEY `product_id` (`product_id`),
            KEY `has_unread_messages_dep_id_id` (`has_unread_messages`,`dep_id`,`id`),
            KEY `status_dep_id_id` (`status`,`dep_id`,`id`),
            KEY `status_dep_id_priority_id` (`status`,`dep_id`,`priority`,`id`),
            KEY `status_priority_id` (`status`,`priority`,`id`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_chat_blocked_user` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `ip` varchar(100) NOT NULL,
                `user_id` int(11) NOT NULL,
                `datets` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `ip` (`ip`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_chat_archive_range` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `range_from` int(11) NOT NULL,
                `range_to` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_abstract_auto_responder` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `siteaccess` varchar(3) NOT NULL,
                `wait_message` varchar(250) NOT NULL,
                `wait_timeout` int(11) NOT NULL,
                `position` int(11) NOT NULL,
                `dep_id` int(11) NOT NULL,
                `repeat_number` int(11) NOT NULL DEFAULT '1',
                `timeout_message` varchar(250) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `siteaccess_position` (`siteaccess`,`position`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_abstract_widget_theme` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(250) NOT NULL,
                `name_company` varchar(250) NOT NULL,
                `onl_bcolor` varchar(10) NOT NULL,
                `bor_bcolor` varchar(10) NOT NULL DEFAULT 'e3e3e3',
                `text_color` varchar(10) NOT NULL,
                `online_image` varchar(250) NOT NULL,
                `online_image_path` varchar(250) NOT NULL,
                `offline_image` varchar(250) NOT NULL,
                `offline_image_path` varchar(250) NOT NULL,
                `logo_image` varchar(250) NOT NULL,
                `logo_image_path` varchar(250) NOT NULL,
                `need_help_image` varchar(250) NOT NULL,
                `header_background` varchar(10) NOT NULL,
                `need_help_tcolor` varchar(10) NOT NULL,
                `need_help_bcolor` varchar(10) NOT NULL,
                `need_help_border` varchar(10) NOT NULL,
                `need_help_close_bg` varchar(10) NOT NULL,
                `need_help_hover_bg` varchar(10) NOT NULL,
                `need_help_close_hover_bg` varchar(10) NOT NULL,
                `need_help_image_path` varchar(250) NOT NULL,
                `custom_status_css` text NOT NULL,
                `custom_container_css` text NOT NULL,
                `custom_widget_css` text NOT NULL,
                `need_help_header` varchar(250) NOT NULL,
                `need_help_text` varchar(250) NOT NULL,
                `online_text` varchar(250) NOT NULL,
                `offline_text` varchar(250) NOT NULL,
                `widget_border_color` varchar(10) NOT NULL,
                `copyright_image` varchar(250) NOT NULL,
                `copyright_image_path` varchar(250) NOT NULL,
                `widget_copyright_url` varchar(250) NOT NULL,
                `show_copyright` int(11) NOT NULL DEFAULT '1',
                `explain_text` text NOT NULL,
                `intro_operator_text` varchar(250) NOT NULL,
                `operator_image` varchar(250) NOT NULL,
                `operator_image_path` varchar(250) NOT NULL,
                `minimize_image` varchar(250) NOT NULL,
                `minimize_image_path` varchar(250) NOT NULL,
                `restore_image` varchar(250) NOT NULL,
                `restore_image_path` varchar(250) NOT NULL,
                `close_image` varchar(250) NOT NULL,
                `close_image_path` varchar(250) NOT NULL,
                `popup_image` varchar(250) NOT NULL,
                `popup_image_path` varchar(250) NOT NULL,
                `support_joined` varchar(250) NOT NULL,
                `support_closed` varchar(250) NOT NULL,
                `pending_join` varchar(250) NOT NULL,
                `noonline_operators` varchar(250) NOT NULL,
                `noonline_operators_offline` varchar(250) NOT NULL,
                `hide_close` int(11) NOT NULL,
                `hide_popup` int(11) NOT NULL,
                `show_need_help` int(11) NOT NULL DEFAULT '1',
                `show_need_help_timeout` int(11) NOT NULL DEFAULT '24',
                `header_height` int(11) NOT NULL,
                `header_padding` int(11) NOT NULL,
                `widget_border_width` int(11) NOT NULL,
                `show_voting` tinyint(1) NOT NULL DEFAULT '1',
                `department_title` varchar(250) NOT NULL,
                `department_select` varchar(250) NOT NULL,
                `buble_visitor_background` varchar(250) NOT NULL,
                `buble_visitor_title_color` varchar(250) NOT NULL,
                `buble_visitor_text_color` varchar(250) NOT NULL,
                `buble_operator_background` varchar(250) NOT NULL,
                `buble_operator_title_color` varchar(250) NOT NULL,
                `buble_operator_text_color` varchar(250) NOT NULL,
                PRIMARY KEY (`id`)				
            ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_faq` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `question` varchar(250) NOT NULL,
                `answer` text NOT NULL,
                `url` varchar(250) NOT NULL,
                `email` varchar(50) NOT NULL,
                `identifier` varchar(10) NOT NULL,
                `active` int(11) NOT NULL,
                `has_url` tinyint(1) NOT NULL,
                `is_wildcard` tinyint(1) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `active` (`active`),
            KEY `active_url` (`active`,`url`),
            KEY `has_url` (`has_url`),
            KEY `identifier` (`identifier`),
            KEY `is_wildcard` (`is_wildcard`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_cobrowse` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `chat_id` int(11) NOT NULL,
                `online_user_id` int(11) NOT NULL,
                `mtime` int(11) NOT NULL,
                `url` varchar(250) NOT NULL,
                `initialize` longtext NOT NULL,
                `modifications` longtext NOT NULL,
                `finished` tinyint(1) NOT NULL,
                `w` int NOT NULL,
                `wh` int NOT NULL,
                `x` int NOT NULL,
                `y` int NOT NULL,        	   		
                PRIMARY KEY (`id`),
            KEY `chat_id` (`chat_id`),
            KEY `online_user_id` (`online_user_id`)
        ) DEFAULT CHARSET=utf8");

            $db->query("CREATE TABLE `lh_abstract_survey` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(250) NOT NULL,
                `max_stars` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8");

            $db->query("CREATE TABLE `lh_admin_theme` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `static_content` longtext NOT NULL,
                `static_js_content` longtext NOT NULL,
                `static_css_content` longtext NOT NULL,
                `header_content` text NOT NULL,
                `header_css` text NOT NULL,
                PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8");

            $db->query("CREATE TABLE `lh_chat_paid` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT,  
                `hash` varchar(250) NOT NULL,  
                `chat_id` int(11) NOT NULL, 
                PRIMARY KEY (`id`),  
            KEY `hash` (`hash`),  
            KEY `chat_id` (`chat_id`)) DEFAULT CHARSET=utf8");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_abstract_survey_item` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `survey_id` int(11) NOT NULL,
                `chat_id` int(11) NOT NULL,
                `stars` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `ftime` int(11) NOT NULL,
                `dep_id` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `survey_id` (`survey_id`),
            KEY `chat_id` (`chat_id`),
            KEY `user_id` (`user_id`),
            KEY `dep_id` (`dep_id`),
            KEY `ftime` (`ftime`)
        ) DEFAULT CHARSET=utf8");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_speech_language` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_speech_language_dialect` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `language_id` int(11) NOT NULL,
                `lang_name` varchar(100) NOT NULL,
                `lang_code` varchar(100) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `language_id` (`language_id`)
        ) DEFAULT CHARSET=utf8");

            $db->query("INSERT INTO `lh_speech_language` (`id`, `name`) VALUES
                (1,	'Afrikaans'),
            (2,	'Bahasa Indonesia'),
            (3,	'Bahasa Melayu'),
            (4,	'Català'),
            (5,	'Čeština'),
            (6,	'Deutsch'),
            (7,	'English'),
            (8,	'Español'),
            (9,	'Euskara'),
            (10,	'Français'),
            (11,	'Galego'),
            (12,	'Hrvatski'),
            (13,	'IsiZulu'),
            (14,	'Íslenska'),
            (15,	'Italiano'),
            (16,	'Magyar'),
            (17,	'Nederlands'),
            (18,	'Norsk bokmål'),
            (19,	'Polski'),
            (20,	'Português'),
            (21,	'Română'),
            (22,	'Slovenčina'),
            (23,	'Suomi'),
            (24,	'Svenska'),
            (25,	'Türkçe'),
            (26,	'български'),
            (27,	'Pусский'),
            (28,	'Српски'),
            (29,	'한국어'),
            (30,	'中文'),
            (31,	'日本語'),
            (32,	'Lingua latīna')");

            $db->query("INSERT INTO `lh_speech_language_dialect` (`id`, `language_id`, `lang_name`, `lang_code`) VALUES
                (1,	1,	'Afrikaans',	'af-ZA'),
            (2,	2,	'Bahasa Indonesia',	'id-ID'),
            (3,	3,	'Bahasa Melayu',	'ms-MY'),
            (4,	4,	'Català',	'ca-ES'),
            (5,	5,	'Čeština',	'cs-CZ'),
            (6,	6,	'Deutsch',	'de-DE'),
            (7,	7,	'Australia',	'en-AU'),
            (8,	7,	'Canada',	'en-CA'),
            (9,	7,	'India',	'en-IN'),
            (10,	7,	'New Zealand',	'en-NZ'),
            (11,	7,	'South Africa',	'en-ZA'),
            (12,	7,	'United Kingdom',	'en-GB'),
            (13,	7,	'United States',	'en-US'),
            (14,	8,	'Argentina',	'es-AR'),
            (15,	8,	'Bolivia',	'es-BO'),
            (16,	8,	'Chile',	'es-CL'),
            (17,	8,	'Colombia',	'es-CO'),
            (18,	8,	'Costa Rica',	'es-CR'),
            (19,	8,	'Ecuador',	'es-EC'),
            (20,	8,	'El Salvador',	'es-SV'),
            (21,	8,	'España',	'es-ES'),
            (22,	8,	'Estados Unidos',	'es-US'),
            (23,	8,	'Guatemala',	'es-GT'),
            (24,	8,	'Honduras',	'es-HN'),
            (25,	8,	'México',	'es-MX'),
            (26,	8,	'Nicaragua',	'es-NI'),
            (27,	8,	'Panamá',	'es-PA'),
            (28,	8,	'Paraguay',	'es-PY'),
            (29,	8,	'Perú',	'es-PE'),
            (30,	8,	'Puerto Rico',	'es-PR'),
            (31,	8,	'República Dominicana',	'es-DO'),
            (32,	8,	'Uruguay',	'es-UY'),
            (33,	8,	'Venezuela',	'es-VE'),
            (34,	9,	'Euskara',	'eu-ES'),
            (35,	10,	'Français',	'fr-FR'),
            (36,	11,	'Galego',	'gl-ES'),
            (37,	12,	'Hrvatski',	'hr_HR'),
            (38,	13,	'IsiZulu',	'zu-ZA'),
            (39,	14,	'Íslenska',	'is-IS'),
            (40,	15,	'Italia',	'it-IT'),
            (41,	15,	'Svizzera',	'it-CH'),
            (42,	16,	'Magyar',	'hu-HU'),
            (43,	17,	'Nederlands',	'nl-NL'),
            (44,	18,	'Norsk bokmål',	'nb-NO'),
            (45,	19,	'Polski',	'pl-PL'),
            (46,	20,	'Brasil',	'pt-BR'),
            (47,	20,	'Portugal',	'pt-PT'),
            (48,	21,	'Română',	'ro-RO'),
            (49,	22,	'Slovenčina',	'sk-SK'),
            (50,	23,	'Suomi',	'fi-FI'),
            (51,	24,	'Svenska',	'sv-SE'),
            (52,	25,	'Türkçe',	'tr-TR'),
            (53,	26,	'български',	'bg-BG'),
            (54,	27,	'Pусский',	'ru-RU'),
            (55,	28,	'Српски',	'sr-RS'),
            (56,	29,	'한국어',	'ko-KR'),
            (57,	30,	'普通话 (中国大陆)',	'cmn-Hans-CN'),
            (58,	30,	'普通话 (香港)',	'cmn-Hans-HK'),
            (59,	30,	'中文 (台灣)',	'cmn-Hant-TW'),
            (60,	30,	'粵語 (香港)',	'yue-Hant-HK'),
            (61,	31,	'日本語',	'ja-JP'),
            (62,	32,	'Lingua latīna',	'la')");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_speech_chat_language` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `chat_id` int(11) NOT NULL,
                `language_id` int(11) NOT NULL,
                `dialect` varchar(50) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `chat_id` (`chat_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_chat_file` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `upload_name` varchar(255) NOT NULL,
                `size` int(11) NOT NULL,
                `type` varchar(255) NOT NULL,
                `file_path` varchar(255) NOT NULL,
                `extension` varchar(255) NOT NULL,
                `chat_id` int(11) NOT NULL,
                `online_user_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `date` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `chat_id` (`chat_id`),
            KEY `online_user_id` (`online_user_id`),
            KEY `user_id` (`user_id`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_abstract_email_template` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(250) NOT NULL,
                `from_name` varchar(150) NOT NULL,
                `from_name_ac` tinyint(4) NOT NULL,
                `from_email` varchar(150) NOT NULL,
                `from_email_ac` tinyint(4) NOT NULL,
                `user_mail_as_sender` tinyint(4) NOT NULL,
                `content` text NOT NULL,
                `subject` varchar(250) NOT NULL,
                `bcc_recipients` varchar(200) NOT NULL,
                `subject_ac` tinyint(4) NOT NULL,
                `reply_to` varchar(150) NOT NULL,
                `reply_to_ac` tinyint(4) NOT NULL,
                `recipient` varchar(150) NOT NULL,
                PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8;");

            $db->query("INSERT INTO `lh_abstract_email_template` (`id`, `name`, `from_name`, `from_name_ac`, `from_email`, `from_email_ac`, `user_mail_as_sender`, `content`, `subject`, `subject_ac`, `reply_to`, `reply_to_ac`, `recipient`,`bcc_recipients`) VALUES
                (1,'Send mail to user','Live Helper Chat',0,'',0, 0,'Dear {user_chat_nick},\r\n\r\n{additional_message}\r\n\r\nLive Support response:\r\n{messages_content}\r\n\r\nSincerely,\r\nLive Support Team\r\n','{name_surname} has responded to your request',	1,'',1,'',''),
            (2,'Support request from user',	'',	0,	'',	0,	 0,'Hello,\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nAdditional data, if any:\r\n{additional_data}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nLink to chat if any:\r\n{prefillchat}\r\n\r\nSincerely,\r\nLive Support Team',	'Support request from user',	0,	'',	0,	'{$adminEmail}',''),
            (3,	'User mail for himself',	'Live Helper Chat',	0,	'',	0,	 0,'Dear {user_chat_nick},\r\n\r\nTranscript:\r\n{messages_content}\r\n\r\nSincerely,\r\nLive Support Team\r\n',	'Chat transcript',	0,	'',	0,	'',''),
            (4,	'New chat request',	'Live Helper Chat',	0,	'',	0,	 0,'Hello,\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nClick to accept chat automatically\r\n{url_accept}\r\n\r\nSincerely,\r\nLive Support Team',	'New chat request',	0,	'',	0,	'{$adminEmail}',''),
            (5,	'Chat was closed',	'Live Helper Chat',	0,	'',	0,	 0,'Hello,\r\n\r\n{operator} has closed a chat\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nAdditional data, if any:\r\n{additional_data}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nSincerely,\r\nLive Support Team',	'Chat was closed',	0,	'',	0,	'',''),
            (6,	'New FAQ question',	'Live Helper Chat',	0,	'',	0,	 0,'Hello,\r\n\r\nNew FAQ question\r\nEmail: {email}\r\n\r\nQuestion:\r\n{question}\r\n\r\nQuestion URL:\r\n{url_question}\r\n\r\nURL to answer a question:\r\n{url_request}\r\n\r\nSincerely,\r\nLive Support Team',	'New FAQ question',	0,	'',	0,	'',	''),
            (7,	'New unread message',	'Live Helper Chat',	0,	'',	0,	 0,'Hello,\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nClick to accept chat automatically\r\n{url_accept}\r\n\r\nSincerely,\r\nLive Support Team',	'New chat request',	0,	'',	0,	'{$adminEmail}',''),
            (8,	'Filled form',	'Live Helper Chat',	0,	'',	0,	 0,'Hello,\r\n\r\nUser has filled a form\r\nForm name - {form_name}\r\nUser IP - {ip}\r\nDownload filled data - {url_download}\r\nIdentifier - {identifier}\r\nView filled data - {url_view}\r\n\r\n {content} \r\n\r\nSincerely,\r\nLive Support Team','Filled form - {form_name}',	0,	'',	0,	'{$adminEmail}',''),
            (9,	'Chat was accepted',	'Live Helper Chat',	0,	'',	0,	 0, 'Hello,\r\n\r\nOperator {user_name} has accepted a chat [{chat_id}]\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nClick to accept chat automatically\r\n{url_accept}\r\n\r\nSincerely,\r\nLive Support Team',	'Chat was accepted [{chat_id}]',	0,	'',	0,	'{$adminEmail}',''),
            (10, 'Permission request',	'Live Helper Chat',	0,	'',	0,	0, 'Hello,\r\n\r\nOperator {user} has requested these permissions\n\r\n{permissions}\r\n\r\nSincerely,\r\nLive Support Team',	'Permission request from {user}',	0,	'',	0,	'{$adminEmail}',	'');");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_question` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `question` varchar(250) NOT NULL,
                `location` varchar(250) NOT NULL,
                `active` int(11) NOT NULL,
                `priority` int(11) NOT NULL,
                `is_voting` int(11) NOT NULL,
                `question_intro` text NOT NULL,
                `revote` int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
            KEY `priority` (`priority`),
            KEY `active_priority` (`active`,`priority`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_question_answer` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `ip` bigint(20) NOT NULL,
                `question_id` int(11) NOT NULL,
                `answer` text NOT NULL,
                `ctime` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `ip` (`ip`),
            KEY `question_id` (`question_id`)
        ) DEFAULT CHARSET=utf8");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_question_option` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `question_id` int(11) NOT NULL,
                `option_name` varchar(250) NOT NULL,
                `priority` tinyint(4) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `question_id` (`question_id`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_question_option_answer` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `question_id` int(11) NOT NULL,
                `option_id` int(11) NOT NULL,
                `ctime` int(11) NOT NULL,
                `ip` bigint(20) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `question_id` (`question_id`),
            KEY `ip` (`ip`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_abstract_product` (
                `id` int(11) NOT NULL AUTO_INCREMENT, 
                `name` varchar(250) NOT NULL, 
                `disabled` int(11) NOT NULL, 
                `priority` int(11) NOT NULL, 
                `departament_id` int(11) NOT NULL, 
                KEY `departament_id` (`departament_id`), 
            PRIMARY KEY (`id`)) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_abstract_browse_offer_invitation` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `siteaccess` varchar(10) NOT NULL,
                `time_on_site` int(11) NOT NULL,
                `content` longtext NOT NULL,
                `callback_content` longtext NOT NULL,
                `lhc_iframe_content` tinyint(4) NOT NULL,
                `custom_iframe_url` varchar(250) NOT NULL,
                `name` varchar(250) NOT NULL,
                `identifier` varchar(50) NOT NULL,
                `executed_times` int(11) NOT NULL,
                `url` varchar(250) NOT NULL,
                `active` int(11) NOT NULL,
                `has_url` int(11) NOT NULL,
                `is_wildcard` int(11) NOT NULL,
                `referrer` varchar(250) NOT NULL,
                `priority` varchar(250) NOT NULL,
                `hash` varchar(40) NOT NULL,
                `width` int(11) NOT NULL,
                `height` int(11) NOT NULL,
                `unit` varchar(10) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `active` (`active`),
            KEY `identifier` (`identifier`)
        ) DEFAULT CHARSET=utf8;");


            $db->query("CREATE TABLE IF NOT EXISTS `lh_abstract_form` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,        	   
                `content` longtext NOT NULL,
                `recipient` varchar(250) NOT NULL,
                `active` int(11) NOT NULL,
                `name_attr` varchar(250) NOT NULL,
                `intro_attr` varchar(250) NOT NULL,
                `xls_columns` text NOT NULL,
                `pagelayout` varchar(200) NOT NULL,
                `post_content` text NOT NULL,
                PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8;");        	   

            $db->query("CREATE TABLE IF NOT EXISTS `lh_abstract_form_collected` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `form_id` int(11) NOT NULL,
                `ctime` int(11) NOT NULL,
                `ip` varchar(250) NOT NULL,
                `identifier` varchar(250) NOT NULL,
                `content` longtext NOT NULL,
                PRIMARY KEY (`id`),
            KEY `form_id` (`form_id`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_chatbox` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `identifier` varchar(50) NOT NULL,
                `name` varchar(100) NOT NULL,
                `chat_id` int(11) NOT NULL,
                `active` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `identifier` (`identifier`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_canned_msg` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `msg` text NOT NULL,
                `fallback_msg` text NOT NULL,
                `title` varchar(250) NOT NULL,
                `explain` varchar(250) NOT NULL,
                `position` int(11) NOT NULL,
                `department_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `delay` int(11) NOT NULL,
                `auto_send` tinyint(1) NOT NULL,
                `attr_int_1` int(11) NOT NULL,
                `attr_int_2` int(11) NOT NULL,
                `attr_int_3` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `department_id` (`department_id`),
            KEY `attr_int_1` (`attr_int_1`),
            KEY `attr_int_2` (`attr_int_2`),
            KEY `attr_int_3` (`attr_int_3`),
            KEY `position_title` (`position`, `title`),
            KEY `user_id` (`user_id`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_chat_online_user_footprint` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `chat_id` int(11) NOT NULL,
                `online_user_id` int(11) NOT NULL,
                `page` varchar(250) NOT NULL,
                `vtime` varchar(250) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `chat_id_vtime` (`chat_id`,`vtime`),
            KEY `online_user_id` (`online_user_id`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_users_setting` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `identifier` varchar(50) NOT NULL,
                `value` varchar(255) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`,`identifier`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_users_setting_option` (
                `identifier` varchar(50) NOT NULL,
                `class` varchar(50) NOT NULL,
                `attribute` varchar(40) NOT NULL,
                PRIMARY KEY (`identifier`)
            ) DEFAULT CHARSET=utf8;");        	   

            $db->query("INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`) VALUES
                ('chat_message',	'',	''),
            ('new_chat_sound',	'',	''),
            ('enable_pending_list', '', ''),
            ('enable_active_list', '', ''),
            ('enable_close_list', '', ''),
            ('new_user_bn', '', ''),
            ('new_user_sound', '', ''),
            ('oupdate_timeout', '', ''),
            ('ouser_timeout', '', ''),
            ('o_department', '', ''),
            ('omax_rows', '', ''),
            ('ogroup_by', '', ''),
            ('omap_depid', '', ''),
            ('omap_mtimeout', '', ''),
            ('dwo', '', ''),
            ('enable_unread_list', '', '')");


            $db->query("CREATE TABLE IF NOT EXISTS `lh_chat_config` (
                `identifier` varchar(50) NOT NULL,
                `value` text NOT NULL,
                `type` tinyint(1) NOT NULL DEFAULT '0',
                `explain` varchar(250) NOT NULL,
                `hidden` int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (`identifier`)
            ) DEFAULT CHARSET=utf8;");

            $randomHash = erLhcoreClassModelForgotPassword::randomPassword(9);
            $randomHashLength = strlen($randomHash);
            $exportHash = erLhcoreClassModelForgotPassword::randomPassword(9);

            if (extension_loaded('bcmath')){
                $geoRow = "('geo_data','a:5:{i:0;b:0;s:21:\"geo_detection_enabled\";i:1;s:22:\"geo_service_identifier\";s:8:\"max_mind\";s:23:\"max_mind_detection_type\";s:7:\"country\";s:22:\"max_mind_city_location\";s:37:\"var/external/geoip/GeoLite2-City.mmdb\";}',0,'',1)";
            } else {
                $geoRow = "('geo_data', '', '0', '', '1')";
            }

            $db->query("INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES
                ('tracked_users_cleanup',	'160',	0,	'How many days keep records of online users.',	0),
            ('list_online_operators', '0', '0', 'List online operators.', '0'),
            ('voting_days_limit',	'7',	0,	'How many days voting widget should not be expanded after last show',	0),
            ('track_online_visitors',	'1',	0,	'Enable online site visitors tracking',	0),
            ('pro_active_invite',	'1',	0,	'Is pro active chat invitation active. Online users tracking also has to be enabled',	0),
            ('customer_company_name',	'Live Helper Chat',	0,	'Your company name - visible in bottom left corner',	0),
            ('customer_site_url',	'http://livehelperchat.com',	0,	'Your site URL address',	0),
            ('smtp_data',	'a:5:{s:4:\"host\";s:0:\"\";s:4:\"port\";s:2:\"25\";s:8:\"use_smtp\";i:0;s:8:\"username\";s:0:\"\";s:8:\"password\";s:0:\"\";}',	0,	'SMTP configuration',	1),
            ('chatbox_data',	'a:6:{i:0;b:0;s:20:\"chatbox_auto_enabled\";i:0;s:19:\"chatbox_secret_hash\";s:{$randomHashLength}:\"{$randomHash}\";s:20:\"chatbox_default_name\";s:7:\"Chatbox\";s:17:\"chatbox_msg_limit\";i:50;s:22:\"chatbox_default_opname\";s:7:\"Manager\";}',	0,	'Chatbox configuration',	1),
            ('start_chat_data',	'a:23:{i:0;b:0;s:21:\"name_visible_in_popup\";b:1;s:27:\"name_visible_in_page_widget\";b:1;s:19:\"name_require_option\";s:8:\"required\";s:22:\"email_visible_in_popup\";b:0;s:28:\"email_visible_in_page_widget\";b:0;s:20:\"email_require_option\";s:8:\"required\";s:24:\"message_visible_in_popup\";b:1;s:30:\"message_visible_in_page_widget\";b:1;s:22:\"message_require_option\";s:8:\"required\";s:22:\"phone_visible_in_popup\";b:0;s:28:\"phone_visible_in_page_widget\";b:0;s:20:\"phone_require_option\";s:8:\"required\";s:21:\"force_leave_a_message\";b:0;s:29:\"offline_name_visible_in_popup\";b:1;s:35:\"offline_name_visible_in_page_widget\";b:1;s:27:\"offline_name_require_option\";s:8:\"required\";s:30:\"offline_phone_visible_in_popup\";b:0;s:36:\"offline_phone_visible_in_page_widget\";b:0;s:28:\"offline_phone_require_option\";s:8:\"required\";s:32:\"offline_message_visible_in_popup\";b:1;s:38:\"offline_message_visible_in_page_widget\";b:1;s:30:\"offline_message_require_option\";s:8:\"required\";}',	0,	'',	1),
            ('application_name',	'a:6:{s:3:\"eng\";s:31:\"Live Helper Chat - live support\";s:3:\"lit\";s:26:\"Live Helper Chat - pagalba\";s:3:\"hrv\";s:0:\"\";s:3:\"esp\";s:0:\"\";s:3:\"por\";s:0:\"\";s:10:\"site_admin\";s:31:\"Live Helper Chat - live support\";}',	1,	'Support application name, visible in browser title.',	0),
            ('track_footprint',	'0',	0,	'Track users footprint. For this also online visitors tracking should be enabled',	0),
            ('pro_active_limitation',	'-1',	0,	'Pro active chats invitations limitation based on pending chats, (-1) do not limit, (0,1,n+1) number of pending chats can be for invitation to be shown.',	0),
            ('pro_active_show_if_offline',	'0',	0,	'Should invitation logic be executed if there is no online operators',	0),
            ('export_hash',	'{$exportHash}',	0,	'Chats export secret hash',	0),
            ('message_seen_timeout', 24, 0, 'Proactive message timeout in hours. After how many hours proactive chat mesasge should be shown again.',	0),
            ('reopen_chat_enabled',1,	0,	'Reopen chat functionality enabled',	0),
            ('ignorable_ip',	'',	0,	'Which ip should be ignored in online users list, separate by comma',0),
            ('run_departments_workflow', 0, 0, 'Should cronjob run departments transfer workflow, even if user leaves a chat',	0),
            ('geo_location_data', 'a:3:{s:4:\"zoom\";i:4;s:3:\"lat\";s:7:\"49.8211\";s:3:\"lng\";s:7:\"11.7835\";}', '0', '', '1'),
            ('xmp_data','a:14:{i:0;b:0;s:4:\"host\";s:15:\"talk.google.com\";s:6:\"server\";s:9:\"gmail.com\";s:8:\"resource\";s:6:\"xmpphp\";s:4:\"port\";s:4:\"5222\";s:7:\"use_xmp\";i:0;s:8:\"username\";s:0:\"\";s:8:\"password\";s:0:\"\";s:11:\"xmp_message\";s:78:\"New chat request [{chat_id}]\r\n{messages}\r\nClick to accept a chat\r\n{url_accept}\";s:10:\"recipients\";s:0:\"\";s:20:\"xmp_accepted_message\";s:69:\"{user_name} has accepted a chat [{chat_id}]\r\n{messages}\r\n{url_accept}\";s:16:\"use_standard_xmp\";i:0;s:15:\"test_recipients\";s:0:\"\";s:21:\"test_group_recipients\";s:0:\"\";}',0,'XMP data',1),
            ('run_unaswered_chat_workflow', 0, 0, 'Should cronjob run unanswered chats workflow and execute unaswered chats callback, 0 - no, any other number bigger than 0 is a minits how long chat have to be not accepted before executing callback.',0),
            ('disable_popup_restore', 0, 0, 'Disable option in widget to open new window. Restore icon will be hidden',	0),
            ('accept_tos_link', '#', 0, 'Change to your site Terms of Service', 0),
            ('hide_button_dropdown', '0', 0, 'Hide close button in dropdown', 0),
            ('on_close_exit_chat', '0', 0, 'On chat close exit chat', 0),
            ('product_enabled_module','0','0','Product module is enabled', '1'),
            ('paidchat_data','','0','Paid chat configuration','1'),
            ('disable_iframe_sharing',	'1',	0,	'Disable iframes in sharing mode',	0),
            ('file_configuration',	'a:7:{i:0;b:0;s:5:\"ft_op\";s:43:\"gif|jpe?g|png|zip|rar|xls|doc|docx|xlsx|pdf\";s:5:\"ft_us\";s:26:\"gif|jpe?g|png|doc|docx|pdf\";s:6:\"fs_max\";i:2048;s:18:\"active_user_upload\";b:0;s:16:\"active_op_upload\";b:1;s:19:\"active_admin_upload\";b:1;}',	0,	'Files configuration item',	1),
            ('accept_chat_link_timeout',	'300',	0,	'How many seconds chat accept link is valid. Set 0 to force login all the time manually.',	0),
            ('session_captcha',0,	0,	'Use session captcha. LHC have to be installed on the same domain or subdomain.',	0),
            ('sync_sound_settings',	'a:16:{i:0;b:0;s:12:\"repeat_sound\";i:1;s:18:\"repeat_sound_delay\";i:5;s:10:\"show_alert\";b:0;s:22:\"new_chat_sound_enabled\";b:1;s:31:\"new_message_sound_admin_enabled\";b:1;s:30:\"new_message_sound_user_enabled\";b:1;s:14:\"online_timeout\";d:300;s:22:\"check_for_operator_msg\";d:10;s:21:\"back_office_sinterval\";d:10;s:22:\"chat_message_sinterval\";d:3.5;s:20:\"long_polling_enabled\";b:0;s:30:\"polling_chat_message_sinterval\";d:1.5;s:29:\"polling_back_office_sinterval\";d:5;s:18:\"connection_timeout\";i:30;s:28:\"browser_notification_message\";b:0;}',	0,	'',	1),
            ('sound_invitation', 1, 0, 'Play sound on invitation to chat.',	0),
            ('explicit_http_mode', '',0,'Please enter explicit http mode. Either http: or https:, do not forget : at the end.', '0'),
            ('track_domain',	'',	0,	'Set your domain to enable user tracking across different domain subdomains.',	0),
            ('max_message_length','500',0,'Maximum message length in characters', '0'),
            ('need_help_tip','1',0,'Show need help tooltip?', '0'),
            ('need_help_tip_timeout','24',0,'Need help tooltip timeout, after how many hours show again tooltip?', '0'),
            ('use_secure_cookie','0',0,'Use secure cookie, check this if you want to force SSL all the time', '0'),
            ('faq_email_required','0',0,'Is visitor e-mail required for FAQ', '0'),
            ('disable_print','0',0,'Disable chat print', '0'),
            ('hide_disabled_department','1',0,'Hide disabled department widget', '0'),
            ('disable_send','0',0,'Disable chat transcript send', '0'),
            ('ignore_user_status','0',0,'Ignore users online statuses and use departments online hours', '0'),
            ('bbc_button_visible','1',0,'Show BB Code button', '0'),
            ('allow_reopen_closed','1', 0, 'Allow user to reopen closed chats?', '0'),
            ('reopen_as_new','1', 0, 'Reopen closed chat as new? Otherwise it will be reopened as active.', '0'),
            ('default_theme_id','0', 0, 'Default theme ID.', '1'),  
            ('default_admin_theme_id','0', 0, 'Default admin theme ID', '1'),  
            ('translation_data',	'a:6:{i:0;b:0;s:19:\"translation_handler\";s:4:\"bing\";s:19:\"enable_translations\";b:0;s:14:\"bing_client_id\";s:0:\"\";s:18:\"bing_client_secret\";s:0:\"\";s:14:\"google_api_key\";s:0:\"\";}',	0,	'Translation data',	1),              
            ('disable_html5_storage','1',0,'Disable HMTL5 storage, check it if your site is switching between http and https', '0'),
            ('automatically_reopen_chat','1',0,'Automatically reopen chat on widget open', '0'),
            ('autoclose_timeout','0', 0, 'Automatic chats closing. 0 - disabled, n > 0 time in minutes before chat is automatically closed', '0'),
            ('autopurge_timeout','0', 0, 'Automatic chats purging. 0 - disabled, n > 0 time in minutes before chat is automatically deleted', '0'),
            ('update_ip',	'127.0.0.1',	0,	'Which ip should be allowed to update DB by executing http request, separate by comma?',0),
            ('track_if_offline',	'0',	0,	'Track online visitors even if there is no online operators',0),
            ('min_phone_length','8',0,'Minimum phone number length',0),
            ('mheight','',0,'Messages box height',0),
            ('dashboard_order', 'online_operators,departments_stats|pending_chats,unread_chats,transfered_chats|active_chats,closed_chats', '0', 'Home page dashboard widgets order', '0'),
            ('banned_ip_range','',0,'Which ip should not be allowed to chat',0),
            ('suggest_leave_msg','1',0,'Suggest user to leave a message then user chooses offline department',0),
            ('checkstatus_timeout','0',0,'Interval between chat status checks in seconds, 0 disabled.',0),
            ('show_language_switcher','0',0,'Show users option to switch language at widget',0),
            ('sharing_auto_allow','0',0,'Do not ask permission for users to see their screen',0),
            ('sharing_nodejs_enabled','0',0,'NodeJs support enabled',0),
            ('sharing_nodejs_path','',0,'socket.io path, optional',0),
            ('online_if','0','0','','0'),
            ('track_mouse_activity','0','0','Should mouse movement be tracked as activity measure, if not checked only basic events would be tracked','0'),
            ('track_activity','0','0','Track users activity on site?','0'),
            ('autologin_data','a:3:{i:0;b:0;s:11:\"secret_hash\";s:16:\"please_change_me\";s:7:\"enabled\";i:0;}',0,'Autologin configuration data',	1),
            ('sharing_nodejs_secure','0',0,'Connect to NodeJs in https mode',0),
            ('disable_js_execution','1',0,'Disable JS execution in Co-Browsing operator window',0),
            ('sharing_nodejs_socket_host','',0,'Host where NodeJs is running',0),
            ('hide_right_column_frontpage','0','0','Hide right column in frontpage','0'),
            ('front_tabs', 'dashboard,online_users,online_map', '0', 'Home page tabs order', '0'),
            ('speech_data',	'a:3:{i:0;b:0;s:8:\"language\";i:7;s:7:\"dialect\";s:5:\"en-US\";}',	1,	'',	1),
            ('sharing_nodejs_sllocation','https://cdn.socket.io/socket.io-1.1.0.js',0,'Location of SocketIO JS library',0),
            ('track_is_online','0',0,'Track is user still on site, chat status checks also has to be enabled',0),
            ('show_languages','eng,lit,hrv,esp,por,nld,ara,ger,pol,rus,ita,fre,chn,cse,nor,tur,vnm,idn,sve,per,ell,dnk,rou,bgr,tha,geo,fin,alb',0,'Between what languages user should be able to switch',0),
            ('geoadjustment_data',	'a:8:{i:0;b:0;s:18:\"use_geo_adjustment\";b:0;s:13:\"available_for\";s:0:\"\";s:15:\"other_countries\";s:6:\"custom\";s:8:\"hide_for\";s:0:\"\";s:12:\"other_status\";s:7:\"offline\";s:11:\"rest_status\";s:6:\"hidden\";s:12:\"apply_widget\";i:0;}',	0,	'Geo adjustment settings',	1),
            {$geoRow}");



            $db->query("CREATE TABLE IF NOT EXISTS `lh_chat_online_user` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `vid` varchar(50) NOT NULL,
                `ip` varchar(50) NOT NULL,
                `current_page` text NOT NULL,
                `page_title` varchar(250) NOT NULL,
                `referrer` text NOT NULL,
                `chat_id` int(11) NOT NULL,
                `invitation_seen_count` int(11) NOT NULL,
                `invitation_id` int(11) NOT NULL,
                `last_visit` int(11) NOT NULL,
                `first_visit` int(11) NOT NULL,
                `total_visits` int(11) NOT NULL,
                `pages_count` int(11) NOT NULL,
                `tt_pages_count` int(11) NOT NULL,
                `invitation_count` int(11) NOT NULL,
                `last_check_time` int(11) NOT NULL,
                `dep_id` int(11) NOT NULL,
                `user_agent` varchar(250) NOT NULL,
                `notes` varchar(250) NOT NULL,
                `user_country_code` varchar(50) NOT NULL,
                `user_country_name` varchar(50) NOT NULL,
                `visitor_tz` varchar(50) NOT NULL,
                `operator_message` text NOT NULL,
                `operator_user_proactive` varchar(100) NOT NULL,
                `operator_user_id` int(11) NOT NULL,
                `message_seen` int(11) NOT NULL,
                `message_seen_ts` int(11) NOT NULL,
                `user_active` int(11) NOT NULL,
                `lat` varchar(10) NOT NULL,
                `lon` varchar(10) NOT NULL,
                `city` varchar(100) NOT NULL,
                `reopen_chat` int(11) NOT NULL,
                `time_on_site` int(11) NOT NULL,
                `tt_time_on_site` int(11) NOT NULL,
                `requires_email` int(11) NOT NULL,
                `requires_username` int(11) NOT NULL,
                `requires_phone` int(11) NOT NULL,
                `screenshot_id` int(11) NOT NULL,
                `identifier` varchar(50) NOT NULL,
                `operation` text NOT NULL,
                `online_attr_system` text NOT NULL,
                `operation_chat` text NOT NULL,
                `online_attr` text NOT NULL,
                PRIMARY KEY (`id`),
            KEY `vid` (`vid`),
            KEY `dep_id` (`dep_id`),
            KEY `last_visit_dep_id` (`last_visit`,`dep_id`)
        ) DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `lh_abstract_proactive_chat_invitation` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `siteaccess` varchar(10) NOT NULL,
                `time_on_site` int(11) NOT NULL,
                `pageviews` int(11) NOT NULL,
                `message` text NOT NULL,
                `message_returning` text NOT NULL,
                `executed_times` int(11) NOT NULL,
                `dep_id` int(11) NOT NULL,
                `hide_after_ntimes` int(11) NOT NULL,
                `name` varchar(50) NOT NULL,
                `operator_ids` varchar(100) NOT NULL,
                `wait_message` varchar(250) NOT NULL,
                `timeout_message` varchar(250) NOT NULL,
                `message_returning_nick` varchar(250) NOT NULL,
                `referrer` varchar(250) NOT NULL,
                `wait_timeout` int(11) NOT NULL,
                `show_random_operator` int(11) NOT NULL,
                `operator_name` varchar(100) NOT NULL,
                `position` int(11) NOT NULL,
                `identifier` varchar(50) NOT NULL,
                `requires_email` int(11) NOT NULL,
                `requires_username` int(11) NOT NULL,
                `requires_phone` int(11) NOT NULL,
                `repeat_number` int(11) NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`),
            KEY `time_on_site_pageviews_siteaccess_position` (`time_on_site`,`pageviews`,`siteaccess`,`identifier`,`position`),
            KEY `identifier` (`identifier`),
            KEY `dep_id` (`dep_id`)
        ) DEFAULT CHARSET=utf8;");



            $db->query("CREATE TABLE IF NOT EXISTS `lh_chat_accept` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `chat_id` int(11) NOT NULL,
                `hash` varchar(50) NOT NULL,
                `ctime` int(11) NOT NULL,
                `wused` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `hash` (`hash`)
        ) DEFAULT CHARSET=utf8;");

            //Default departament
            $db->query("CREATE TABLE IF NOT EXISTS `lh_departament` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(100) NOT NULL,
				  `email` varchar(100) NOT NULL,
				  `xmpp_recipients` text NOT NULL,
				  `xmpp_group_recipients` text NOT NULL,
				  `priority` int(11) NOT NULL,
				  `sort_priority` int(11) NOT NULL,
				  `department_transfer_id` int(11) NOT NULL,
				  `transfer_timeout` int(11) NOT NULL,
				  `disabled` int(11) NOT NULL,
				  `hidden` int(11) NOT NULL,
				  `delay_lm` int(11) NOT NULL,
				  `max_active_chats` int(11) NOT NULL,
				  `max_timeout_seconds` int(11) NOT NULL,
				  `identifier` varchar(50) NOT NULL,
				  `mod_start_hour` int(4) NOT NULL,
				  `mod_end_hour` int(4) NOT NULL,
				  `tud_start_hour` int(4) NOT NULL,
				  `tud_end_hour` int(4) NOT NULL,
				  `wed_start_hour` int(4) NOT NULL,
				  `wed_end_hour` int(4) NOT NULL,
				  `thd_start_hour` int(4) NOT NULL,
				  `thd_end_hour` int(4) NOT NULL,
				  `frd_start_hour` int(4) NOT NULL,
				  `frd_end_hour` int(4) NOT NULL,
				  `sad_start_hour` int(4) NOT NULL,
				  `sad_end_hour` int(4) NOT NULL,
				  `sud_start_hour` int(4) NOT NULL,
				  `sud_end_hour` int(4) NOT NULL,
				  `nc_cb_execute` tinyint(1) NOT NULL,
				  `na_cb_execute` tinyint(1) NOT NULL,
				  `inform_unread` tinyint(1) NOT NULL,
				  `active_balancing` tinyint(1) NOT NULL,
				  `visible_if_online` tinyint(1) NOT NULL,
				  `inform_close` int(11) NOT NULL,
				  `inform_unread_delay` int(11) NOT NULL,
				  `inform_options` varchar(250) NOT NULL,
				  `online_hours_active` tinyint(1) NOT NULL,
				  `inform_delay` int(11) NOT NULL,
				  `attr_int_1` int(11) NOT NULL,
				  `attr_int_2` int(11) NOT NULL,
				  `attr_int_3` int(11) NOT NULL,
				  `active_chats_counter` int(11) NOT NULL,
				  `pending_chats_counter` int(11) NOT NULL,
				  `closed_chats_counter` int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `identifier` (`identifier`),
				  KEY `attr_int_1` (`attr_int_1`),
				  KEY `attr_int_2` (`attr_int_2`),
				  KEY `attr_int_3` (`attr_int_3`),
				  KEY `disabled_hidden` (`disabled`, `hidden`),
				  KEY `sort_priority_name` (`sort_priority`, `name`),
				  KEY `active_mod` (`online_hours_active`,`mod_start_hour`,`mod_end_hour`),
				  KEY `active_tud` (`online_hours_active`,`tud_start_hour`,`tud_end_hour`),
				  KEY `active_wed` (`online_hours_active`,`wed_start_hour`,`wed_end_hour`),
				  KEY `active_thd` (`online_hours_active`,`thd_start_hour`,`thd_end_hour`),
				  KEY `active_frd` (`online_hours_active`,`frd_start_hour`,`frd_end_hour`),
				  KEY `active_sad` (`online_hours_active`,`sad_start_hour`,`sad_end_hour`),
				  KEY `active_sud` (`online_hours_active`,`sud_start_hour`,`sud_end_hour`)
				) DEFAULT CHARSET=utf8;");


            $Departament = new erLhcoreClassModelDepartament();
            $Departament->name = $form->DefaultDepartament;
            erLhcoreClassDepartament::getSession()->save($Departament);

            //Department custom work hours
            $db->query("CREATE TABLE IF NOT EXISTS `lh_departament_custom_work_hours` (
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
				) DEFAULT CHARSET=utf8;");

            //Administrators group
            $db->query("CREATE TABLE IF NOT EXISTS `lh_group` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(50) NOT NULL,
                `disabled` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `disabled` (`disabled`)
        ) DEFAULT CHARSET=utf8;");                             

            // Admin group
            $GroupData = new erLhcoreClassModelGroup();
            $GroupData->name    = "Administrators";
            erLhcoreClassUser::getSession()->save($GroupData);

            // Precreate operators group
            $GroupDataOperators = new erLhcoreClassModelGroup();
            $GroupDataOperators->name    = "Operators";
            erLhcoreClassUser::getSession()->save($GroupDataOperators);

            //Administrators role
            $db->query("CREATE TABLE IF NOT EXISTS `lh_role` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(50) NOT NULL,
                PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8;");

            // Administrators role
            $Role = new erLhcoreClassModelRole();
            $Role->name = 'Administrators';
            erLhcoreClassRole::getSession()->save($Role);

            // Operators role
            $RoleOperators = new erLhcoreClassModelRole();
            $RoleOperators->name = 'Operators';
            erLhcoreClassRole::getSession()->save($RoleOperators);

            //Assing group role
            $db->query("CREATE TABLE IF NOT EXISTS `lh_grouprole` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `group_id` int(11) NOT NULL,
                `role_id` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `group_id` (`role_id`,`group_id`),
            KEY `group_id_primary` (`group_id`)
        ) DEFAULT CHARSET=utf8;");

            // Assign admin role to admin group
            $GroupRole = new erLhcoreClassModelGroupRole();
            $GroupRole->group_id =$GroupData->id;
            $GroupRole->role_id = $Role->id;
            erLhcoreClassRole::getSession()->save($GroupRole);

            // Assign operators role to operators group
            $GroupRoleOperators = new erLhcoreClassModelGroupRole();
            $GroupRoleOperators->group_id =$GroupDataOperators->id;
            $GroupRoleOperators->role_id = $RoleOperators->id;
            erLhcoreClassRole::getSession()->save($GroupRoleOperators);

            // Users
            $db->query("CREATE TABLE IF NOT EXISTS `lh_users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(40) NOT NULL,
                `password` varchar(200) NOT NULL,
                `email` varchar(100) NOT NULL,
                `time_zone` varchar(100) NOT NULL,
                `name` varchar(100) NOT NULL,
                `surname` varchar(100) NOT NULL,
                `filepath` varchar(200) NOT NULL,
                `filename` varchar(200) NOT NULL,
                `job_title` varchar(100) NOT NULL,
                `departments_ids` varchar(100) NOT NULL,
                `chat_nickname` varchar(100) NOT NULL,
                `xmpp_username` varchar(200) NOT NULL,
                `session_id` varchar(40) NOT NULL,
                `skype` varchar(50) NOT NULL,
                `disabled` tinyint(4) NOT NULL,
                `hide_online` tinyint(1) NOT NULL,
                `all_departments` tinyint(1) NOT NULL,
                `invisible_mode` tinyint(1) NOT NULL,
                `rec_per_req` tinyint(1) NOT NULL,
                `active_chats_counter` int(11) NOT NULL,
                `closed_chats_counter` int(11) NOT NULL,
                `pending_chats_counter` int(11) NOT NULL,
                `attr_int_1` int(11) NOT NULL,
                `attr_int_2` int(11) NOT NULL,
                `attr_int_3` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `hide_online` (`hide_online`),
            KEY `rec_per_req` (`rec_per_req`),
            KEY `email` (`email`),
            KEY `xmpp_username` (`xmpp_username`)
        ) DEFAULT CHARSET=utf8;");

            $UserData = new erLhcoreClassModelUser();

            $UserData->setPassword($form->AdminPassword);
            $UserData->email   = $form->AdminEmail;
            $UserData->name    = $form->AdminName;
            $UserData->surname = $form->AdminSurname;
            $UserData->username = $form->AdminUsername;
            $UserData->all_departments = 1;
            $UserData->departments_ids = 0;

            erLhcoreClassUser::getSession()->save($UserData);

            // User departaments
            $db->query("CREATE TABLE IF NOT EXISTS `lh_userdep` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `dep_id` int(11) NOT NULL,
                `last_activity` int(11) NOT NULL,
                `hide_online` int(11) NOT NULL,
                `last_accepted` int(11) NOT NULL,
                `active_chats` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`),
            KEY `last_activity_hide_online_dep_id` (`last_activity`,`hide_online`,`dep_id`),
            KEY `dep_id` (`dep_id`)
        ) DEFAULT CHARSET=utf8;");

            // Insert record to departament instantly
            $db->query("INSERT INTO `lh_userdep` (`user_id`,`dep_id`,`last_activity`,`hide_online`,`last_accepted`,`active_chats`) VALUES ({$UserData->id},0,0,0,0,0)");

            // Transfer chat
            $db->query("CREATE TABLE IF NOT EXISTS `lh_transfer` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `chat_id` int(11) NOT NULL,
                `dep_id` int(11) NOT NULL,
                `transfer_user_id` int(11) NOT NULL,
                `from_dep_id` int(11) NOT NULL,
                `transfer_to_user_id` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `dep_id` (`dep_id`),
            KEY `transfer_user_id_dep_id` (`transfer_user_id`,`dep_id`),
            KEY `transfer_to_user_id` (`transfer_to_user_id`)
        ) DEFAULT CHARSET=utf8;");

            // Remember user table
            $db->query("CREATE TABLE IF NOT EXISTS `lh_users_remember` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `mtime` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8;");

            // API table
            $db->query("CREATE TABLE IF NOT EXISTS `lh_abstract_rest_api_key` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `api_key` varchar(50) NOT NULL,
                `user_id` int(11) NOT NULL,
                `active` int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
            KEY `api_key` (`api_key`),
            KEY `user_id` (`user_id`)
        ) DEFAULT CHARSET=utf8;");

            // Chat messages
            $db->query("CREATE TABLE IF NOT EXISTS `lh_msg` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `msg` text NOT NULL,
                `time` int(11) NOT NULL,
                `chat_id` int(11) NOT NULL DEFAULT '0',
                `user_id` int(11) NOT NULL DEFAULT '0',
                `name_support` varchar(100) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `chat_id_id` (`chat_id`, `id`)
        ) DEFAULT CHARSET=utf8;");

            // Forgot password table
            $db->query("CREATE TABLE IF NOT EXISTS `lh_forgotpasswordhash` (
                `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `user_id` INT NOT NULL ,
                `hash` VARCHAR( 40 ) NOT NULL ,
                `created` INT NOT NULL
            ) DEFAULT CHARSET=utf8;");

            // User groups table
            $db->query("CREATE TABLE IF NOT EXISTS `lh_groupuser` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `group_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `group_id` (`group_id`),
            KEY `user_id` (`user_id`),
            KEY `group_id_2` (`group_id`,`user_id`)
        ) DEFAULT CHARSET=utf8;");

            // Assign admin user to admin group
            $GroupUser = new erLhcoreClassModelGroupUser();
            $GroupUser->group_id = $GroupData->id;
            $GroupUser->user_id = $UserData->id;
            erLhcoreClassUser::getSession()->save($GroupUser);

            //Assign default role functions
            $db->query("CREATE TABLE IF NOT EXISTS `lh_rolefunction` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `role_id` int(11) NOT NULL,
                `module` varchar(100) NOT NULL,
                `function` varchar(100) NOT NULL,
                PRIMARY KEY (`id`),
            KEY `role_id` (`role_id`)
        ) DEFAULT CHARSET=utf8;");

            // Admin role and function
            $RoleFunction = new erLhcoreClassModelRoleFunction();
            $RoleFunction->role_id = $Role->id;
            $RoleFunction->module = '*';
            $RoleFunction->function = '*';
            erLhcoreClassRole::getSession()->save($RoleFunction);

            // Operators rules and functions
            $permissionsArray = array(
                array('module' => 'lhuser',  'function' => 'selfedit'),
                array('module' => 'lhuser',  'function' => 'changeonlinestatus'),
                array('module' => 'lhuser',  'function' => 'changeskypenick'),
                array('module' => 'lhuser',  'function' => 'personalcannedmsg'),
                array('module' => 'lhuser',  'function' => 'change_visibility_list'),
                array('module' => 'lhuser',  'function' => 'see_assigned_departments'),
                array('module' => 'lhuser',  'function' => 'canseedepartmentstats'),
                array('module' => 'lhchat',  'function' => 'use'),
                array('module' => 'lhchat',  'function' => 'chattabschrome'),
                array('module' => 'lhchat',  'function' => 'singlechatwindow'),
                array('module' => 'lhchat',  'function' => 'allowopenremotechat'),
                array('module' => 'lhchat',  'function' => 'allowchattabs'),
                array('module' => 'lhchat',  'function' => 'use_onlineusers'),
                array('module' => 'lhchat',  'function' => 'take_screenshot'),
                array('module' => 'lhfront', 'function' => 'use'),
                array('module' => 'lhsystem','function' => 'use'),
                array('module' => 'lhtranslation','function' => 'use'),
                array('module' => 'lhchat',  'function' => 'allowblockusers'),
                array('module' => 'lhsystem','function' => 'generatejs'),
                array('module' => 'lhsystem','function' => 'changelanguage'),
                array('module' => 'lhchat',  'function' => 'allowredirect'),
                array('module' => 'lhchat',  'function' => 'allowtransfer'),
                array('module' => 'lhchat',  'function' => 'administratecannedmsg'),
                array('module' => 'lhchat',  'function' => 'sees_all_online_visitors'),
                array('module' => 'lhpermission',   'function' => 'see_permissions'),
                array('module' => 'lhquestionary',  'function' => 'manage_questionary'),
                array('module' => 'lhfaq',   		'function' => 'manage_faq'),
                array('module' => 'lhchatbox',   	'function' => 'manage_chatbox'),
                array('module' => 'lhbrowseoffer',  'function' => 'manage_bo'),
                array('module' => 'lhxml',   		'function' => '*'),
                array('module' => 'lhcobrowse',   	'function' => 'browse'),
                array('module' => 'lhfile',   		'function' => 'use_operator'),
                array('module' => 'lhfile',   		'function' => 'file_delete_chat'),
                array('module' => 'lhstatistic',   	'function' => 'use'),
                array('module' => 'lhspeech', 'function' => 'changedefaultlanguage'),
                array('module' => 'lhspeech', 'function' => 'use'),
                array('module' => 'lhspeech', 'function' => 'change_chat_recognition'),
            );

            foreach ($permissionsArray as $paramsPermission) {
                $RoleFunctionOperator = new erLhcoreClassModelRoleFunction();
                $RoleFunctionOperator->role_id = $RoleOperators->id;
                $RoleFunctionOperator->module = $paramsPermission['module'];
                $RoleFunctionOperator->function = $paramsPermission['function'];
                erLhcoreClassRole::getSession()->save($RoleFunctionOperator);
            }

            $cfgSite = erConfigClassLhConfig::getInstance();
            $cfgSite->setSetting( 'site', 'installed', true);
            $cfgSite->setSetting( 'site', 'templatecache', true);
            $cfgSite->setSetting( 'site', 'templatecompile', true);
            $cfgSite->setSetting( 'site', 'modulecompile', true);
            return true;
        } else {
            return $Errors;
        }
    }

    function step4() {
        $cfgSite = erConfigClassLhConfig::getInstance();
        $cfgSite->save();
        $msg = "Installation is complete. You can start by adding users and departments";
        syslog(LOG_DEBUG, $msg);
    }

    function print_errors($errors) {
        foreach($errors as $error) {
            syslog(LOG_ERR, "ERROR: ".$error);
        }
        exit(-1);
    }

    private function file_perms($file, $octal = true) {
        if(!file_exists($file)) return false;

        $perms = fileperms($file);

        $cut = $octal ? 2 : 3;

        return substr(decoct($perms), $cut);
    }
    private function file_is_writable($directories, $preffix = '', &$Errors) {
        foreach ($directories as $directory) {
            $error = false;
            syslog(LOG_DEBUG, "Evaluate $directory if writable");
            $owner = fileowner($preffix.$directory);
            $group = filegroup($preffix.$directory);
            $permission = $this->file_perms($preffix.$directory);
            if ($permission[2] == 7) {
                continue;
            }
            if ($owner == 33 and $permission[0] != 7) {
                $error = true;
            }
            if ($group == 33 and $permission[1] != 7) {
                $error = true;
            }
            if ($error) {
                $Errors[] = $preffix.$directory." is not writable";
            }
        }
    }

    private function _scandir($directory) {
        $directories = scandir($directory);
        $directories = array_diff($directories, ['.']);
        $directories = array_diff($directories, ['..']);
        return $directories;
    }
}
