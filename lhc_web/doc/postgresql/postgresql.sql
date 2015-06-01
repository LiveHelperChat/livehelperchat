-- Converted by db_converter
START TRANSACTION;
SET standard_conforming_strings=off;
SET escape_string_warning=off;
SET CONSTRAINTS ALL DEFERRED;

CREATE TABLE "lh_abstract_auto_responder" (
    "id" integer NOT NULL,
    "siteaccess" varchar(6) NOT NULL,
    "wait_message" varchar(500) NOT NULL,
    "wait_timeout" integer NOT NULL,
    "position" integer NOT NULL,
    "repeat_number" integer NOT NULL,
    "dep_id" integer NOT NULL,
    "timeout_message" varchar(500) NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_abstract_widget_theme" (
  "id" integer NOT NULL,
  "show_copyright" integer NOT NULL,
  "widget_border_width" integer NOT NULL,
  "header_padding" integer NOT NULL,
  "header_height" integer NOT NULL,
  "hide_popup" integer NOT NULL,
  "hide_close" integer NOT NULL,
  "name" varchar(250) NOT NULL,
  "popup_image_path" varchar(250) NOT NULL,
  "popup_image" varchar(250) NOT NULL,
  "close_image_path" varchar(250) NOT NULL,
  "close_image" varchar(250) NOT NULL,
  "restore_image_path" varchar(250) NOT NULL,
  "restore_image" varchar(250) NOT NULL,
  "minimize_image_path" varchar(250) NOT NULL,
  "minimize_image" varchar(250) NOT NULL,
  "onl_bcolor" varchar(10) NOT NULL,
  "text_color" varchar(10) NOT NULL,
  "online_image" varchar(250) NOT NULL,
  "name_company" varchar(250) NOT NULL,
  "online_image_path" varchar(250) NOT NULL,
  "offline_image" varchar(250) NOT NULL,
  "offline_image_path" varchar(250) NOT NULL,
  "logo_image" varchar(250) NOT NULL,
  "logo_image_path" varchar(250) NOT NULL,
  "intro_operator_text" varchar(250) NOT NULL,
  "operator_image" varchar(250) NOT NULL,
  "operator_image_path" varchar(250) NOT NULL,
  "support_joined" varchar(250) NOT NULL,
  "support_closed" varchar(250) NOT NULL,
  "pending_join" varchar(250) NOT NULL,
  "noonline_operators" varchar(250) NOT NULL,
  "noonline_operators_offline" varchar(250) NOT NULL,
  "bor_bcolor" varchar(10) NOT NULL,
  "need_help_image" varchar(250) NOT NULL,
  "header_background" varchar(10) NOT NULL,
  "widget_border_color" varchar(10) NOT NULL,
  "need_help_tcolor" varchar(10) NOT NULL,
  "need_help_bcolor" varchar(10) NOT NULL,
  "need_help_border" varchar(10) NOT NULL,
  "need_help_close_bg" varchar(10) NOT NULL,
  "need_help_hover_bg" varchar(10) NOT NULL,
  "need_help_close_hover_bg" varchar(10) NOT NULL,
  "need_help_image_path" varchar(250) NOT NULL,
  "copyright_image" varchar(250) NOT NULL,
  "copyright_image_path" varchar(250) NOT NULL,
  "widget_copyright_url" varchar(250) NOT NULL,
  "custom_status_css" text NOT NULL,
  "custom_container_css" text NOT NULL,
  "custom_widget_css" text NOT NULL,
  "explain_text" text NOT NULL,
  "need_help_header" varchar(250) NOT NULL,
  "need_help_text" varchar(250) NOT NULL,
  "online_text" varchar(250) NOT NULL,
  "offline_text" varchar(250) NOT NULL,
  PRIMARY KEY ("id")
);



CREATE TABLE "lh_abstract_form" (
  "id" integer NOT NULL,
  "name" varchar(100) NOT NULL,
  "content" text NOT NULL,
  "recipient" varchar(250) NOT NULL,
  "active" integer NOT NULL,
  "name_attr" varchar(250) NOT NULL,
  "intro_attr" varchar(250) NOT NULL,
  "xls_columns" text NOT NULL,
  "pagelayout" varchar(200) NOT NULL,
  "post_content" text NOT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE "lh_abstract_form_collected" (
  "id" integer NOT NULL,
  "form_id" integer NOT NULL,
  "ctime" integer NOT NULL,
  "ip" varchar(250) NOT NULL,
  "identifier" varchar(250) NOT NULL,
  "content" text NOT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE "lh_abstract_email_template" (
    "id" integer NOT NULL,
    "name" varchar(500) NOT NULL,
    "from_name" varchar(300) NOT NULL,
    "from_name_ac" integer NOT NULL,
    "from_email" varchar(300) NOT NULL,
    "from_email_ac" integer NOT NULL,    
    "content" text NOT NULL,
    "subject" varchar(500) NOT NULL,
    "subject_ac" integer NOT NULL,
    "reply_to" varchar(300) NOT NULL,
    "reply_to_ac" integer NOT NULL,
    "recipient" varchar(300) NOT NULL,
    "bcc_recipients" varchar(200) NOT NULL,
    "user_mail_as_sender" integer NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "lh_abstract_email_template" VALUES 
(1,'Send mail to user','Live Helper Chat',0,'',0,'Dear {user_chat_nick},\r\n\r\n{additional_message}\r\n\r\nLive Support response:\r\n{messages_content}\r\n\r\nSincerely,\r\nLive Support Team\r\n','{name_surname} has responded to your request',1,'',1,'','',0),
(2,'Support request from user','',0,'',0,'Hello,\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nAdditional data, if any:\r\n{additional_data}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nLink to chat if any:\r\n{prefillchat}\r\n\r\nSincerely,\r\nLive Support Team','Support request from user',0,'',0,'admin@example.com','',0),
(3,'User mail for himself','Live Helper Chat',0,'',0,'Dear {user_chat_nick},\r\n\r\nTranscript:\r\n{messages_content}\r\n\r\nSincerely,\r\nLive Support Team\r\n','Chat transcript',0,'',0,'','',0),
(4,'New chat request','Live Helper Chat',0,'',0,'Hello,\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nClick to accept chat automatically\r\n{url_accept}\r\n\r\nSincerely,\r\nLive Support Team','New chat request',0,'',0,'admin@example.com','',0),
(5,'Chat was closed','Live Helper Chat',0,'',0,'Hello,\r\n\r\n{operator} has closed a chat\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nAdditional data, if any:\r\n{additional_data}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nSincerely,\r\nLive Support Team','Chat was closed',0,'',0,'','',0),
(6,'New FAQ question','Live Helper Chat',0,'',0,'Hello,\r\n\r\nNew FAQ question\r\nEmail: {email}\r\n\r\nQuestion:\r\n{question}\r\n\r\nURL to answer a question:\r\n{url_request}\r\n\r\nSincerely,\r\nLive Support Team',	'New FAQ question',0,'',0,'','',0),
(7,'New unread message','Live Helper Chat',0,'',0,'Hello,\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nClick to accept chat automatically\r\n{url_accept}\r\n\r\nSincerely,\r\nLive Support Team',	'New unread message',	0,	'',	0,	'',	'',0),
(8,'Filled form','Live Helper Chat',0,'',0,'Hello,\r\n\r\nUser has filled a form\r\nForm name - {form_name}\r\nUser IP - {ip}\r\nDownload filled data - {url_download}\r\n\r\nSincerely,\r\nLive Support Team','Filled form - {form_name}',	0,	'',	0,	'',	'',0),
(9,	'Chat was accepted',	'Live Helper Chat',	0,	'',	0,	'Hello,\r\n\r\nOperator {user_name} has accepted a chat [{chat_id}]\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nClick to accept chat automatically\r\n{url_accept}\r\n\r\nSincerely,\r\nLive Support Team','Chat was accepted [{chat_id}]',	0,	'',	0,	'',	'', 0),
(10,'Permission request',	'Live Helper Chat',	0,	'',	0,	'Hello,\r\n\r\nOperator {user} has requested these permissions\n\r\n{permissions}\r\n\r\nSincerely,\r\nLive Support Team','Permission request from {user}',	0,	'',	0,	'',	'',	0);

CREATE TABLE "lh_abstract_proactive_chat_invitation" (
    "id" integer NOT NULL,
    "siteaccess" varchar(20) NOT NULL,
    "time_on_site" integer NOT NULL,
    "pageviews" integer NOT NULL,
    "dep_id" integer NOT NULL,
    "message" text NOT NULL,
    "message_returning" text NOT NULL,
    "executed_times" integer NOT NULL,
    "requires_phone" integer NOT NULL,
    "hide_after_ntimes" integer NOT NULL,
    "repeat_number" integer NOT NULL,
    "name" varchar(100) NOT NULL,
    "operator_ids" varchar(100) NOT NULL,
    "message_returning_nick" varchar(100) NOT NULL,
    "wait_message" varchar(500) NOT NULL,
    "timeout_message" varchar(500) NOT NULL,
    "referrer" varchar(500) NOT NULL,
    "wait_timeout" integer NOT NULL,
    "show_random_operator" integer NOT NULL,
    "requires_username" integer NOT NULL,
    "operator_name" varchar(200) NOT NULL,
    "position" integer NOT NULL,
    "identifier" varchar(100) NOT NULL,
    "requires_email" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_canned_msg" (
    "id" integer NOT NULL,
    "msg" text NOT NULL,
    "fallback_msg" text NOT NULL,
    "title" varchar(200) NOT NULL,
    "explain" varchar(200) NOT NULL,
    "position" integer NOT NULL,
    "department_id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "delay" integer NOT NULL,
    "auto_send" integer NOT NULL,
    "attr_int_1" integer NOT NULL,
    "attr_int_2" integer NOT NULL,
    "attr_int_3" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_chat" (
    "id" integer NOT NULL,
    "nick" varchar(100) NOT NULL,
    "status" integer NOT NULL DEFAULT '0',
    "status_sub" integer NOT NULL DEFAULT '0',
    "time" integer NOT NULL,
    "user_id" integer NOT NULL,
    "hash" varchar(80) NOT NULL,
    "referrer" text NOT NULL,
    "session_referrer" text NOT NULL,
    "chat_variables" text NOT NULL,
    "remarks" text NOT NULL,
    "ip" varchar(200) NOT NULL,
    "user_tz_identifier" varchar(50) NOT NULL,
    "chat_locale" varchar(10) NOT NULL,
    "chat_locale_to" varchar(10) NOT NULL,
    "dep_id" integer NOT NULL,
    "user_closed_ts" integer NOT NULL,
    "user_status" integer NOT NULL DEFAULT '0',
    "support_informed" integer NOT NULL DEFAULT '0',
    "email" varchar(200) NOT NULL,
    "country_code" varchar(200) NOT NULL,
    "country_name" varchar(200) NOT NULL,
    "operation" text NOT NULL,
    "operation_admin" varchar(150) NOT NULL,
    "user_typing" integer NOT NULL,
    "wait_timeout_repeat" integer NOT NULL,
    "user_typing_txt" varchar(100) NOT NULL,
    "operator_typing" integer NOT NULL,
    "unread_messages_informed" integer NOT NULL,
    "reinform_timeout" integer NOT NULL,
    "operator_typing_id" integer NOT NULL,
    "screenshot_id" integer NOT NULL,
    "phone" varchar(200) NOT NULL,
    "has_unread_messages" integer NOT NULL,
    "last_user_msg_time" integer NOT NULL,
    "fbst" integer NOT NULL,
    "online_user_id" integer NOT NULL,
    "last_msg_id" integer NOT NULL,
    "additional_data" varchar(500) NOT NULL,
    "timeout_message" varchar(500) NOT NULL,
    "lat" varchar(20) NOT NULL,
    "lon" varchar(20) NOT NULL,
    "city" varchar(200) NOT NULL,
    "mail_send" integer NOT NULL,
    "wait_time" integer NOT NULL,
    "wait_timeout" integer NOT NULL,
    "wait_timeout_send" integer NOT NULL,
    "chat_duration" integer NOT NULL,
    "priority" integer NOT NULL,
    "chat_initiator" integer NOT NULL,
    "transfer_timeout_ts" integer NOT NULL,
    "transfer_timeout_ac" integer NOT NULL,
    "transfer_if_na" integer NOT NULL,
    "na_cb_executed" integer NOT NULL,
    "nc_cb_executed" integer NOT NULL,
    "tslasign" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_chat_accept" (
    "id" integer NOT NULL,
    "chat_id" integer NOT NULL,
    "hash" varchar(100) NOT NULL,
    "ctime" integer NOT NULL,
    "wused" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_chat_archive_range" (
    "id" integer NOT NULL,
    "range_from" integer NOT NULL,
    "range_to" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_chat_blocked_user" (
    "id" integer NOT NULL,
    "ip" varchar(200) NOT NULL,
    "user_id" integer NOT NULL,
    "datets" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_speech_language" (
    "id" integer NOT NULL,
    "name" varchar(100) NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_speech_language_dialect" (
    "id" integer NOT NULL,
    "language_id" integer NOT NULL,
    "lang_name" varchar(100) NOT NULL,
    "lang_code" varchar(100) NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_speech_chat_language" (
    "id" integer NOT NULL,
    "chat_id" integer NOT NULL,
    "language_id" integer NOT NULL,
    "dialect" varchar(50) NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_chat_config" (
    "identifier" varchar(100) NOT NULL,
    "value" text NOT NULL,
    "type" smallint NOT NULL DEFAULT '0',
    "explain" varchar(500) NOT NULL,
    "hidden" integer NOT NULL DEFAULT '0',
    PRIMARY KEY ("identifier")
);

INSERT INTO "lh_speech_language" ("id", "name") VALUES
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
        	   (32,	'Lingua latīna');
        	   
INSERT INTO "lh_speech_language_dialect" ("id", "language_id", "lang_name", "lang_code") VALUES
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
                (62,	32,	'Lingua latīna',	'la');
                  
        	   
INSERT INTO "lh_chat_config" VALUES ('autologin_data',	'a:3:{i:0;b:0;s:11:\"secret_hash\";s:16:\"please_change_me\";s:7:\"enabled\";i:0;}',	0,	'Autologin configuration data',	1),('sharing_nodejs_path','',0,'socket.io path, optional',0),('track_is_online','0',0,'Track is user still on site, chat status checks also has to be enabled',0),('speech_data','a:3:{i:0;b:0;s:8:\"language\";i:7;s:7:\"dialect\";s:5:\"en-US\";}',	1,	'',	1),('translation_data',	'a:6:{i:0;b:0;s:19:\"translation_handler\";s:4:\"bing\";s:19:\"enable_translations\";b:0;s:14:\"bing_client_id\";s:0:\"\";s:18:\"bing_client_secret\";s:0:\"\";s:14:\"google_api_key\";s:0:\"\";}',	0,	'Translation data',	1),('front_tabs', 'dashboard,online_users,online_map,pending_chats,active_chats,unread_chats,closed_chats,online_operators', '0', 'Home page tabs order', '0'),('show_languages','eng,lit,hrv,esp,por,nld,ara,ger,pol,rus,ita,fre,chn,cse,nor,tur,vnm,idn,sve,per,ell,dnk,rou,bgr,tha,geo,fin,alb',0,'Between what languages user should be able to switch',0),('show_language_switcher','0',0,'Show users option to switch language at widget',0),('suggest_leave_msg','1',0,'Suggest user to leave a message then user chooses offline department',0),('checkstatus_timeout','0',0,'Interval between chat status checks in seconds, 0 disabled.',0),('mheight','',0,'Messages box height',0),('geoadjustment_data',	'a:8:{i:0;b:0;s:18:\"use_geo_adjustment\";b:0;s:13:\"available_for\";s:0:\"\";s:15:\"other_countries\";s:6:\"custom\";s:8:\"hide_for\";s:0:\"\";s:12:\"other_status\";s:7:\"offline\";s:11:\"rest_status\";s:6:\"hidden\";s:12:\"apply_widget\";i:0;}',	0,	'Geo adjustment settings',	1),('min_phone_length','8',0,'Minimum phone number length',0),('need_help_tip','0',0,'Show need help tooltip?', 0),('accept_chat_link_timeout','300',0,'How many seconds chat accept link is valid. Set 0 to force login all the time manually.',0),('accept_tos_link','#',0,'Change to your site Terms of Service',0),('application_name','a:6:{s:3:\"eng\";s:31:\"Live Helper Chat - live support\";s:3:\"lit\";s:26:\"Live Helper Chat - pagalba\";s:3:\"hrv\";s:0:\"\";s:3:\"esp\";s:0:\"\";s:3:\"por\";s:0:\"\";s:10:\"site_admin\";s:31:\"Live Helper Chat - live support\";}',1,'Support application name, visible in browser title.',0),('chatbox_data','a:6:{i:0;b:0;s:20:\"chatbox_auto_enabled\";i:0;s:19:\"chatbox_secret_hash\";s:9:\"5xuht65rf\";s:20:\"chatbox_default_name\";s:7:\"Chatbox\";s:17:\"chatbox_msg_limit\";i:50;s:22:\"chatbox_default_opname\";s:7:\"Manager\";}',0,'Chatbox configuration',1),('customer_company_name','Live Helper Chat',0,'Your company name - visible in bottom left corner',0),('customer_site_url','http://livehelperchat.com',0,'Your site URL address',0),('disable_popup_restore','0',0,'Disable option in widget to open new window. Restore icon will be hidden',0),('explicit_http_mode','',0,'Please enter explicit http mode. Either http: or https:, do not forget : at the end.',0),('export_hash','9eeq9ntkd',0,'Chats export secret hash',0),('file_configuration','a:7:{i:0;b:0;s:5:\"ft_op\";s:43:\"gif|jpe?g|png|zip|rar|xls|doc|docx|xlsx|pdf\";s:5:\"ft_us\";s:26:\"gif|jpe?g|png|doc|docx|pdf\";s:6:\"fs_max\";i:2048;s:18:\"active_user_upload\";b:0;s:16:\"active_op_upload\";b:1;s:19:\"active_admin_upload\";b:1;}',0,'Files configuration item',1),('geo_data','',0,'',1),('geo_location_data','a:3:{s:4:\"zoom\";i:4;s:3:\"lat\";s:7:\"49.8211\";s:3:\"lng\";s:7:\"11.7835\";}',0,'',1),('ignorable_ip','',0,'Which ip should be ignored in online users list, separate by comma',0),('list_online_operators','0',0,'List online operators.',0),('max_message_length','500',0,'Maximum message length in characters',0),('message_seen_timeout','24',0,'Proactive message timeout in hours. After how many hours proactive chat mesasge should be shown again.',0),('pro_active_invite','0',0,'Is pro active chat invitation active. Online users tracking also has to be enabled',0),('pro_active_limitation','-1',0,'Pro active chats invitations limitation based on pending chats, (-1) do not limit, (0,1,n+1) number of pending chats can be for invitation to be shown.',0),('pro_active_show_if_offline','0',0,'Should invitation logic be executed if there is no online operators',0),('reopen_chat_enabled','1',0,'Reopen chat functionality enabled',0),('run_departments_workflow','0',0,'Should cronjob run departments transfer workflow, even if user leaves a chat',0),('run_unaswered_chat_workflow','0',0,'Should cronjob run unanswered chats workflow and execute unaswered chats callback, 0 - no, any other number bigger than 0 is a minits how long chat have to be not accepted before executing callback.',0),('session_captcha','0',0,'Use session captcha. LHC have to be installed on the same domain or subdomain.',0),('smtp_data','a:5:{s:4:\"host\";s:0:\"\";s:4:\"port\";s:2:\"25\";s:8:\"use_smtp\";i:0;s:8:\"username\";s:0:\"\";s:8:\"password\";s:0:\"\";}',0,'SMTP configuration',1),('sound_invitation','1',0,'Play sound on invitation to chat.',0),('start_chat_data','a:23:{i:0;b:0;s:21:\"name_visible_in_popup\";b:1;s:27:\"name_visible_in_page_widget\";b:1;s:19:\"name_require_option\";s:8:\"required\";s:22:\"email_visible_in_popup\";b:0;s:28:\"email_visible_in_page_widget\";b:0;s:20:\"email_require_option\";s:8:\"required\";s:24:\"message_visible_in_popup\";b:1;s:30:\"message_visible_in_page_widget\";b:1;s:22:\"message_require_option\";s:8:\"required\";s:22:\"phone_visible_in_popup\";b:0;s:28:\"phone_visible_in_page_widget\";b:0;s:20:\"phone_require_option\";s:8:\"required\";s:21:\"force_leave_a_message\";b:0;s:29:\"offline_name_visible_in_popup\";b:1;s:35:\"offline_name_visible_in_page_widget\";b:1;s:27:\"offline_name_require_option\";s:8:\"required\";s:30:\"offline_phone_visible_in_popup\";b:0;s:36:\"offline_phone_visible_in_page_widget\";b:0;s:28:\"offline_phone_require_option\";s:8:\"required\";s:32:\"offline_message_visible_in_popup\";b:1;s:38:\"offline_message_visible_in_page_widget\";b:1;s:30:\"offline_message_require_option\";s:8:\"required\";}',0,'',1),('sync_sound_settings','a:15:{i:0;b:0;s:12:\"repeat_sound\";i:1;s:18:\"repeat_sound_delay\";i:5;s:10:\"show_alert\";b:0;s:22:\"new_chat_sound_enabled\";b:1;s:31:\"new_message_sound_admin_enabled\";b:1;s:30:\"new_message_sound_user_enabled\";b:1;s:14:\"online_timeout\";d:300;s:22:\"check_for_operator_msg\";d:10;s:21:\"back_office_sinterval\";d:10;s:22:\"chat_message_sinterval\";d:3.5;s:20:\"long_polling_enabled\";b:0;s:30:\"polling_chat_message_sinterval\";d:1.5;s:29:\"polling_back_office_sinterval\";d:5;s:18:\"connection_timeout\";i:30;}',0,'',1),('tracked_users_cleanup','160',0,'How many days keep records of online users.',0),('track_domain','',0,'Set your domain to enable user tracking across different domain subdomains.',0),('track_footprint','0',0,'Track users footprint. For this also online visitors tracking should be enabled',0),('track_online_visitors','0',0,'Enable online site visitors tracking',0),('voting_days_limit','7',0,'How many days voting widget should not be expanded after last show',0),('xmp_data','a:9:{i:0;b:0;s:4:\"host\";s:15:\"talk.google.com\";s:6:\"server\";s:9:\"gmail.com\";s:8:\"resource\";s:6:\"xmpphp\";s:4:\"port\";s:4:\"5222\";s:7:\"use_xmp\";i:0;s:8:\"username\";s:0:\"\";s:8:\"password\";s:0:\"\";s:11:\"xmp_message\";s:77:\"You have a new chat request\r\n{messages}\r\nClick to accept a chat\r\n{url_accept}\";}',0,'XMP data',1),('use_secure_cookie','0',0,'Use secure cookie, check this if you want to force SSL all the time',0),('faq_email_required','0',0,'Is visitor e-mail required for FAQ',0),('need_help_tip_timeout','24',0,'Need help tooltip timeout, after how many hours show again tooltip?',0),('disable_print','0',0,'Disable chat print', 0),('disable_send','0',0,'Disable chat transcript send', 0),('hide_disabled_department','1',0,'Hide disabled department widget', 0),('ignore_user_status','0',0,'Ignore users online statuses and use departments online hours',0),('bbc_button_visible','1',0,'Show BB Code button',0),('disable_html5_storage','0',0,'Disable HMTL5 storage, check it if your site is switching between http and https', 0),('automatically_reopen_chat','0',0,'Automatically reopen chat on widget open', 0),('doc_sharer',	'a:10:{i:0;b:0;s:17:\"libre_office_path\";s:20:\"/usr/bin/libreoffice\";s:19:\"supported_extension\";s:51:\"ppt,pptx,doc,odp,docx,xlsx,txt,xls,xlsx,pdf,rtf,odt\";s:18:\"background_process\";i:1;s:13:\"max_file_size\";i:4;s:13:\"pdftoppm_path\";s:17:\"/usr/bin/pdftoppm\";s:13:\"PdftoppmLimit\";i:5;s:14:\"pdftoppm_limit\";i:0;s:14:\"http_user_name\";s:6:\"apache\";s:20:\"http_user_group_name\";s:6:\"apache\";}',	0,	'Libreoffice path',	1),('autoclose_timeout','0', 0, 'Automatic chats closing. 0 - disabled, n > 0 time in minutes before chat is automatically closed', 0),('autopurge_timeout','0', 0, 'Automatic chats purging. 0 - disabled, n > 0 time in minutes before chat is automatically deleted', 0),('allow_reopen_closed','1', 0, 'Allow user to reopen closed chats?', 0),('reopen_as_new','1', 0, 'Reopen closed chat as new? Otherwise it will be reopened as active.', 0),('default_theme_id','0', 0, 'Default theme ID.', 1),('update_ip','127.0.0.1',0,'Which ip should be allowed to update DB by executing http request, separate by comma?',0),('banned_ip_range','',0,'Which ip should not be allowed to chat',0),('track_if_offline','0',0,'Track online visitors even if there is no online operators',0),('sharing_auto_allow','0',0,'Do not ask permission for users to see their screen',0),('sharing_nodejs_enabled','0',0,'NodeJs support enabled',0),('sharing_nodejs_secure','0',0,'Connect to NodeJs in https mode',0),('sharing_nodejs_socket_host','',0,'Host where NodeJs is running',0),('sharing_nodejs_sllocation','https://cdn.socket.io/socket.io-1.1.0.js',0,'Location of SocketIO JS library',0),('disable_js_execution','1',0,'Disable JS execution in Co-Browsing operator window',0),('dashboard_order', 'online_operators,departments_stats|pending_chats,unread_chats|active_chats,closed_chats', '0', 'Home page dashboard widgets order', '0');


CREATE TABLE "lh_chat_file" (
    "id" integer  NOT NULL,
    "name" varchar(510) NOT NULL,
    "upload_name" varchar(510) NOT NULL,
    "size" integer NOT NULL,
    "type" varchar(510) NOT NULL,
    "file_path" varchar(510) NOT NULL,
    "extension" varchar(510) NOT NULL,
    "chat_id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "online_user_id" integer NOT NULL,
    "date" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_cobrowse" (
  "id" integer NOT NULL,
  "chat_id" integer NOT NULL,
  "mtime" integer NOT NULL,
  "online_user_id" integer NOT NULL,
  "url" varchar(250) NOT NULL,
  "initialize" text NOT NULL,
  "modifications" text NOT NULL,
  "finished" smallint NOT NULL,
  "w" integer NOT NULL,
  "wh" integer NOT NULL,
  "x" integer NOT NULL,
  "y" integer NOT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE "lh_chat_online_user" (
    "id" integer NOT NULL,
    "vid" varchar(100) NOT NULL,
    "ip" varchar(100) NOT NULL,
    "current_page" text NOT NULL,
    "page_title" text NOT NULL,
    "referrer" text NOT NULL,
    "chat_id" integer NOT NULL,
    "invitation_seen_count" integer NOT NULL,
    "invitation_id" integer NOT NULL,
    "last_visit" integer NOT NULL,
    "first_visit" integer NOT NULL,
    "total_visits" integer NOT NULL,
    "pages_count" integer NOT NULL,
    "requires_phone" integer NOT NULL,
    "tt_pages_count" integer NOT NULL,
    "invitation_count" integer NOT NULL,
    "requires_username" integer NOT NULL,
    "dep_id" integer NOT NULL,
    "user_agent" text NOT NULL,
    "user_country_code" varchar(100) NOT NULL,
    "user_country_name" varchar(100) NOT NULL,
    "visitor_tz" varchar(50) NOT NULL,
    "online_attr" varchar(250) NOT NULL,
    "operator_message" text NOT NULL,
    "operator_user_proactive" varchar(200) NOT NULL,
    "operation" text NOT NULL,
    "online_attr_system" text NOT NULL,
    "operation_chat" text NOT NULL,
    "notes" varchar(250) NOT NULL,
    "operator_user_id" integer NOT NULL,
    "message_seen" integer NOT NULL,
    "message_seen_ts" integer NOT NULL,
    "screenshot_id" integer NOT NULL,
    "last_check_time" integer NOT NULL,
    "lat" varchar(20) NOT NULL,
    "lon" varchar(20) NOT NULL,
    "city" varchar(200) NOT NULL,
    "reopen_chat" integer NOT NULL,
    "time_on_site" integer NOT NULL,
    "tt_time_on_site" integer NOT NULL,
    "requires_email" integer NOT NULL,
    "identifier" varchar(100) NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_chat_online_user_footprint" (
    "id" integer NOT NULL,
    "chat_id" integer NOT NULL,
    "online_user_id" integer NOT NULL,
    "page" varchar(500) NOT NULL,
    "vtime" varchar(500) NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_chatbox" (
    "id" integer NOT NULL,
    "identifier" varchar(100) NOT NULL,
    "name" varchar(200) NOT NULL,
    "chat_id" integer NOT NULL,
    "active" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_departament" (
    "id" integer NOT NULL,
    "name" varchar(200) NOT NULL,
    "email" varchar(200) NOT NULL,
    "xmpp_recipients" varchar(500) NOT NULL,
    "xmpp_group_recipients" varchar(500) NOT NULL,
    "priority" integer NOT NULL,
    "department_transfer_id" integer NOT NULL,
    "transfer_timeout" integer NOT NULL,
    "disabled" integer NOT NULL,
    "hidden" integer NOT NULL,
    "delay_lm" integer NOT NULL,
    "inform_unread" integer NOT NULL,
    "inform_unread_delay" integer NOT NULL,
    "identifier" varchar(100) NOT NULL,
    "mod" smallint NOT NULL,
    "tud" smallint NOT NULL,
    "wed" smallint NOT NULL,
    "thd" smallint NOT NULL,
    "frd" smallint NOT NULL,
    "sad" smallint NOT NULL,
    "sud" smallint NOT NULL,
    "start_hour" integer NOT NULL,
    "end_hour" integer NOT NULL,
    "inform_close" integer NOT NULL,
    "inform_options" varchar(500) NOT NULL,
    "online_hours_active" smallint NOT NULL,
    "inform_delay" integer NOT NULL,
    "nc_cb_execute" integer NOT NULL,
    "na_cb_execute" integer NOT NULL,
    "active_balancing" smallint NOT NULL,
    "max_active_chats" integer NOT NULL,
    "max_timeout_seconds" integer NOT NULL,
    "attr_int_1" integer NOT NULL,
    "attr_int_2" integer NOT NULL,
    "attr_int_3" integer NOT NULL,
    "active_chats_counter" integer NOT NULL,
    "pending_chats_counter" integer NOT NULL,
    "closed_chats_counter" integer NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "lh_departament" VALUES (1,'Support','','','',0,0,0,0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0,'',0,0,0,0,0,0,0,0,0,0);

CREATE TABLE "lh_abstract_browse_offer_invitation" (
  "id" integer NOT NULL,
  "siteaccess" varchar(10) NOT NULL,
  "time_on_site" integer NOT NULL,
  "content" text NOT NULL,
  "callback_content" text NOT NULL,
  "lhc_iframe_content" smallint NOT NULL,
  "custom_iframe_url" varchar(250) NOT NULL,
  "name" varchar(250) NOT NULL,
  "identifier" varchar(50) NOT NULL,
  "executed_times" integer NOT NULL,
  "url" varchar(250) NOT NULL,
  "active" smallint NOT NULL,
  "has_url" smallint NOT NULL,
  "is_wildcard" smallint NOT NULL,
  "referrer" varchar(250) NOT NULL,
  "priority" varchar(250) NOT NULL,
  "hash" varchar(40) NOT NULL,
  "width" integer NOT NULL,
  "height" integer NOT NULL,
  "unit" varchar(10) NOT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE "lh_faq" (
    "id" integer NOT NULL,
    "question" varchar(500) NOT NULL,
    "answer" text NOT NULL,
    "url" varchar(500) NOT NULL,
    "email" varchar(50) NOT NULL,
    "identifier" varchar(10) NOT NULL,
    "active" integer NOT NULL,
    "has_url" smallint NOT NULL,
    "is_wildcard" smallint NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_forgotpasswordhash" (
    "id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "hash" varchar(80) NOT NULL,
    "created" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_group" (
    "id" integer NOT NULL,
    "disabled" integer NOT NULL,
    "name" varchar(100) NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "lh_group" VALUES (1,0,'Administrators'),(2,0,'Operators');
CREATE TABLE "lh_grouprole" (
    "id" integer NOT NULL,
    "group_id" integer NOT NULL,
    "role_id" integer NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "lh_grouprole" VALUES (1,1,1),(2,2,2);
CREATE TABLE "lh_groupuser" (
    "id" integer NOT NULL,
    "group_id" integer NOT NULL,
    "user_id" integer NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "lh_groupuser" VALUES (1,1,1);
CREATE TABLE "lh_msg" (
    "id" integer NOT NULL,
    "msg" text NOT NULL,
    "time" integer NOT NULL,
    "chat_id" integer NOT NULL DEFAULT '0',
    "user_id" integer NOT NULL DEFAULT '0',
    "name_support" varchar(200) NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_question" (
    "id" integer NOT NULL,
    "question" varchar(500) NOT NULL,
    "location" varchar(500) NOT NULL,
    "active" integer NOT NULL,
    "priority" integer NOT NULL,
    "is_voting" integer NOT NULL,
    "question_intro" text NOT NULL,
    "revote" integer NOT NULL DEFAULT '0',
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_question_answer" (
    "id" integer NOT NULL,
    "ip" bigint NOT NULL,
    "question_id" integer NOT NULL,
    "answer" text NOT NULL,
    "ctime" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_question_option" (
    "id" integer NOT NULL,
    "question_id" integer NOT NULL,
    "option_name" varchar(500) NOT NULL,
    "priority" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_question_option_answer" (
    "id" integer NOT NULL,
    "question_id" integer NOT NULL,
    "option_id" integer NOT NULL,
    "ctime" integer NOT NULL,
    "ip" bigint NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_role" (
    "id" integer NOT NULL,
    "name" varchar(100) NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "lh_role" VALUES (1,'Administrators'),(2,'Operators');
CREATE TABLE "lh_rolefunction" (
    "id" integer NOT NULL,
    "role_id" integer NOT NULL,
    "module" varchar(200) NOT NULL,
    "function" varchar(200) NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "lh_rolefunction" VALUES (1,1,'*','*'),(2,2,'lhuser','selfedit'),(3,2,'lhuser','changeonlinestatus'),(4,2,'lhuser','changeskypenick'),(5,2,'lhuser','personalcannedmsg'),(6,2,'lhchat','use'),(7,2,'lhchat','chattabschrome'),(8,2,'lhchat','singlechatwindow'),(9,2,'lhchat','allowopenremotechat'),(10,2,'lhchat','allowchattabs'),(11,2,'lhchat','use_onlineusers'),(12,2,'lhfront','use'),(13,2,'lhsystem','use'),(14,2,'lhchat','allowblockusers'),(15,2,'lhsystem','generatejs'),(16,2,'lhsystem','changelanguage'),(17,2,'lhchat','allowtransfer'),(18,2,'lhchat','administratecannedmsg'),(19,2,'lhquestionary','manage_questionary'),(20,2,'lhfaq','manage_faq'),(21,2,'lhchatbox','manage_chatbox'),(22,2,'lhxml','*'),(23,2,'lhfile','use_operator'),(24,2,'lhfile','file_delete_chat');
CREATE TABLE "lh_transfer" (
    "id" integer NOT NULL,
    "chat_id" integer NOT NULL,
    "dep_id" integer NOT NULL,
    "transfer_user_id" integer NOT NULL,
    "from_dep_id" integer NOT NULL,
    "transfer_to_user_id" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_userdep" (
    "id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "dep_id" integer NOT NULL,
    "last_activity" integer NOT NULL,
    "hide_online" integer NOT NULL,
    "last_accepted" integer NOT NULL,
    "active_chats" integer NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "lh_userdep" VALUES (1,1,0,1391881558,0,0,0);
CREATE TABLE "lh_users" (
    "id" integer NOT NULL,
    "username" varchar(80) NOT NULL,
    "password" varchar(80) NOT NULL,
    "email" varchar(200) NOT NULL,
    "time_zone" varchar(200) NOT NULL,
    "name" varchar(200) NOT NULL,
    "surname" varchar(200) NOT NULL,    
    "job_title" varchar(100) NOT NULL,
    "session_id" varchar(40) NOT NULL,
    "filepath" varchar(400) NOT NULL,
    "filename" varchar(400) NOT NULL,
    "xmpp_username" varchar(400) NOT NULL,
    "departments_ids" varchar(100) NOT NULL,
    "skype" varchar(100) NOT NULL,
    "disabled" integer NOT NULL,
    "active_chats_counter" integer NOT NULL,
    "closed_chats_counter" integer NOT NULL,
    "pending_chats_counter" integer NOT NULL,
    "hide_online" smallint NOT NULL,
    "invisible_mode" smallint NOT NULL,
    "all_departments" smallint NOT NULL,
    "rec_per_req" smallint NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "lh_users" VALUES (1,'admin','44f7fa28bdd2ffbd74dbde0684728bb6dc132178','admin@example.com','','Support','','','','','','','',0,0,0,1,0);

CREATE TABLE "lh_users_remember" (
    "id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "mtime" integer NOT NULL,
    PRIMARY KEY ("id")
);

CREATE TABLE "lh_users_setting" (
    "id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "identifier" varchar(100) NOT NULL,
    "value" varchar(100) NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "lh_users_setting" VALUES (1,1,'user_language','en_EN'),(2,1,'enable_pending_list','1'),(3,1,'enable_active_list','1'),(4,1,'enable_close_list','0'),(5,1,'enable_unread_list','1'),(6,1,'new_chat_sound','1'),(7,1,'chat_message','1');
CREATE TABLE "lh_users_setting_option" (
    "identifier" varchar(100) NOT NULL,
    "class" varchar(100) NOT NULL,
    "attribute" varchar(80) NOT NULL,
    PRIMARY KEY ("identifier")
);

INSERT INTO "lh_users_setting_option" VALUES ('omap_mtimeout', '', ''),('omap_depid', '', ''),('ogroup_by', '', ''),('omax_rows', '', ''),('o_department', '', ''),('ouser_timeout', '', ''),('oupdate_timeout', '', ''),('chat_message','',''),('enable_active_list','',''),('enable_close_list','',''),('enable_pending_list','',''),('enable_unread_list','',''),('new_chat_sound','',''),('new_user_bn', '', ''),('new_user_sound', '', '');

-- Post-data save --
COMMIT;
START TRANSACTION;

-- Sequences --
DROP SEQUENCE IF EXISTS lh_abstract_auto_responder_id_seq;
CREATE SEQUENCE lh_abstract_auto_responder_id_seq;
SELECT setval('lh_abstract_auto_responder_id_seq', max(id)) FROM lh_abstract_auto_responder;
ALTER TABLE "lh_abstract_auto_responder" ALTER COLUMN "id" SET DEFAULT nextval('lh_abstract_auto_responder_id_seq');

DROP SEQUENCE IF EXISTS lh_speech_language_id_seq;
CREATE SEQUENCE lh_speech_language_id_seq;
SELECT setval('lh_speech_language_id_seq', max(id)) FROM lh_speech_language;
ALTER TABLE "lh_speech_language" ALTER COLUMN "id" SET DEFAULT nextval('lh_speech_language_id_seq');

DROP SEQUENCE IF EXISTS lh_speech_language_dialect_id_seq;
CREATE SEQUENCE lh_speech_language_dialect_id_seq;
SELECT setval('lh_speech_language_dialect_id_seq', max(id)) FROM lh_speech_language_dialect;
ALTER TABLE "lh_speech_language_dialect" ALTER COLUMN "id" SET DEFAULT nextval('lh_speech_language_dialect_id_seq');

DROP SEQUENCE IF EXISTS lh_speech_chat_language_id_seq;
CREATE SEQUENCE lh_speech_chat_language_id_seq;
SELECT setval('lh_speech_chat_language_id_seq', max(id)) FROM lh_speech_chat_language;
ALTER TABLE "lh_speech_chat_language" ALTER COLUMN "id" SET DEFAULT nextval('lh_speech_chat_language_id_seq');

DROP SEQUENCE IF EXISTS lh_cobrowse_id_seq;
CREATE SEQUENCE lh_cobrowse_id_seq;
SELECT setval('lh_cobrowse_id_seq', max(id)) FROM lh_cobrowse;
ALTER TABLE "lh_cobrowse" ALTER COLUMN "id" SET DEFAULT nextval('lh_cobrowse_id_seq');

DROP SEQUENCE IF EXISTS lh_abstract_form_id_seq;
CREATE SEQUENCE lh_abstract_form_id_seq;
SELECT setval('lh_abstract_form_id_seq', max(id)) FROM lh_abstract_form;
ALTER TABLE "lh_abstract_form" ALTER COLUMN "id" SET DEFAULT nextval('lh_abstract_form_id_seq');

DROP SEQUENCE IF EXISTS lh_abstract_form_collected_id_seq;
CREATE SEQUENCE lh_abstract_form_collected_id_seq;
SELECT setval('lh_abstract_form_collected_id_seq', max(id)) FROM lh_abstract_form_collected;
ALTER TABLE "lh_abstract_form_collected" ALTER COLUMN "id" SET DEFAULT nextval('lh_abstract_form_collected_id_seq');

DROP SEQUENCE IF EXISTS lh_abstract_widget_theme_id_seq;
CREATE SEQUENCE lh_abstract_widget_theme_id_seq;
SELECT setval('lh_abstract_widget_theme_id_seq', max(id)) FROM lh_abstract_widget_theme;
ALTER TABLE "lh_abstract_widget_theme" ALTER COLUMN "id" SET DEFAULT nextval('lh_abstract_widget_theme_id_seq');

DROP SEQUENCE IF EXISTS lh_abstract_email_template_id_seq;
CREATE SEQUENCE lh_abstract_email_template_id_seq;
SELECT setval('lh_abstract_email_template_id_seq', max(id)) FROM lh_abstract_email_template;
ALTER TABLE "lh_abstract_email_template" ALTER COLUMN "id" SET DEFAULT nextval('lh_abstract_email_template_id_seq');

DROP SEQUENCE IF EXISTS lh_abstract_proactive_chat_invitation_id_seq;
CREATE SEQUENCE lh_abstract_proactive_chat_invitation_id_seq;
SELECT setval('lh_abstract_proactive_chat_invitation_id_seq', max(id)) FROM lh_abstract_proactive_chat_invitation;
ALTER TABLE "lh_abstract_proactive_chat_invitation" ALTER COLUMN "id" SET DEFAULT nextval('lh_abstract_proactive_chat_invitation_id_seq');

DROP SEQUENCE IF EXISTS lh_canned_msg_id_seq;
CREATE SEQUENCE lh_canned_msg_id_seq;
SELECT setval('lh_canned_msg_id_seq', max(id)) FROM lh_canned_msg;
ALTER TABLE "lh_canned_msg" ALTER COLUMN "id" SET DEFAULT nextval('lh_canned_msg_id_seq');

DROP SEQUENCE IF EXISTS lh_chat_id_seq;
CREATE SEQUENCE lh_chat_id_seq;
SELECT setval('lh_chat_id_seq', max(id)) FROM lh_chat;
ALTER TABLE "lh_chat" ALTER COLUMN "id" SET DEFAULT nextval('lh_chat_id_seq');

DROP SEQUENCE IF EXISTS lh_chat_accept_id_seq;
CREATE SEQUENCE lh_chat_accept_id_seq;
SELECT setval('lh_chat_accept_id_seq', max(id)) FROM lh_chat_accept;
ALTER TABLE "lh_chat_accept" ALTER COLUMN "id" SET DEFAULT nextval('lh_chat_accept_id_seq');

DROP SEQUENCE IF EXISTS lh_chat_archive_range_id_seq;
CREATE SEQUENCE lh_chat_archive_range_id_seq;
SELECT setval('lh_chat_archive_range_id_seq', max(id)) FROM lh_chat_archive_range;
ALTER TABLE "lh_chat_archive_range" ALTER COLUMN "id" SET DEFAULT nextval('lh_chat_archive_range_id_seq');

DROP SEQUENCE IF EXISTS lh_chat_blocked_user_id_seq;
CREATE SEQUENCE lh_chat_blocked_user_id_seq;
SELECT setval('lh_chat_blocked_user_id_seq', max(id)) FROM lh_chat_blocked_user;
ALTER TABLE "lh_chat_blocked_user" ALTER COLUMN "id" SET DEFAULT nextval('lh_chat_blocked_user_id_seq');

DROP SEQUENCE IF EXISTS lh_chat_file_id_seq;
CREATE SEQUENCE lh_chat_file_id_seq;
SELECT setval('lh_chat_file_id_seq', max(id)) FROM lh_chat_file;
ALTER TABLE "lh_chat_file" ALTER COLUMN "id" SET DEFAULT nextval('lh_chat_file_id_seq');

DROP SEQUENCE IF EXISTS lh_chat_online_user_id_seq;
CREATE SEQUENCE lh_chat_online_user_id_seq;
SELECT setval('lh_chat_online_user_id_seq', max(id)) FROM lh_chat_online_user;
ALTER TABLE "lh_chat_online_user" ALTER COLUMN "id" SET DEFAULT nextval('lh_chat_online_user_id_seq');

DROP SEQUENCE IF EXISTS lh_chat_online_user_footprint_id_seq;
CREATE SEQUENCE lh_chat_online_user_footprint_id_seq;
SELECT setval('lh_chat_online_user_footprint_id_seq', max(id)) FROM lh_chat_online_user_footprint;
ALTER TABLE "lh_chat_online_user_footprint" ALTER COLUMN "id" SET DEFAULT nextval('lh_chat_online_user_footprint_id_seq');

DROP SEQUENCE IF EXISTS lh_chatbox_id_seq;
CREATE SEQUENCE lh_chatbox_id_seq;
SELECT setval('lh_chatbox_id_seq', max(id)) FROM lh_chatbox;
ALTER TABLE "lh_chatbox" ALTER COLUMN "id" SET DEFAULT nextval('lh_chatbox_id_seq');

DROP SEQUENCE IF EXISTS lh_departament_id_seq;
CREATE SEQUENCE lh_departament_id_seq;
SELECT setval('lh_departament_id_seq', max(id)) FROM lh_departament;
ALTER TABLE "lh_departament" ALTER COLUMN "id" SET DEFAULT nextval('lh_departament_id_seq');

DROP SEQUENCE IF EXISTS lh_abstract_browse_offer_invitation_id_seq;
CREATE SEQUENCE lh_abstract_browse_offer_invitation_id_seq;
SELECT setval('lh_abstract_browse_offer_invitation_id_seq', max(id)) FROM lh_abstract_browse_offer_invitation;
ALTER TABLE "lh_abstract_browse_offer_invitation" ALTER COLUMN "id" SET DEFAULT nextval('lh_abstract_browse_offer_invitation_id_seq');

DROP SEQUENCE IF EXISTS lh_faq_id_seq;
CREATE SEQUENCE lh_faq_id_seq;
SELECT setval('lh_faq_id_seq', max(id)) FROM lh_faq;
ALTER TABLE "lh_faq" ALTER COLUMN "id" SET DEFAULT nextval('lh_faq_id_seq');

DROP SEQUENCE IF EXISTS lh_forgotpasswordhash_id_seq;
CREATE SEQUENCE lh_forgotpasswordhash_id_seq;
SELECT setval('lh_forgotpasswordhash_id_seq', max(id)) FROM lh_forgotpasswordhash;
ALTER TABLE "lh_forgotpasswordhash" ALTER COLUMN "id" SET DEFAULT nextval('lh_forgotpasswordhash_id_seq');

DROP SEQUENCE IF EXISTS lh_group_id_seq;
CREATE SEQUENCE lh_group_id_seq;
SELECT setval('lh_group_id_seq', max(id)) FROM lh_group;
ALTER TABLE "lh_group" ALTER COLUMN "id" SET DEFAULT nextval('lh_group_id_seq');

DROP SEQUENCE IF EXISTS lh_grouprole_id_seq;
CREATE SEQUENCE lh_grouprole_id_seq;
SELECT setval('lh_grouprole_id_seq', max(id)) FROM lh_grouprole;
ALTER TABLE "lh_grouprole" ALTER COLUMN "id" SET DEFAULT nextval('lh_grouprole_id_seq');

DROP SEQUENCE IF EXISTS lh_groupuser_id_seq;
CREATE SEQUENCE lh_groupuser_id_seq;
SELECT setval('lh_groupuser_id_seq', max(id)) FROM lh_groupuser;
ALTER TABLE "lh_groupuser" ALTER COLUMN "id" SET DEFAULT nextval('lh_groupuser_id_seq');

DROP SEQUENCE IF EXISTS lh_msg_id_seq;
CREATE SEQUENCE lh_msg_id_seq;
SELECT setval('lh_msg_id_seq', max(id)) FROM lh_msg;
ALTER TABLE "lh_msg" ALTER COLUMN "id" SET DEFAULT nextval('lh_msg_id_seq');

DROP SEQUENCE IF EXISTS lh_question_id_seq;
CREATE SEQUENCE lh_question_id_seq;
SELECT setval('lh_question_id_seq', max(id)) FROM lh_question;
ALTER TABLE "lh_question" ALTER COLUMN "id" SET DEFAULT nextval('lh_question_id_seq');

DROP SEQUENCE IF EXISTS lh_question_answer_id_seq;
CREATE SEQUENCE lh_question_answer_id_seq;
SELECT setval('lh_question_answer_id_seq', max(id)) FROM lh_question_answer;
ALTER TABLE "lh_question_answer" ALTER COLUMN "id" SET DEFAULT nextval('lh_question_answer_id_seq');

DROP SEQUENCE IF EXISTS lh_question_option_id_seq;
CREATE SEQUENCE lh_question_option_id_seq;
SELECT setval('lh_question_option_id_seq', max(id)) FROM lh_question_option;
ALTER TABLE "lh_question_option" ALTER COLUMN "id" SET DEFAULT nextval('lh_question_option_id_seq');

DROP SEQUENCE IF EXISTS lh_question_option_answer_id_seq;
CREATE SEQUENCE lh_question_option_answer_id_seq;
SELECT setval('lh_question_option_answer_id_seq', max(id)) FROM lh_question_option_answer;
ALTER TABLE "lh_question_option_answer" ALTER COLUMN "id" SET DEFAULT nextval('lh_question_option_answer_id_seq');

DROP SEQUENCE IF EXISTS lh_role_id_seq;
CREATE SEQUENCE lh_role_id_seq;
SELECT setval('lh_role_id_seq', max(id)) FROM lh_role;
ALTER TABLE "lh_role" ALTER COLUMN "id" SET DEFAULT nextval('lh_role_id_seq');

DROP SEQUENCE IF EXISTS lh_rolefunction_id_seq;
CREATE SEQUENCE lh_rolefunction_id_seq;
SELECT setval('lh_rolefunction_id_seq', max(id)) FROM lh_rolefunction;
ALTER TABLE "lh_rolefunction" ALTER COLUMN "id" SET DEFAULT nextval('lh_rolefunction_id_seq');

DROP SEQUENCE IF EXISTS lh_transfer_id_seq;
CREATE SEQUENCE lh_transfer_id_seq;
SELECT setval('lh_transfer_id_seq', max(id)) FROM lh_transfer;
ALTER TABLE "lh_transfer" ALTER COLUMN "id" SET DEFAULT nextval('lh_transfer_id_seq');

DROP SEQUENCE IF EXISTS lh_userdep_id_seq;
CREATE SEQUENCE lh_userdep_id_seq;
SELECT setval('lh_userdep_id_seq', max(id)) FROM lh_userdep;
ALTER TABLE "lh_userdep" ALTER COLUMN "id" SET DEFAULT nextval('lh_userdep_id_seq');

DROP SEQUENCE IF EXISTS lh_users_id_seq;
CREATE SEQUENCE lh_users_id_seq;
SELECT setval('lh_users_id_seq', max(id)) FROM lh_users;
ALTER TABLE "lh_users" ALTER COLUMN "id" SET DEFAULT nextval('lh_users_id_seq');

DROP SEQUENCE IF EXISTS lh_users_remember_id_seq;
CREATE SEQUENCE lh_users_remember_id_seq;
SELECT setval('lh_users_remember_id_seq', max(id)) FROM lh_users_remember;
ALTER TABLE "lh_users_remember" ALTER COLUMN "id" SET DEFAULT nextval('lh_users_remember_id_seq');

DROP SEQUENCE IF EXISTS lh_users_setting_id_seq;
CREATE SEQUENCE lh_users_setting_id_seq;
SELECT setval('lh_users_setting_id_seq', max(id)) FROM lh_users_setting;
ALTER TABLE "lh_users_setting" ALTER COLUMN "id" SET DEFAULT nextval('lh_users_setting_id_seq');

CREATE INDEX abstract_auto_responder_siteaccess_position ON lh_abstract_auto_responder USING btree (siteaccess, "position");
CREATE INDEX abstract_time_ositepsp ON lh_abstract_proactive_chat_invitation USING btree (time_on_site,pageviews,siteaccess,identifier,position);
CREATE INDEX abstract_pro_inv_identifier ON lh_abstract_proactive_chat_invitation USING btree (identifier);
CREATE INDEX abstract_pro_inv_dep_id ON lh_abstract_proactive_chat_invitation USING btree (dep_id);
CREATE INDEX canned_msg_department_id ON lh_canned_msg USING btree (department_id);
CREATE INDEX canned_msg_attr_int_1 ON lh_canned_msg USING btree (attr_int_1);
CREATE INDEX canned_msg_attr_int_2 ON lh_canned_msg USING btree (attr_int_2);
CREATE INDEX canned_msg_attr_int_3 ON lh_canned_msg USING btree (attr_int_3);
CREATE INDEX canned_msg_user_id ON lh_canned_msg USING btree (user_id);
CREATE INDEX status_user_id ON lh_chat USING btree (status,user_id);
CREATE INDEX chat_user_id ON lh_chat USING btree (user_id);
CREATE INDEX chat_online_user_id ON lh_chat USING btree (online_user_id);
CREATE INDEX chat_dep_id ON lh_chat USING btree (dep_id);
CREATE INDEX chat_hum_dep_id_id ON lh_chat USING btree (has_unread_messages, dep_id, id);	
CREATE INDEX chat_status_dep_id_id ON lh_chat USING btree (status, dep_id, id);
CREATE INDEX chat_status_dep_id_priority_id ON lh_chat USING btree (status, dep_id, priority, id);	
CREATE INDEX chat_status_priority_id ON lh_chat USING btree (status, priority, id);
CREATE INDEX lh_speech_language_dialect_language_id ON lh_speech_language_dialect USING btree (language_id);
CREATE INDEX lh_speech_chat_language_chat_id ON lh_speech_chat_language USING btree (chat_id);
CREATE INDEX chat_accept_hash ON lh_chat_accept USING btree (hash);
CREATE INDEX chat_blocked_user_ip ON lh_chat_blocked_user USING btree (ip);
CREATE INDEX chat_file_chat_id ON lh_chat_file USING btree (chat_id);
CREATE INDEX chat_file_user_id ON lh_chat_file USING btree (user_id);
CREATE INDEX chat_file_online_user_id ON lh_chat_file USING btree (online_user_id);
CREATE INDEX chat_online_user_vid ON lh_chat_online_user USING btree (vid);
CREATE INDEX chat_online_user_dep_id ON lh_chat_online_user USING btree (dep_id);
CREATE INDEX chat_online_user_last_visit_dep_id ON lh_chat_online_user USING btree (last_visit, dep_id);
CREATE INDEX chat_online_user_footprint_chat_id_vtime ON lh_chat_online_user_footprint USING btree (chat_id, vtime);
CREATE INDEX chat_online_user_footprint_ou_id ON lh_chat_online_user_footprint USING btree (online_user_id);
CREATE INDEX chatbox_identifier ON lh_chatbox USING btree (identifier);
CREATE INDEX departament_identifier ON lh_departament USING btree (identifier);
CREATE INDEX departament_disabled_hidden ON lh_departament USING btree (disabled,hidden);
CREATE INDEX departament_online_hours ON lh_departament USING btree (online_hours_active, start_hour, end_hour);
CREATE INDEX boffer_active ON lh_abstract_browse_offer_invitation USING btree (active);
CREATE INDEX boffer_identifier ON lh_abstract_browse_offer_invitation USING btree (identifier);
CREATE INDEX faq_active_url ON lh_faq USING btree (active, has_url);
CREATE INDEX faq_has_url ON lh_faq USING btree (has_url);
CREATE INDEX faq_is_wildcard ON lh_faq USING btree (is_wildcard);	
CREATE INDEX faq_identifier ON lh_faq USING btree (identifier);	
CREATE INDEX grouprole_role_id_group_id ON lh_grouprole USING btree (role_id, group_id);
CREATE INDEX grouprole_group_id ON lh_grouprole USING btree (group_id);
CREATE INDEX groupuser_group_id_user_id ON lh_groupuser USING btree (group_id, user_id);
CREATE INDEX groupuser_user_id ON lh_groupuser USING btree (user_id);
CREATE INDEX msg_chat_id_id ON lh_msg USING btree (chat_id, id)	;
CREATE INDEX question_priority ON lh_question USING btree (priority);
CREATE INDEX question_active_priority ON lh_question USING btree (active, priority);
CREATE INDEX question_answer_ip ON lh_question_answer USING btree (ip);
CREATE INDEX question_answer_question_id ON lh_question_answer USING btree (question_id); 
CREATE INDEX question_option_question_id ON lh_question_option USING btree (question_id);
CREATE INDEX question_option_answer_question_id ON lh_question_option_answer USING btree (question_id);
CREATE INDEX question_option_answer_ip ON lh_question_option_answer USING btree (ip);
CREATE INDEX rolefunction_role_id ON lh_rolefunction USING btree (role_id);
CREATE INDEX transfer_dep_id ON lh_transfer USING btree (dep_id);
CREATE INDEX transfer_tuid_dep_id ON lh_transfer USING btree (transfer_user_id, dep_id);
CREATE INDEX trasnfer_transfer_to_user_id ON lh_transfer USING btree (transfer_to_user_id);	
CREATE INDEX userdep_user_id ON lh_userdep USING btree (user_id);
CREATE INDEX userdep_la_ho_dep_id ON lh_userdep USING btree (last_activity, hide_online, dep_id);
CREATE INDEX userdep_dep_id ON lh_userdep USING btree (dep_id);
CREATE INDEX users_hide_online ON lh_users USING btree (hide_online);
CREATE INDEX users_rec_per_req ON lh_users USING btree (rec_per_req);
CREATE INDEX users_email ON lh_users USING btree (email);
CREATE INDEX users_xmpp_username ON lh_users USING btree (xmpp_username);
CREATE INDEX users_settings_user_id_identifier ON lh_users_setting USING btree (user_id, identifier);
CREATE INDEX form_id ON lh_abstract_form_collected USING btree (form_id);
CREATE INDEX chat_id ON lh_cobrowse USING btree (chat_id);
CREATE INDEX lh_cobrowse_online_user_id ON lh_cobrowse USING btree (online_user_id);
CREATE INDEX lh_group_disabled ON lh_group USING btree (disabled);
CREATE INDEX lh_departament_attr_int_1 ON lh_departament USING btree (attr_int_1);
CREATE INDEX lh_departament_attr_int_2 ON lh_departament USING btree (attr_int_2);
CREATE INDEX lh_departament_attr_int_3 ON lh_departament USING btree (attr_int_3);

COMMIT;
