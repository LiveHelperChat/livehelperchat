<?php
return array (
  'settings' =>
  array (
    'site' =>
    array (
      'title' => 'Live helper Chat',
      'site_admin_email' => '',
      'locale' => 'en_EN',
      'theme' => 'defaulttheme',
      'installed' => false,
      'secrethash' => '',
      'debug_output' => false,
      'templatecache' => false,
      'templatecompile' => false,
      'modulecompile' => false,
      'https_port' => 443,
      'default_site_access' => 'eng',
      'extensions' =>
          array (
            // 0 => 'customstatus',
      ),
      'available_site_access' =>
      array (
        0 => 'eng',
        1 => 'lit',
        2 => 'hrv',
        3 => 'esp',
        4 => 'por',
        5 => 'nld',
      	6 => 'ara',
      	7 => 'ger',
      	8 => 'pol',
      	9 => 'rus',
      	10 => 'ita',
      	11 => 'fre',
        12 => 'site_admin',
      ),
    ),
    'default_url' =>
    array (
      'module' => 'chat',
      'view' => 'startchat',
    ),
    'chat' => array(
        'online_timeout' => 300,
        'back_office_sinterval' => 10,
        'chat_message_sinterval' => 3.5,
        'check_for_operator_msg' => 10,
        'new_chat_sound_enabled' => true,
        'new_message_sound_admin_enabled' => true,
        'new_message_sound_user_enabled' => true,
    ),
    'db' =>
    array (
      'host' => '',
      'user' => '',
      'password' => '',
      'database' => '',
      'port' => 3306,
      'use_slaves' => false,
      'db_slaves' =>
      array (
        0 =>
        array (
          'host' => '',
          'user' => '',
          'port' => 3306,
          'password' => '',
          'database' => '',
        ),
      ),
    ),
    'site_access_options' =>
    array (
      'eng' =>
      array (
        'locale' => 'en_EN',
        'content_language' => 'en',
      	'dir_language' => 'ltr',
        'default_url' =>
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
        'theme' =>
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
      ),
      'lit' =>
      array (
        'locale' => 'lt_LT',
        'content_language' => 'lt',
      	'dir_language' => 'ltr',
        'title' => '',
        'description' => '',
        'theme' =>
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'default_url' =>
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
      ),
      'hrv' =>
      array (
        'locale' => 'hr_HR',
        'content_language' => 'hr',
      	'dir_language' => 'ltr',
        'title' => '',
        'description' => '',
        'theme' =>
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'default_url' =>
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
      ),
      'esp' =>
      array (
        'locale' => 'es_MX',
        'content_language' => 'es',
      	'dir_language' => 'ltr',
        'title' => '',
        'description' => '',
        'theme' =>
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'default_url' =>
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
      ),
      'por' =>
      array (
        'locale' => 'pt_BR',
        'content_language' => 'pt',
      	'dir_language' => 'ltr',
        'title' => '',
        'description' => '',
        'theme' =>
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'default_url' =>
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
      ),
      'nld' =>
      array (
        'locale' => 'nl_NL',
        'content_language' => 'nl',
      	'dir_language' => 'ltr',
        'title' => '',
        'description' => '',
        'theme' =>
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'default_url' =>
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
      ),
      'ara' =>
      array (
    	'locale' => 'ar_EG',
    	'content_language' => 'ar',
    	'dir_language' => 'rtl',
    	'title' => '',
    	'description' => '',
    	'theme' =>
      	array (
    		0 => 'customtheme',
    		1 => 'defaulttheme'
      	),
    	   'default_url' =>
    	array (
    		'module' => 'chat',
    		'view' => 'startchat'
    	),
      ),
      'ger' =>
      array (
    	'locale' => 'de_DE',
    	'content_language' => 'de',
    	'dir_language' => 'ltr',
    	'title' => '',
    	'description' => '',
    	'theme' =>
      	array (
    		0 => 'customtheme',
    		1 => 'defaulttheme'
      	),
    	   'default_url' =>
    	array (
    		'module' => 'chat',
    		'view' => 'startchat'
    	),
      ),
      'pol' =>
      array (
    	'locale' => 'pl_PL',
    	'content_language' => 'pl',
    	'dir_language' => 'ltr',
    	'title' => '',
    	'description' => '',
    	'theme' =>
      	array (
    		0 => 'customtheme',
    		1 => 'defaulttheme'
      	),
    	   'default_url' =>
    	array (
    		'module' => 'chat',
    		'view' => 'startchat'
    	),
      ),
      'rus' =>
      array (
    	'locale' => 'ru_RU',
    	'content_language' => 'ru',
    	'dir_language' => 'ltr',
    	'title' => '',
    	'description' => '',
    	'theme' =>
      	array (
    		0 => 'customtheme',
    		1 => 'defaulttheme'
      	),
    	   'default_url' =>
    	array (
    		'module' => 'chat',
    		'view' => 'startchat'
    	),
      ),
      'ita' =>
      array (
    	'locale' => 'it_IT',
    	'content_language' => 'it',
    	'dir_language' => 'ltr',
    	'title' => '',
    	'description' => '',
    	'theme' =>
      	array (
    		0 => 'customtheme',
    		1 => 'defaulttheme'
      	),
    	   'default_url' =>
    	array (
    		'module' => 'chat',
    		'view' => 'startchat'
    	),
      ),
      'fre' =>
      array (
    	'locale' => 'fr_FR',
    	'content_language' => 'fr',
    	'dir_language' => 'ltr',
    	'title' => '',
    	'description' => '',
    	'theme' =>
      	array (
    		0 => 'customtheme',
    		1 => 'defaulttheme'
      	),
    	   'default_url' =>
    	array (
    		'module' => 'chat',
    		'view' => 'startchat'
    	),
      ),
      'site_admin' =>
      array (
        'locale' => 'en_EN',
        'content_language' => 'en',
      	'dir_language' => 'ltr',
        'theme' =>
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'login_pagelayout' => 'login',
        'default_url' =>
        array (
          'module' => 'front',
          'view' => 'default',
        ),
      ),
    ),
    'cacheEngine' =>
    array (
      'cache_global_key' => 'global_cache_key',
      'className' => false,
    ),
  ),
  'comments' => NULL,
);
?>
