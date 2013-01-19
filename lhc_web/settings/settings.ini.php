<?php
return array (
  'settings' => 
  array (
    'site' => 
    array (
      'title' => 'Live helper chat 1.16v',
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
      'available_site_access' => 
      array (
        0 => 'eng',
        1 => 'lit',
        2 => 'site_admin',
      ),           
    ),
    'default_url' => 
    array (
      'module' => 'chat',
      'view' => 'startchat',
    ),
    'extensions' => 
      array (
        // 0 => 'customstatus',
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
