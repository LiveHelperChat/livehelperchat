<?php

try {

$cfgSite = erConfigClassLhConfig::getInstance();

if ($cfgSite->getSetting( 'site', 'installed' ) == true)
{
    $Params['module']['functions'] = array('install');
    include_once('modules/lhkernel/nopermission.php');

    $Result['pagelayout'] = 'install';
    $Result['path'] = array(array('title' => 'Live helper chat installation'));
    return $Result;

    exit;
}

$instance = erLhcoreClassSystem::instance();

if ($instance->SiteAccess != 'site_admin') {
    header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/install/install') );
    exit;
}

$tpl = new erLhcoreClassTemplate( 'lhinstall/install1.tpl.php');

switch ((int)$Params['user_parameters']['step_id']) {

	case '1':
		$Errors = array();
		if (!is_writable("cache/cacheconfig/settings.ini.php"))
	       $Errors[] = "cache/cacheconfig/settings.ini.php is not writable";

	    if (!is_writable("settings/settings.ini.php"))
	       $Errors[] = "settings/settings.ini.php is not writable";

		if (!is_writable("cache/translations"))
	       $Errors[] = "cache/translations is not writable";

		if (!is_writable("cache/userinfo"))
	       $Errors[] = "cache/userinfo is not writable";

		if (!is_writable("cache/compiledtemplates"))
	       $Errors[] = "cache/compiledtemplates is not writable";

		if (!extension_loaded ('pdo_mysql' ))
	       $Errors[] = "php-pdo extension not detected. Please install php extension";

	       if (count($Errors) == 0)
	           $tpl->setFile('lhinstall/install2.tpl.php');
	  break;

	  case '2':
		$Errors = array();

		$definition = array(
            'DatabaseUsername' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::REQUIRED, 'string'
            ),
            'DatabasePassword' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::REQUIRED, 'string'
            ),
            'DatabaseHost' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::REQUIRED, 'string'
            ),
            'DatabasePort' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::REQUIRED, 'int'
            ),
            'DatabaseDatabaseName' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::REQUIRED, 'string'
            ),
        );

	   $form = new ezcInputForm( INPUT_POST, $definition );


	   if ( !$form->hasValidData( 'DatabaseUsername' ) )
       {
           $Errors[] = 'Please enter database username';
       }

	   if ( !$form->hasValidData( 'DatabasePassword' ) )
       {
           $Errors[] = 'Please enter database password';
       }

	   if ( !$form->hasValidData( 'DatabaseHost' ) || $form->DatabaseHost == '' )
       {
           $Errors[] = 'Please enter database host';
       }

	   if ( !$form->hasValidData( 'DatabasePort' ) || $form->DatabasePort == '' )
       {
           $Errors[] = 'Please enter database post';
       }

	   if ( !$form->hasValidData( 'DatabaseDatabaseName' ) || $form->DatabaseDatabaseName == '' )
       {
           $Errors[] = 'Please enter database name';
       }

       if (count($Errors) == 0)
       {
           try {
           	$db = ezcDbFactory::create( "mysql://{$form->DatabaseUsername}:{$form->DatabasePassword}@{$form->DatabaseHost}:{$form->DatabasePort}/{$form->DatabaseDatabaseName}" );
           } catch (Exception $e) {
                  $Errors[] = 'Cannot login with provided logins. Returned message: <br/>'.$e->getMessage();
           }
       }

	       if (count($Errors) == 0){

	           $cfgSite = erConfigClassLhConfig::getInstance();
	           $cfgSite->setSetting( 'db', 'host', $form->DatabaseHost);
	           $cfgSite->setSetting( 'db', 'user', $form->DatabaseUsername);
	           $cfgSite->setSetting( 'db', 'password', $form->DatabasePassword);
	           $cfgSite->setSetting( 'db', 'database', $form->DatabaseDatabaseName);
	           $cfgSite->setSetting( 'db', 'port', $form->DatabasePort);

	           $cfgSite->setSetting( 'site', 'secrethash', substr(md5(time() . ":" . mt_rand()),0,10));

	           $cfgSite->save();

	           $tpl->setFile('lhinstall/install3.tpl.php');
	       } else {

	          $tpl->set('db_username',$form->DatabaseUsername);
	          $tpl->set('db_password',$form->DatabasePassword);
	          $tpl->set('db_host',$form->DatabaseHost);
	          $tpl->set('db_port',$form->DatabasePort);
	          $tpl->set('db_name',$form->DatabaseDatabaseName);

	          $tpl->set('errors',$Errors);
	          $tpl->setFile('lhinstall/install2.tpl.php');
	       }
	  break;

	case '3':

	    $Errors = array();

	    if ($_SERVER['REQUEST_METHOD'] == 'POST')
	    {
    		$definition = array(
                'AdminUsername' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::REQUIRED, 'string'
                ),
                'AdminPassword' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::REQUIRED, 'string'
                ),
                'AdminPassword1' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::REQUIRED, 'string'
                ),
                'AdminEmail' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::REQUIRED, 'validate_email'
                ),
                'AdminName' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'string'
                ),
                'AdminSurname' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::OPTIONAL, 'string'
                ),
                'DefaultDepartament' => new ezcInputFormDefinitionElement(
                    ezcInputFormDefinitionElement::REQUIRED, 'string'
                )
            );

    	    $form = new ezcInputForm( INPUT_POST, $definition );


    	    if ( !$form->hasValidData( 'AdminUsername' ) || $form->AdminUsername == '')
            {
                $Errors[] = 'Please enter admin username';
            }

            if ($form->hasValidData( 'AdminUsername' ) && $form->AdminUsername != '' && strlen($form->AdminUsername) > 10)
            {
                $Errors[] = 'Maximum 10 characters for admin username';
            }

    	    if ( !$form->hasValidData( 'AdminPassword' ) || $form->AdminPassword == '')
            {
                $Errors[] = 'Please enter admin password';
            }

    	    if ($form->hasValidData( 'AdminPassword' ) && $form->AdminPassword != '' && strlen($form->AdminPassword) > 10)
            {
                $Errors[] = 'Maximum 10 characters for admin password';
            }

    	    if ($form->hasValidData( 'AdminPassword' ) && $form->AdminPassword != '' && strlen($form->AdminPassword) <= 10 && $form->AdminPassword1 != $form->AdminPassword)
            {
                $Errors[] = 'Passwords missmatch';
            }


    	    if ( !$form->hasValidData( 'AdminEmail' ) )
            {
                $Errors[] = 'Wrong email address';
            }


            if ( !$form->hasValidData( 'DefaultDepartament' ) || $form->DefaultDepartament == '')
            {
                $Errors[] = 'Please enter default departament name';
            }

            if (count($Errors) == 0) {

               $tpl->set('admin_username',$form->AdminUsername);
               if ( $form->hasValidData( 'AdminEmail' ) ) $tpl->set('admin_email',$form->AdminEmail);
    	       $tpl->set('admin_name',$form->AdminName);
    	       $tpl->set('admin_surname',$form->AdminSurname);
    	       $tpl->set('admin_departament',$form->DefaultDepartament);

    	       /*DATABASE TABLES SETUP*/
    	       $db = ezcDbInstance::get();

        	   $db->query("CREATE TABLE `lh_chat` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `nick` varchar(50) NOT NULL,
				  `status` int(11) NOT NULL DEFAULT '0',
				  `time` int(11) NOT NULL,
				  `user_id` int(11) NOT NULL,
				  `hash` varchar(40) NOT NULL,
				  `referrer` text NOT NULL,
				  `ip` varchar(100) NOT NULL,
				  `dep_id` int(11) NOT NULL,
				  `user_status` int(11) NOT NULL DEFAULT '0',
				  `support_informed` int(11) NOT NULL DEFAULT '0',
				  `email` varchar(100) NOT NULL,
				  `country_code` varchar(100) NOT NULL,
				  `country_name` varchar(100) NOT NULL,
				  `user_typing` int(11) NOT NULL,
				  `operator_typing` int(11) NOT NULL,
				  `phone` varchar(100) NOT NULL,
				  `has_unread_messages` int(11) NOT NULL,
				  `last_user_msg_time` int(11) NOT NULL,
				  `last_msg_id` int(11) NOT NULL,
				  `additional_data` varchar(250) NOT NULL,
				  `mail_send` int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `status` (`status`),
				  KEY `user_id` (`user_id`),
				  KEY `dep_id` (`dep_id`),
				  KEY `has_unread_messages_dep_id_id` (`has_unread_messages`,`dep_id`,`id`),
				  KEY `status_dep_id_id` (`status`,`dep_id`,`id`)
				) DEFAULT CHARSET=utf8;");

        	   $db->query("CREATE TABLE IF NOT EXISTS `lh_chat_blocked_user` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `ip` varchar(100) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  `datets` int(11) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `ip` (`ip`)
                ) DEFAULT CHARSET=utf8;");

        	   $db->query("CREATE TABLE `lh_faq` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `question` varchar(250) NOT NULL,
				  `answer` text NOT NULL,
				  `url` varchar(250) NOT NULL,
				  `active` int(11) NOT NULL,
				  `has_url` tinyint(1) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `active` (`active`),
				  KEY `active_url` (`active`,`url`),
				  KEY `has_url` (`has_url`)
				) DEFAULT CHARSET=utf8;");

        	   $db->query("CREATE TABLE `lh_abstract_email_template` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(250) NOT NULL,
				  `from_name` varchar(150) NOT NULL,
				  `from_name_ac` tinyint(4) NOT NULL,
				  `from_email` varchar(150) NOT NULL,
				  `from_email_ac` tinyint(4) NOT NULL,
				  `content` text NOT NULL,
				  `subject` varchar(250) NOT NULL,
				  `subject_ac` tinyint(4) NOT NULL,
				  `reply_to` varchar(150) NOT NULL,
				  `reply_to_ac` tinyint(4) NOT NULL,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8;");

        	   $db->query("INSERT INTO `lh_abstract_email_template` (`id`, `name`, `from_name`, `from_name_ac`, `from_email`, `from_email_ac`, `content`, `subject`, `subject_ac`, `reply_to`, `reply_to_ac`) VALUES (1,'Send mail to user','Live Support',0,'',0,'Dear {user_chat_nick},\r\n\r\n{additional_message}\r\n\r\nLive Support response:\r\n{messages_content}\r\n\r\nSincerely,\r\nLive Support Team\r\n','{name_surname} has responded to your request',	1,'',1);");

        	   $db->query("CREATE TABLE `lh_question` (
        	   `id` int(11) NOT NULL AUTO_INCREMENT,
        	   `question` varchar(250) NOT NULL,
        	   `location` varchar(250) NOT NULL,
        	   `active` int(11) NOT NULL,
        	   `priority` int(11) NOT NULL,
        	   `is_voting` int(11) NOT NULL,
        	   `question_intro` text NOT NULL,
        	   PRIMARY KEY (`id`),
        	   KEY `priority` (`priority`),
        	   KEY `active_priority` (`active`,`priority`)
        	   ) DEFAULT CHARSET=utf8;");

        	   $db->query("CREATE TABLE `lh_question_answer` (
        	   `id` int(11) NOT NULL AUTO_INCREMENT,
        	   `ip` bigint(20) NOT NULL,
        	   `question_id` int(11) NOT NULL,
        	   `answer` text NOT NULL,
        	   `ctime` int(11) NOT NULL,
        	   PRIMARY KEY (`id`),
        	   KEY `ip` (`ip`),
        	   KEY `question_id` (`question_id`)
        	   ) DEFAULT CHARSET=utf8");

        	   $db->query("CREATE TABLE `lh_question_option` (
        	   `id` int(11) NOT NULL AUTO_INCREMENT,
        	   `question_id` int(11) NOT NULL,
        	   `option_name` varchar(250) NOT NULL,
        	   `priority` tinyint(4) NOT NULL,
        	   PRIMARY KEY (`id`),
        	   KEY `question_id` (`question_id`)
        	   ) DEFAULT CHARSET=utf8;");

        	   $db->query("CREATE TABLE `lh_question_option_answer` (
        	   `id` int(11) NOT NULL AUTO_INCREMENT,
        	   `question_id` int(11) NOT NULL,
        	   `option_id` int(11) NOT NULL,
        	   `ip` bigint(20) NOT NULL,
        	   PRIMARY KEY (`id`),
        	   KEY `question_id` (`question_id`),
        	   KEY `ip` (`ip`)
        	   ) DEFAULT CHARSET=utf8");

        	   $db->query("CREATE TABLE `lh_chatbox` (
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
                  PRIMARY KEY (`id`)
                ) DEFAULT CHARSET=utf8;");

        	   $db->query("CREATE TABLE IF NOT EXISTS `lh_users_setting` (
        	   `id` int(11) NOT NULL AUTO_INCREMENT,
        	   `user_id` int(11) NOT NULL,
        	   `identifier` varchar(50) NOT NULL,
        	   `value` varchar(50) NOT NULL,
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

        	   $db->query("INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES
                ('tracked_users_cleanup',	'7',	0,	'How many days keep records of online users.',	0),
        	   	('list_online_operators', '0', '0', 'List online operators, 0 - no, 1 - yes.', '0'),
        	   	('voting_days_limit',	'7',	0,	'How many days voting widget should not be expanded after last show',	0),
                ('track_online_visitors',	'0',	0,	'Enable online site visitors tracking, 0 - no, 1 - yes',	0),
                ('customer_company_name',	'Live Helper Chat',	0,	'Your company name - visible in bottom left corner',	0),
                ('customer_site_url',	'http://livehelperchat.com',	0,	'Your site URL address',	0),
        	    ('chatbox_data',	'a:6:{i:0;b:0;s:20:\"chatbox_auto_enabled\";i:0;s:19:\"chatbox_secret_hash\";s:9:\"{$randomHash}\";s:20:\"chatbox_default_name\";s:7:\"Chatbox\";s:17:\"chatbox_msg_limit\";i:50;s:22:\"chatbox_default_opname\";s:7:\"Manager\";}',	0,	'Chatbox configuration',	1),
                ('start_chat_data',	'a:10:{i:0;b:0;s:21:\"name_visible_in_popup\";b:1;s:27:\"name_visible_in_page_widget\";b:1;s:19:\"name_require_option\";s:8:\"required\";s:22:\"email_visible_in_popup\";b:1;s:28:\"email_visible_in_page_widget\";b:1;s:20:\"email_require_option\";s:8:\"required\";s:24:\"message_visible_in_popup\";b:1;s:30:\"message_visible_in_page_widget\";b:1;s:22:\"message_require_option\";s:8:\"required\";}',	0,	'',	1),
                ('application_name',	'a:6:{s:3:\"eng\";s:31:\"Live Helper Chat - live support\";s:3:\"lit\";s:26:\"Live Helper Chat - pagalba\";s:3:\"hrv\";s:0:\"\";s:3:\"esp\";s:0:\"\";s:3:\"por\";s:0:\"\";s:10:\"site_admin\";s:31:\"Live Helper Chat - live support\";}',	1,	'Support application name, visible in browser title.',	0),
                ('geo_data', '', '0', '', '1')");

        	   $db->query("CREATE TABLE `lh_chat_online_user` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `vid` varchar(50) NOT NULL,
                  `ip` varchar(50) NOT NULL,
                  `current_page` varchar(250) NOT NULL,
                  `chat_id` int(11) NOT NULL,
                  `last_visit` int(11) NOT NULL,
        	   	  `first_visit` int(11) NOT NULL,
        	   	  `pages_count` int(11) NOT NULL,
                  `user_agent` varchar(250) NOT NULL,
                  `user_country_code` varchar(50) NOT NULL,
                  `user_country_name` varchar(50) NOT NULL,
                  `operator_message` varchar(250) NOT NULL,
                  `operator_user_id` int(11) NOT NULL,
                  `message_seen` int(11) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `last_visit` (`last_visit`),
                  KEY `vid` (`vid`)
                ) DEFAULT CHARSET=utf8;");

        	   //Default departament
        	   $db->query("CREATE TABLE IF NOT EXISTS `lh_departament` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(100) NOT NULL,
                  PRIMARY KEY (`id`)
                ) DEFAULT CHARSET=utf8;");

        	   $Departament = new erLhcoreClassModelDepartament();
               $Departament->name = $form->DefaultDepartament;
               erLhcoreClassDepartament::getSession()->save($Departament);

               //Administrators group
               $db->query("CREATE TABLE IF NOT EXISTS `lh_group` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(50) NOT NULL,
                  PRIMARY KEY (`id`)
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
                  KEY `group_id` (`role_id`,`group_id`)
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
               $db->query("CREATE TABLE `lh_users` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `username` varchar(40) NOT NULL,
                  `password` varchar(40) NOT NULL,
                  `email` varchar(100) NOT NULL,
                  `name` varchar(100) NOT NULL,
                  `surname` varchar(100) NOT NULL,
                  `disabled` tinyint(4) NOT NULL,
                  `hide_online` tinyint(1) NOT NULL,
                  `all_departments` tinyint(1) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `hide_online` (`hide_online`)
                ) DEFAULT CHARSET=utf8;");

                $UserData = new erLhcoreClassModelUser();

                $UserData->setPassword($form->AdminPassword);
                $UserData->email   = $form->AdminEmail;
                $UserData->name    = $form->AdminName;
                $UserData->surname = $form->AdminSurname;
                $UserData->username = $form->AdminUsername;
                $UserData->all_departments = 1;

                erLhcoreClassUser::getSession()->save($UserData);

                // User departaments
                $db->query("CREATE TABLE `lh_userdep` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `dep_id` int(11) NOT NULL,
                  `last_activity` int(11) NOT NULL,
                  `hide_online` int(11) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `last_activity_hide_online_dep_id` (`last_activity`,`hide_online`,`dep_id`),
                  KEY `dep_id` (`dep_id`)
                ) DEFAULT CHARSET=utf8;");

                // Insert record to departament instantly
                $db->query("INSERT INTO `lh_userdep` (`user_id`,`dep_id`,`last_activity`,`hide_online`) VALUES ({$UserData->id},0,0,0)");

                // Transfer chat
                $db->query("CREATE TABLE `lh_transfer` (
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
                    array('module' => 'lhchat',  'function' => 'use'),
                    array('module' => 'lhchat',  'function' => 'singlechatwindow'),
                    array('module' => 'lhchat',  'function' => 'allowopenremotechat'),
                    array('module' => 'lhchat',  'function' => 'allowchattabs'),
                    array('module' => 'lhfront', 'function' => 'use'),
                    array('module' => 'lhsystem','function' => 'use'),
                    array('module' => 'lhchat',  'function' => 'allowblockusers'),
                    array('module' => 'lhsystem','function' => 'generatejs'),
                    array('module' => 'lhchat',  'function' => 'allowtransfer'),
                    array('module' => 'lhchat',  'function' => 'administratecannedmsg'),
                    array('module' => 'lhquestionary',  'function' => 'manage_questionary'),
                    array('module' => 'lhfaq',   'function' => 'manage_faq'),
                    array('module' => 'lhchatbox',   'function' => 'manage_chatbox'),
                    array('module' => 'lhxml',   'function' => '*')
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
	           $cfgSite->save();

    	       $tpl->setFile('lhinstall/install4.tpl.php');

            } else {

               $tpl->set('admin_username',$form->AdminUsername);
               if ( $form->hasValidData( 'AdminEmail' ) ) $tpl->set('admin_email',$form->AdminEmail);
    	       $tpl->set('admin_name',$form->AdminName);
    	       $tpl->set('admin_surname',$form->AdminSurname);
    	       $tpl->set('admin_departament',$form->DefaultDepartament);

    	       $tpl->set('errors',$Errors);

    	       $tpl->setFile('lhinstall/install3.tpl.php');
            }
	    } else {
	        $tpl->setFile('lhinstall/install3.tpl.php');
	    }

	    break;

	case '4':
	    $tpl->setFile('lhinstall/install4.tpl.php');
	    break;

	default:
	    $tpl->setFile('lhinstall/install1.tpl.php');
		break;
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'install';
$Result['path'] = array(array('title' => 'Live helper chat installation'));

} catch (Exception $e){
	echo "Make sure that &quot;cache/*&quot; is writable and then <a href=\"".erLhcoreClassDesign::baseurl('install/install')."\">try again</a>";
	exit;
}
?>