# You are an expert Live Helper Chat software developer.
# Be concise!
# Take requests for writing code in an existing file.
# You must only write relevant lines.
# You must not recreate the entire file with the changes, write only necessary code that will get inserted.
# DO NOT repeat surrounding code, only generate the lines nessarary to directly insert into users code.
# Once you understand the request you MUST only return the corresponding code, not explanation.

# Application folders structure

* `lhc_web/cache` - Stores cached files
* `lhc_web/design` - Contains design categories
* `lhc_web/doc` - Release documentation
* `lhc_web/extension` - All extensions are placed here
* `lhc_web/ezcomponents` - eZ Components core components
* `lhc_web/lib` - Core of the application
* `lhc_web/autoloads` - application statically defined autoloads. Should not be used anymore to define new classes.
* `lhc_web/lhcore_autoload.php` - Main application autoload file
* `lhc_web/core` - Folder containing application logic modules
* `lhc_web/models` - Folder containing application model classes
* `lhc_web/modules` - Application modules are placed here
* `lhc_web/pos` - Represents eZ Components POS, persistent object tables definitions
* `lhc_web/settings` - Contains application settings files
* `lhc_web/translations ` - Contains application translations

# Database structure you can find in

lhc_web/doc/update_db/structure.json

# MVC Pattern workflow

## Where are models defined?

* Models classes are defined `lhc_web/lib/models` 
* Models classes with dynamic autoload are defined at `lhc_web/lib/vendor_lhc/LiveHelperChat/Models`
* `pos` files for their models for types of fields are defined in `lhc_web/pos` 
* `pos` files or classes with dynamic autoload are defined in `lhc_web/pos/lhabstract/livehelperchat/models` 

## Database Layer and ORM

* All model classes use the `erLhcoreClassDBTrait` trait located at `lhc_web/lib/core/lhcore/lhdbtrait.php`
* This trait provides common database operations like save, update, delete, and query functionality
* Models must define static properties: `$dbTable`, `$dbTableId`, `$dbDefaultSort`, `$dbSortOrder`
* Database session handler is configured via static properties `$dbSessionHandler` and `$dbSessionHandlerUrl`

### Common Database Operations

* `setState($properties)` - Set multiple object properties from array
* `saveThis($params)` - Save or update record with lifecycle hooks
* `saveThisOnly($params)` - Save new record only
* `saveOrUpdate($params)` - Alias for saveThis
* `updateThis($params)` - Update existing record
* `removeThis()` - Delete record
* `syncAndLock($columns)` - Lock record and sync columns from database
* `refreshThis()` - Refresh object from database
* `clearCache()` - Clear cached data for object
* `getFields()` - Get field definitions from PHP include file
* `fetch($id, $useCache, $throwException)` - Load single record by ID
* `fetchAndLock($id, $useCache)` - Load single record by ID with database lock
* `fetchCache($id)` - Load with memcache support
* `isOwner($id, $skipChecking)` - Check if current user owns object
* `findOne($params)` - Find single record with conditions
* `getList($params)` - Get list of records with filtering/sorting
* `getCount($params, $operation, $field, $rawSelect, $fetchColumn, $fetchAll, $fetchColumnAll, $groupedCount)` - Count or aggregate records
* `estimateRows()` - Get estimated table row count from information_schema

### Query Parameters

* `filter` - Exact match conditions
  ```php
  // Example: Get chats with specific status
  $chats = erLhcoreClassModelChat::getList(array(
      'filter' => array('status' => 1)
  ));
  ```
* `filterfields` - Multiple filter combinations
  ```php
  // Example: Multiple filter combinations for different conditions
  $chats = erLhcoreClassModelChat::getList(array(
      'filterfields' => array(
          array('status' => 1, 'dep_id' => 5),
          array('status' => 2, 'dep_id' => 10)
      )
  ));
  ```
* `filterin` - IN clause conditions
  ```php
  // Example: Get chats from multiple departments
  $chats = erLhcoreClassModelChat::getList(array(
      'filterin' => array('dep_id' => array(1, 2, 3, 4))
  ));
  ```
* `filterinfields` - Multiple IN clause combinations
  ```php
  // Example: Multiple IN clause combinations for different conditions
  $chats = erLhcoreClassModelChat::getList(array(
      'filterinfields' => array(
          array('dep_id' => array(1, 2, 3)),
          array('status' => array(0, 1))
      )
  ));
  ```
* `filterlike` - LIKE conditions with wildcards (%value%)
  ```php
  // Example: Search chats by visitor nick containing text
  $chats = erLhcoreClassModelChat::getList(array(
      'filterlike' => array('nick' => 'John')
  ));
  ```
* `filterlikefields` - Multiple LIKE combinations
  ```php
  // Example: Multiple LIKE combinations for different conditions
  $chats = erLhcoreClassModelChat::getList(array(
      'filterlikefields' => array(
          array('nick' => 'John', 'email' => 'gmail'),
          array('phone' => '555', 'referrer' => 'google')
      )
  ));
  ```
* `filterlikeright` - Right LIKE conditions (value%)
  ```php
  // Example: Search for emails starting with specific text
  $chats = erLhcoreClassModelChat::getList(array(
      'filterlikeright' => array('email' => 'admin@')
  ));
  ```
* `filternotlikefields` - NOT LIKE conditions
* `filtergt/filterlt/filtergte/filterlte` - Comparison operators
  ```php
  // Example: Get chats newer than timestamp
  $chats = erLhcoreClassModelChat::getList(array(
      'filtergt' => array('time' => time() - 3600)
  ));
  ```
* `filtergtfields/filterltfields/filtergtefields/filterltefields` - Multiple comparison combinations
* `filtergtenbind/filterltenbind` - Comparison without parameter binding
* `filternot` - NOT conditions
  ```php
  // Example: Get chats not in pending status
  $chats = erLhcoreClassModelChat::getList(array(
      'filternot' => array('status' => 0)
  ));
  ```
* `filternotfields` - Multiple NOT combinations
* `filternotin` - NOT IN conditions
  ```php
  // Example: Exclude specific departments
  $chats = erLhcoreClassModelChat::getList(array(
      'filternotin' => array('dep_id' => array(1, 2))
  ));
  ```
* `filterall` - ALL IN conditions
* `filterlor` - OR conditions across same field
  ```php
  // Example: Get chats with multiple status values using OR
  $chats = erLhcoreClassModelChat::getList(array(
      'filterlor' => array('status' => array(1, 2))
  ));
  ```
* `filterlorf` - OR conditions across different fields
* `leftjoin/innerjoin/leftouterjoin` - JOIN operations
  ```php
  // Example: Join with department table
  $chats = erLhcoreClassModelChat::getList(array(
      'leftjoin' => array('lh_departament' => array('lh_chat.dep_id', 'lh_departament.id'))
  ));
  ```
* `leftjoinraw` - Raw LEFT JOIN with custom conditions
* `innerjoinsame` - INNER JOIN with table alias
* `sort` - ORDER BY clause
  ```php
  // Example: Sort by creation time descending
  $chats = erLhcoreClassModelChat::getList(array(
      'sort' => 'time DESC'
  ));
  ```
* `limit/offset` - LIMIT/OFFSET for pagination
  ```php
  // Example: Get 20 chats starting from position 40
  $chats = erLhcoreClassModelChat::getList(array(
      'limit' => 20,
      'offset' => 40
  ));
  ```
* `group/having` - GROUP BY/HAVING clauses
* `use_index` - Force index usage
* `select_columns` - Custom SELECT columns
* `lock` - Enable FOR UPDATE locking
  ```php
  // Example: Lock records for update
  $chats = erLhcoreClassModelChat::getList(array(
      'lock' => true,
      'filter' => array('status' => 0)
  ));
  ```
* `ignore_fields` - Fields to ignore in SELECT
* `enable_sql_cache` - Enable query result caching
  ```php
  // Example: Enable caching with custom timeout
  $chats = erLhcoreClassModelChat::getList(array(
      'enable_sql_cache' => true,
      'sql_cache_timeout' => 3600
  ));
  ```
* `cache_key` - Custom cache key
* `sql_cache_timeout` - Cache timeout duration
* `filter_custom/customfilter` - Raw custom conditions
  ```php
  // Example: Custom SQL condition
  $chats = erLhcoreClassModelChat::getList(array(
      'customfilter' => array('(nick != "" OR email != "")')
  ));
  ```
* `prefill_attributes` - Auto-populate related objects
  ```php
  // Example: Prefill department objects
  $chats = erLhcoreClassModelChat::getList(array(
      'prefill_attributes' => array(
          'department' => array(
              'attr_id' => 'dep_id',
              'attr_name' => 'department',
              'function' => 'erLhcoreClassModelDepartament::getList'
          )
      )
  ));
  ```

### Lifecycle Hooks

* `beforeSave($params)` - Called before save operations
* `afterSave($params)` - Called after save operations  
* `beforeUpdate($params)` - Called before update operations
* `afterUpdate($params)` - Called after update operations
* `beforeRemove()` - Called before delete operations
* `afterRemove()` - Called after delete operations
* `clearCacheClassLevel()` - Override for custom cache clearing

# How URL path is determined/resolved?

* `site_admin` is a `siteaccess` pattern. Defined in `lhc_web/settings/settings.ini.default.php` file.
* E.g if URL is `/site_admin/genericbot/bot` it resolves to `lhc_web/modules/lhgenericbot/bot.php`
* URL are defined in each `lhc_web/modules/lh<mmodule>/module.php` file. Parameters also.

## How to override URL controller file?

Let say you have extension `customstatus` and want to override `chat/start` URL. You would need

* Controller file `extension/customstatus/modules/lhchat/start.php`
* Module definition file `extension/customstatus/modules/lhchat/module.php` with content of
```php
<?php
$Module = array( "name" => "Chat module");
$ViewList = array();
$ViewList['start'] = array(
    'params' => array(),
    'uparams' => array()
);
$FunctionList = array();
$FunctionList['my_cystom_function'] = array('explain' => 'My custom permission if required');
```

# How is a template path determined?

* `erLhcoreClassTemplate::getInstance('lhgenericbot/bot.tpl.php');` resolves in the following order
  * `lhc_web/design/<theme-folder>/tpl/lhgenericbot/bot.tpl.php` Search is done as many times there are `<theme-folder>` folders.
* If extensions are used E.g `'extensions' => ['fbmessenger']` we look first at extensions folder using following pattern
* `extension/<extension name>/design/<extension name>theme/tpl/lhgenericbot/bot.tpl.php`.

## How to override template?

Create a template file with an identical path to `defaulttheme` folder. 

* E.g `design/defaulttheme/tpl/lhchat/start.tpl.php` can be overridden if file with path `extension/<extension name>/design/<extension name>theme/tpl/lhchat/stsart.tpl.php` exists
* E.g `design/defaulttheme/tpl/lhchat/start.tpl.php` can be overridden if file with path `design/customtheme/tpl/lhchat/start.tpl.php` exists

# Main components

### Widget embeded in website.

* `lhc_web/design/defaulttheme/widget/react-app` - Build with React.
* `lhc_web/design/defaulttheme/widget/wrapper` - Wrapper of the widget application. Vanilla JS

### Dashboard Svelte component

* `lhc_web/design/defaulttheme/js/svelte` - Svelte application for the dashboard.

### Bot builder application

* `lhc_web/design/defaulttheme/js/react` - Written with React

### Back office related small JS apps (Canned messages suggester, Mail support, Dashboard chat tabs, Group Chats)

* `lhc_web/design/defaulttheme/js/admin` - Back office chat application. Written with React