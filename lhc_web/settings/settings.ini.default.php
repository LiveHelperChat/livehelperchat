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
      'force_virtual_host' => false,
      'one_login_per_account' => false,
      'time_zone' => '',
      'date_format' => 'Y-m-d',
      'date_hour_format' => 'H:i:s',
      'date_date_hour_format' => 'Y-m-d H:i:s',
      'default_site_access' => 'eng',
      'maps_api_key' => false,
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
      	12 => 'chn',
      	13 => 'cse',
      	14 => 'nor',
      	15 => 'tur',
      	16 => 'vnm',
      	17 => 'idn',
      	18 => 'sve',
      	19 => 'per',
      	20 => 'ell',
      	21 => 'dnk',
      	22 => 'rou',
      	23 => 'bgr',
      	24 => 'tha',
      	25 => 'geo',
      	26 => 'fin',
      	27 => 'alb',
      	28 => 'heb',
      	29 => 'site_admin'
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
  	'memecache' =>
  		array (
  				'servers' =>
  				array (
  						0 =>
  						array (
  								'host' => '127.0.0.1',
  								'port' => '11211',
  								'weight' => 1,
  						),
  				),
  	),
  	'redis' => array (
  				'server' => array (
  						'host' => 'localhost',
  						'port' => 6379,
                        'database' => 0
  				)
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
      'dnk' =>
      array (
        'locale' => 'da_DA',
        'content_language' => 'da',
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
      'tur' =>
      array (
    	'locale' => 'tr_TR',
    	'content_language' => 'tr',
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
      'chn' =>
      array (
    	'locale' => 'zh_ZH',
    	'content_language' => 'zh',
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
      'cse' =>
      array (
    	'locale' => 'cs_CS',
    	'content_language' => 'cs',
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
      'nor' =>
      array (
    	'locale' => 'no_NO',
    	'content_language' => 'no',
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
      'vnm' =>
      array (
    	'locale' => 'vi_VN',
    	'content_language' => 'vi',
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
      'tha' =>
      array (
    	'locale' => 'th_TH',
    	'content_language' => 'th',
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
      'idn' =>
      array (
    	'locale' => 'id_ID',
    	'content_language' => 'in',
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
      'sve' =>
      array (
    	'locale' => 'sv_SV',
    	'content_language' => 'sv',
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
      'per' =>
      array (
    	'locale' => 'fa_FA',
    	'content_language' => 'fa',
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
      'ell' =>
      array (
    	'locale' => 'el_EL',
    	'content_language' => 'el',
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
      'rou' =>
      array (
    	'locale' => 'ro_RO',
    	'content_language' => 'ro',
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
      'bgr' =>
      array (
    	'locale' => 'bg_BG',
    	'content_language' => 'bg',
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
      'geo' =>
      array (
    	'locale' => 'ka_KA',
    	'content_language' => 'ka',
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
      'fin' =>
      array (
    	'locale' => 'fi_FI',
    	'content_language' => 'fi',
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
      'alb' =>
      array (
    	'locale' => 'sq_AL',
    	'content_language' => 'sq',
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
      'heb' => array (
        'locale' => 'he_HE',
        'content_language' => 'he',
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
