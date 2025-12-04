# Caching Domain - Cache System

## Overview

Live Helper Chat uses a multi-level caching system through the `CSCacheAPC` class, supporting various backend engines (APCu, Memcached, Redis, File).

## Cache Architecture

### Cache Class

```php
// lib/core/lhcore/lhsys.php
#[\AllowDynamicProperties]
class CSCacheAPC {

    static private $m_objMem = NULL;
    public $cacheEngine = null;
    public $cacheGlobalKey = null;
    
    // Cache version keys for invalidation
    public $cacheKeys = array(
        'site_version'
    );

    function __construct()
    {
        $cacheEngineClassName = erConfigClassLhConfig::getInstance()->getSetting('cacheEngine', 'className');
        $this->cacheGlobalKey = erConfigClassLhConfig::getInstance()->getSetting('cacheEngine', 'cache_global_key');

        if ($cacheEngineClassName !== false) {
            $this->cacheEngine = new $cacheEngineClassName();
        }
    }

    static function getMem()
    {
        if (self::$m_objMem == NULL) {
            self::$m_objMem = new CSCacheAPC();
        }
        return self::$m_objMem;
    }
}
```

## Basic Operations

### Store and Restore

```php
$cache = CSCacheAPC::getMem();

// Store with default TTL
$cache->store('my_key', $value);

// Store with custom TTL (seconds)
$cache->store('my_key', $value, 3600);

// Restore value
$value = $cache->restore('my_key');
if ($value === false) {
    // Cache miss - load from database
    $value = loadFromDatabase();
    $cache->store('my_key', $value);
}
```

### Delete

```php
$cache = CSCacheAPC::getMem();

// Delete single key
$cache->delete('my_key');

// Delete multiple keys
$cache->delete('key1');
$cache->delete('key2');
```

### Multi-Get

```php
$cache = CSCacheAPC::getMem();

$keys = array('key1', 'key2', 'key3');
$values = $cache->restoreMulti($keys);

// Returns: array('key1' => $value1, 'key2' => $value2, ...)
```

## Session Caching

```php
$cache = CSCacheAPC::getMem();

// Set session value (stored in $_SESSION and optionally $GLOBALS)
$cache->setSession('identifier', $value, false, false);

// Set session + global cache
$cache->setSession('identifier', $value, true, false);

// Set global only (skip $_SESSION)
$cache->setSession('identifier', $value, true, true);

// Get session value
$value = $cache->getSession('identifier', false);

// Get with global fallback
$value = $cache->getSession('identifier', true);
```

### Session Arrays

```php
$cache = CSCacheAPC::getMem();

// Append to session array
$cache->appendToArray('viewed_chats', $chatId);

// Remove from array
$cache->removeFromArray('viewed_chats', $chatId);

// Get array
$viewedChats = $cache->getArray('viewed_chats');
```

## Cache Versioning

### Purpose

Cache versioning allows bulk invalidation of related cache entries:

```php
$cache = CSCacheAPC::getMem();

// Get current version
$version = $cache->getCacheVersion('departments_version');

// Build cache key with version
$cacheKey = 'departments_list_v' . $version;

// Store with version
$cache->store($cacheKey, $departments);

// Invalidate all versioned caches
$cache->increaseCacheVersion('departments_version');
```

### Model Cache Clearing

```php
// lib/core/lhcore/lhdbtrait.php
public function clearCache()
{
    $cache = CSCacheAPC::getMem();
    
    // Increase global model version
    $cache->increaseCacheVersion('site_attributes_version_' . strtolower(__CLASS__));

    // Delete specific object cache
    if (isset($this->id)) {
        $cache->delete('object_' . strtolower(__CLASS__) . '_' . $this->id);

        // Clear global cache
        if (isset($GLOBALS[__CLASS__ . $this->id])) {
            unset($GLOBALS[__CLASS__ . $this->id]);
        }
    }

    // Call custom cache clearing
    $this->clearCacheClassLevel();
}
```

## SQL Query Caching

```php
// Enable caching in getList
$departments = erLhcoreClassModelDepartament::getList(array(
    'filter' => array('disabled' => 0),
    'enable_sql_cache' => true,
    'sql_cache_timeout' => 300  // 5 minutes
));

// With custom cache key
$chats = erLhcoreClassModelChat::getList(array(
    'filter' => array('dep_id' => $depId),
    'enable_sql_cache' => true,
    'cache_key' => 'dep_' . $depId . '_chats'
));

// With automatic versioning
$cache = CSCacheAPC::getMem();
$version = $cache->getCacheVersion('site_attributes_version_erlhcoreclassmodelchat');
$cacheKey = 'chat_list_v' . $version;
```

## Configuration Cache

```php
// lib/core/lhconfig/lhcacheconfig.php
class erConfigClassLhCacheConfig {
    
    private static $storage = null;
    
    public static function getStorage()
    {
        if (self::$storage === null) {
            self::$storage = new erLhcoreClassCacheStorage('cache/cacheconfig/');
        }
        return self::$storage;
    }
}

// Usage in config class
public function getSetting($section, $key, $default = null)
{
    $cache = CSCacheAPC::getMem();
    $cacheKey = "config_{$section}_{$key}";
    
    $value = $cache->restore($cacheKey);
    if ($value === false) {
        $value = $this->loadFromFile($section, $key, $default);
        $cache->store($cacheKey, $value);
    }
    
    return $value;
}
```

## Template Caching

```php
// lib/core/lhtpl/tpl.php
class erLhcoreClassTemplate {
    
    private $cacheEnabled = true;
    private $templatecompile = true;
    
    public function fetch()
    {
        if ($this->cacheEnabled && $this->isCached()) {
            return $this->loadFromCache();
        }
        
        $output = $this->compile();
        
        if ($this->cacheEnabled) {
            $this->saveToCache($output);
        }
        
        return $output;
    }
}
```

## Cache Configuration

```php
// settings/settings.ini.php
'cacheEngine' => array(
    'className' => 'lhRedis',  // or 'lhMemcache', 'lhApc', false
    'cache_global_key' => 'lhc_unique_key_here'
),

'redis' => array(
    'host' => '127.0.0.1',
    'port' => 6379,
    'password' => '',
    'db' => 0
),

'memcache' => array(
    'host' => 'localhost',
    'port' => 11211
),
```

## Object-Level Caching

```php
// Model fetch with cache
class erLhcoreClassModelChat {
    
    public static function fetch($id, $useCache = true)
    {
        // Check global variable cache
        if (isset($GLOBALS[__CLASS__ . $id]) && $useCache) {
            return $GLOBALS[__CLASS__ . $id];
        }

        $object = self::getSession()->load(__CLASS__, $id);

        if ($useCache) {
            $GLOBALS[__CLASS__ . $id] = $object;
        }

        return $object;
    }

    // With memcache layer
    public static function fetchCache($id)
    {
        $cache = CSCacheAPC::getMem();
        $cacheKey = 'object_' . strtolower(__CLASS__) . '_' . $id;

        $object = $cache->restore($cacheKey);
        if ($object === false) {
            $object = self::fetch($id, true);
            $cache->store($cacheKey, $object);
        }

        return $object;
    }
}
```

## Best Practices

1. **Use cache versioning for related data:**
   ```php
   // When department changes, invalidate all related caches
   $cache->increaseCacheVersion('department_' . $depId);
   ```

2. **Set appropriate TTLs:**
   ```php
   // Frequently changing data: short TTL
   $cache->store('online_operators', $list, 60);
   
   // Static data: longer TTL
   $cache->store('system_config', $config, 3600);
   ```

3. **Handle cache misses gracefully:**
   ```php
   $value = $cache->restore($key);
   if ($value === false) {
       $value = $this->expensiveOperation();
       $cache->store($key, $value, 300);
   }
   ```

4. **Clear cache after writes:**
   ```php
   $model->saveThis();  // Automatically calls clearCache()
   ```

5. **Use cache keys consistently:**
   ```php
   // Good: prefixed and structured
   $key = "chat:{$chatId}:messages:{$page}";
   
   // Avoid: unpredictable keys
   $key = md5(serialize($params));  // Hard to invalidate
   ```

6. **Monitor cache hit rates:**
   ```php
   // Log cache misses for optimization
   $value = $cache->restore($key);
   if ($value === false) {
       erLhcoreClassLog::write("Cache miss: $key");
   }
   ```
