<?php
return array (
  'settings' => 
  array (
    'site' => 
    array (
      'title' => 'Live helper chat 1.17v',
      'site_admin_email' => '',
      'locale' => 'en_EN',
      'theme' => 'defaulttheme',
      'installed' => false,
      'secrethash' => '',            
      'debug_output' => false,
      'templatecache' => false,
      'templatecompile' => false,
      'modulecompile' => false,      
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
        5 => 'site_admin',
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
      'site_admin' => 
      array (
        'locale' => 'en_EN',
        'content_language' => 'en',
        'theme' => 
        array (
          0 => 'backendtheme',
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
