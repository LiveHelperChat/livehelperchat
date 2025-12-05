# Configuration Domain - Settings Management

## Overview

Live Helper Chat uses a cascading configuration system with settings stored in INI-style PHP arrays, database tables, and cached configuration.

## Configuration Files

### Default Settings

```php
// settings/settings.ini.default.php
<?php
return array(
    'settings' => array(
        'site' => array(
            'title' => 'Live Helper Chat',
            'locale' => 'en_EN',
            'theme' => 'defaulttheme',
            'installed' => false,
            'debug_output' => false,
            'templatecache' => false,
            'templatecompile' => false,
            'time_zone' => 'UTC',
            'extensions' => array(),
            // ...
        ),
        'webhooks' => array(
            'enabled' => false,
            'worker' => 'http',
        ),
        'chat' => array(
            'online_timeout' => 300,
            'back_office_sinterval' => 10,
            'chat_message_sinterval' => 3.5,
        ),
        'db' => array(
            'host' => 'localhost',
            'database' => 'lhc',
            'port' => 3306,
        ),
        'cacheEngine' => array(
            'className' => false,
            'cache_global_key' => '',
        ),
    ),
);
```

### User Settings Override

```php
// settings/settings.ini.php (created during installation, gitignored)
<?php
return array(
    'settings' => array(
        'site' => array(
            'installed' => true,
            'secrethash' => 'random_generated_hash',
            'extensions' => array('myextension'),
        ),
        'db' => array(
            'host' => '127.0.0.1',
            'user' => 'lhc_user',
            'password' => 'secure_password',
            'database' => 'livehelperchat',
        ),
        'cacheEngine' => array(
            'className' => 'lhRedis',
            'cache_global_key' => 'lhc_prod_',
        ),
    ),
);
```

## Configuration Class

### Accessing Settings

```php
// lib/core/lhconfig/lhconfig.php
class erConfigClassLhConfig {
    
    private static $instance = null;
    private $settings = array();
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new erConfigClassLhConfig();
        }
        return self::$instance;
    }
    
    public function getSetting($section, $key, $default = null)
    {
        if (isset($this->settings['settings'][$section][$key])) {
            return $this->settings['settings'][$section][$key];
        }
        return $default;
    }
    
    public function setSetting($section, $key, $value)
    {
        $this->settings['settings'][$section][$key] = $value;
    }
}
```

### Usage Examples

```php
$cfg = erConfigClassLhConfig::getInstance();

// Get setting with default
$timeout = $cfg->getSetting('chat', 'online_timeout', 300);

// Check boolean setting
$debugEnabled = $cfg->getSetting('site', 'debug_output', false);

// Get nested array
$extensions = $cfg->getSetting('site', 'extensions', array());

// Get database config
$dbHost = $cfg->getSetting('db', 'host');
$dbName = $cfg->getSetting('db', 'database');
```

## Database Configuration

### Chat Config Model

```php
// lib/models/lhchat/erlhcoreclassmodelchatconfig.php
class erLhcoreClassModelChatConfig {
    
    public static $dbTable = 'lh_chat_config';
    
    public $identifier;  // Primary key (string)
    public $value;
    public $type;
    public $explain;
    public $hidden;
    
    public static function fetch($identifier)
    {
        $cache = CSCacheAPC::getMem();
        $cacheKey = 'chat_config_' . $identifier;
        
        $config = $cache->restore($cacheKey);
        if ($config === false) {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare('SELECT * FROM lh_chat_config WHERE identifier = :id');
            $stmt->bindValue(':id', $identifier);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $config = new self();
                $config->setState($row);
                $cache->store($cacheKey, $config);
            }
        }
        
        return $config;
    }
}
```

### Using Database Config

```php
// Fetch config value
$config = erLhcoreClassModelChatConfig::fetch('message_length_limit');
$maxLength = $config ? (int)$config->value : 500;

// Update config value
$config = erLhcoreClassModelChatConfig::fetch('site_name');
if ($config) {
    $config->value = 'New Site Name';
    $config->updateThis();
}
```

## User Settings

### Model

```php
// lib/models/lhuser/erlhcoreclassmodelusersetting.php
class erLhcoreClassModelUserSetting {
    
    public $id;
    public $user_id;
    public $identifier;
    public $value;
    
    public static function setSetting($user_id, $identifier, $value)
    {
        $existing = self::findOne(array(
            'filter' => array(
                'user_id' => $user_id,
                'identifier' => $identifier
            )
        ));
        
        if ($existing) {
            $existing->value = $value;
            $existing->updateThis();
        } else {
            $setting = new self();
            $setting->user_id = $user_id;
            $setting->identifier = $identifier;
            $setting->value = $value;
            $setting->saveThis();
        }
        
        // Update cache
        CSCacheAPC::getMem()->store(
            'settings_user_id_' . $user_id . '_' . $identifier,
            $value
        );
    }
    
    public static function getSetting($user_id, $identifier, $default = null)
    {
        $cache = CSCacheAPC::getMem();
        $cacheKey = 'settings_user_id_' . $user_id . '_' . $identifier;
        
        $value = $cache->restore($cacheKey);
        if ($value === false) {
            $setting = self::findOne(array(
                'filter' => array(
                    'user_id' => $user_id,
                    'identifier' => $identifier
                )
            ));
            
            $value = $setting ? $setting->value : $default;
            $cache->store($cacheKey, $value);
        }
        
        return $value;
    }
}
```

### Usage

```php
// Get user setting
$notificationsEnabled = erLhcoreClassModelUserSetting::getSetting(
    $userId,
    'notifications_enabled',
    true  // default
);

// Set user setting
erLhcoreClassModelUserSetting::setSetting(
    $userId,
    'notifications_enabled',
    false
);
```

## Extension Configuration

### Extension Settings File

```php
// extension/myext/settings/settings.ini.php
<?php
return array(
    'myext' => array(
        'api_url' => 'https://api.example.com',
        'api_key' => '',
        'debug' => false,
        'features' => array(
            'feature_a' => true,
            'feature_b' => false,
        ),
    ),
);
```

### Loading Extension Config

```php
// In extension bootstrap or class
class erLhcoreClassExtMyExt {
    
    private static $settings = null;
    
    public static function getSettings()
    {
        if (self::$settings === null) {
            $settingsFile = 'extension/myext/settings/settings.ini.php';
            if (file_exists($settingsFile)) {
                self::$settings = include $settingsFile;
            } else {
                self::$settings = array('myext' => array());
            }
        }
        return self::$settings['myext'];
    }
    
    public static function getSetting($key, $default = null)
    {
        $settings = self::getSettings();
        return $settings[$key] ?? $default;
    }
}
```

## Configuration Caching

### File-Based Cache

```php
// lib/core/lhconfig/lhcacheconfig.php
class erLhcoreClassCacheStorage {
    
    private $cacheDir;
    
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }
    
    public function store($identifier, array $data)
    {
        // Atomic write
        $tempFile = $this->cacheDir . md5($identifier . microtime()) . '.php';
        $targetFile = $this->cacheDir . $identifier . '.cache.php';
        
        file_put_contents(
            $tempFile,
            "<?php\n return " . var_export($data, true) . ";\n?>",
            LOCK_EX
        );
        
        rename($tempFile, $targetFile);
    }
    
    public function restore($identifier)
    {
        $file = $this->cacheDir . $identifier . '.cache.php';
        if (file_exists($file)) {
            return include $file;
        }
        return false;
    }
}
```

## Environment-Based Configuration

```php
// settings/settings.ini.php
<?php

$env = getenv('LHC_ENVIRONMENT') ?: 'production';

$baseConfig = array(
    'settings' => array(
        'db' => array(
            'host' => getenv('DB_HOST') ?: 'localhost',
            'database' => getenv('DB_NAME') ?: 'lhc',
            'user' => getenv('DB_USER') ?: 'root',
            'password' => getenv('DB_PASS') ?: '',
        ),
    ),
);

// Environment-specific overrides
if ($env === 'development') {
    $baseConfig['settings']['site']['debug_output'] = true;
    $baseConfig['settings']['site']['templatecache'] = false;
}

return $baseConfig;
```

## Best Practices

1. **Never hardcode sensitive values:**
   ```php
   // Bad
   'api_key' => 'sk-1234567890'
   
   // Good
   'api_key' => getenv('API_KEY')
   ```

2. **Provide sensible defaults:**
   ```php
   $timeout = $cfg->getSetting('chat', 'timeout', 300);
   ```

3. **Cache frequently accessed config:**
   ```php
   $cache = CSCacheAPC::getMem();
   $config = $cache->restore('myconfig');
   if ($config === false) {
       $config = loadExpensiveConfig();
       $cache->store('myconfig', $config, 3600);
   }
   ```

4. **Document configuration options:**
   ```php
   // In settings.ini.default.php
   'option' => array(
       'my_setting' => true,  // Description of what this does
   ),
   ```

5. **Validate configuration on load:**
   ```php
   public static function validateConfig($config)
   {
       $required = array('db.host', 'db.database', 'site.secrethash');
       foreach ($required as $key) {
           $parts = explode('.', $key);
           if (!isset($config['settings'][$parts[0]][$parts[1]])) {
               throw new Exception("Missing required config: $key");
           }
       }
   }
   ```
