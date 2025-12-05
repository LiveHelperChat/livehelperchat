# You are an expert Live Helper Chat software developer.
# Be concise!
# Take requests for writing code in an existing file.
# You must only write relevant lines.
# You must not recreate the entire file with the changes, write only necessary code that will get inserted.
# DO NOT repeat surrounding code, only generate the lines nessarary to directly insert into users code.
# Once you understand the request you MUST only return the corresponding code, not explanation.

# REFERENCE DOCUMENTATION
# For comprehensive architecture, patterns, and domain knowledge, refer to:
# `.github/instructions/` folder which contains:
# - 1-techstack.md - Technology stack and framework analysis
# - 2-file-categories.md - Complete file categorization and model inventory
# - 3-architectural-domains.json - Domain boundaries and constraints
# - 5-code-patterns.json - Design patterns used throughout codebase
# - 6-integration-points.json - Integration points and APIs
# - 4-domains/*.md - Detailed domain guides (api, auth, bot, caching, config, data-layer, departments, events, extensions, routing, ui, users)
# - 10-summary.json - Quick reference project summary

# Application folders structure

* `lhc_web/cache` - Stores cached files
* `lhc_web/design` - Contains design categories, templates, and frontend apps
* `lhc_web/doc` - Release documentation and database schema (update_db/structure.json)
* `lhc_web/extension` - All extensions/plugins are placed here
* `lhc_web/ezcomponents` - eZ Components core components (database, persistence, URL handling)
* `lhc_web/lib` - Core of the application
  * `lib/core` - Service classes and business logic
  * `lib/models` - Database model classes using erLhcoreClassDBTrait
  * `lib/vendor_lhc` - Dynamically loaded models and classes
* `lhc_web/lhcore_autoload.php` - Main application autoload file
* `lhc_web/modules` - Application modules organized by feature (lhchat, lhuser, lhdepartment, etc.)
* `lhc_web/pos` - Persistent Object definitions (ORM field type mappings)
* `lhc_web/settings` - Configuration files
* `lhc_web/translations` - Application translations

# Database structure reference

See: `lhc_web/doc/update_db/structure.json` for complete database schema
See: `.github/instructions/10-summary.json` for primary entities overview

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

# Technology Stack

## Backend
- **PHP 8.2+** - Primary backend language
- **Custom MVC Framework** - Built on eZ Components (see `.github/instructions/1-techstack.md`)
- **Slim Framework 4.14** - REST API endpoints
- **MySQL/MariaDB** - Primary database
- **Elasticsearch 7.x** - Advanced search (optional)

## Frontend
- **React 18** - Widget, bot builder, admin components
- **Svelte 4** - Operator dashboard
- **Redux** - State management with redux-thunk, redux-promise-middleware
- **AngularJS** - Legacy admin (being phased out)

## Build Tools
- **Webpack 5** - JavaScript bundling for React
- **Rollup** - Svelte bundling
- **Gulp 5** - Task automation
- **Babel** - JavaScript transpilation

For complete technology stack analysis, see: `.github/instructions/1-techstack.md`

# Core Patterns

## Model Pattern (Active Record via Trait)

See detailed examples and patterns in: `.github/instructions/5-code-patterns.json`

### Key Patterns Overview:
- **Singleton** - Service instance management (User, Cache, Dispatcher, Config)
- **Active Record** - Database models with built-in CRUD via `erLhcoreClassDBTrait`
- **Template Rendering** - PHP-based template instantiation with variable binding
- **Event Dispatch** - Event-driven architecture for extensibility
- **REST API Handler** - Authentication and JSON response handling
- **Permission Check** - Role-based access control validation
- **Query Builder** - Fluent database query construction
- **Cache Pattern** - Cache-aside pattern for expensive operations
- **Lifecycle Hooks** - Model lifecycle callbacks (beforeSave, afterSave, etc.)

Example Model Pattern:

```php
class erLhcoreClassModelChat {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_chat';
    public static $dbTableId = 'id';
    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';
    
    public $id;
    public $nick;
    public $status;
    // ... other properties
    
    public function getState() {
        return array(
            'id' => $this->id,
            'nick' => $this->nick,
            'status' => $this->status,
            // ... map all properties
        );
    }
}
```

## Controller Pattern

See detailed domain guides: `.github/instructions/4-domains/`

```php
// modules/lhchat/single.php
$currentUser = erLhcoreClassUser::instance();

// Permission check
if (!$currentUser->hasAccessTo('lhchat', 'use')) {
    erLhcoreClassModule::redirect('user/login');
    exit;
}

// Fetch data
$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

// Template rendering
$tpl = erLhcoreClassTemplate::getInstance('lhchat/single.tpl.php');
$tpl->set('chat', $chat);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'pagelayouts/parts/main.php';
```

## Event Dispatch Pattern

```php
// Dispatch events for extensibility
erLhcoreClassChatEventDispatcher::getInstance()->dispatch(
    'chat.chat_started',
    array('chat' => &$chat, 'msg' => &$msg)
);

// Listen for events (in extension bootstrap.php)
$dispatcher = erLhcoreClassChatEventDispatcher::getInstance();
$dispatcher->listen('chat.chat_started', function($params) {
    $chat = $params['chat'];
    // Custom logic here
});
```

## Cache Pattern

```php
$cache = CSCacheAPC::getMem();
$cacheKey = 'my_data_' . $id;

$data = $cache->restore($cacheKey);
if ($data === false) {
    $data = $this->loadExpensiveData($id);
    $cache->store($cacheKey, $data, 300); // 5 min TTL
}
```

## Permission Check Pattern

```php
$currentUser = erLhcoreClassUser::instance();

// Simple check
if (!$currentUser->hasAccessTo('lhchat', 'allowcloseremote')) {
    throw new Exception('No permission');
}

// Check with limitation return
$limitation = $currentUser->hasAccessTo('lhchat', array('allowcloseremote'), true);
if ($limitation === false) {
    // Check if user owns the resource
    if ($chat->user_id != $currentUser->getUserID()) {
        throw new Exception('No permission');
    }
}
```

## REST API Pattern

See: `.github/instructions/4-domains/api.md` for comprehensive API documentation

```php
// modules/lhrestapi/chats.php
erLhcoreClassRestAPIHandler::setHeaders();

$userData = erLhcoreClassRestAPIHandler::validateRequest();
if ($userData === false) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'message' => 'Unauthorized'
    ));
    exit;
}

$chats = erLhcoreClassModelChat::getList($filter);
echo erLhcoreClassRestAPIHandler::outputResponse(array(
    'error' => false,
    'chats' => $chats
));
```

# Primary Entities

See: `.github/instructions/10-summary.json` for complete entity overview with relationships

## Chat (`lh_chat`)
- Core conversation entity
- Status: 0=Pending, 1=Active, 2=Closed, 3=Chatbox, 4=Operators, 5=Bot
- Key fields: id, nick, status, user_id, dep_id, time, hash

## Message (`lh_msg`)
- Individual messages within chat
- Key fields: id, msg, time, chat_id, user_id, meta_msg

## User (`lh_users`)
- Operator/admin accounts
- Key fields: id, username, email, name, disabled, all_departments

## Department (`lh_departament`)
- Chat routing units
- Key fields: id, name, disabled, hidden, priority, online_hours_active

## Bot (`lh_generic_bot_bot`)
- Chatbot configurations
- Key fields: id, name, nick, configuration

## Bot Trigger (`lh_generic_bot_trigger`)
- Bot response actions
- Key fields: id, name, actions, bot_id, default

## Mail Conversation (`lhc_mailconv_conversation`)
- Email ticket conversations
- Key fields: id, subject, status, user_id, dep_id, mailbox_id

# User ID Conventions
- `-1` = System/Visitor message
- `-2` = Bot message
- `>0` = Operator user ID

# Naming Conventions

## Classes
- Models: `erLhcoreClassModel{Entity}`
- Services: `erLhcoreClass{Service}`
- Extensions: `erLhcoreClassExt{ExtName}`
- Abstracts: `erLhAbstractModel{Entity}`

## Methods
- Getters: `get{Property}()`
- Finders: `findOne()`, `fetch()`, `getList()`
- Actions: `saveThis()`, `updateThis()`, `removeThis()`

## Database
- Tables: `lh_{entity}`
- Extension tables: `lh_ext_{ext}_{entity}`
- Foreign keys: `{entity}_id`

# URL Generation

```php
// Relative URL
$url = erLhcoreClassDesign::baseurl('chat/single') . '/' . $chatId;
// Output: /site_admin/chat/single/123

// Absolute URL
$url = erLhcoreClassDesign::baseurldirect('chat/single/' . $chatId);
// Output: https://example.com/site_admin/chat/single/123

// With unordered params
$url = erLhcoreClassDesign::baseurl('user/account') . '/(tab)/settings';
```

# Common Event Names

- `chat.chat_started` - New chat started
- `chat.chat_closed` - Chat closed
- `chat.addmsguser` - Visitor sent message
- `chat.web_add_msg_admin` - Operator sent message
- `chat.before_chat_started` - Before chat creation
- `user.before_login` - Before user login
- `chat.genericbot_chat_command_transfer` - Bot transfer triggered

# Adding New Features Workflow

1. **Define Model** in `lib/models/` with `erLhcoreClassDBTrait`
2. **Create POS Definition** in `pos/` for ORM field types
3. **Add Controller** in `modules/lh{module}/`
4. **Create Templates** in `design/defaulttheme/tpl/`
5. **Define Permissions** in `module.php` FunctionList
6. **Dispatch Events** for extensibility

See: `.github/instructions/4-domains/` for detailed guidance on each domain

# Adding REST API Endpoint

1. Add handler in `modules/lhrestapi/`
2. Define in `modules/lhrestapi/module.php` ViewList
3. Validate with `erLhcoreClassRestAPIHandler`
4. Return JSON via `outputResponse()`

# Creating Extensions

## Extension Directory Structure

```
extension/myext/
├── bootstrap/
│   └── bootstrap.php         # Main bootstrap class with event listeners
├── classes/
│   └── erlhcoreclassmodel*.php  # Model classes
├── design/
│   └── myexttheme/
│       └── tpl/              # Template overrides
├── doc/
│   └── structure.json        # Database schema definition
├── modules/
│   └── lhmyext/
│       ├── module.php        # Route and permission definitions
│       └── *.php             # Controller files
├── pos/
│   └── lhmyext/              # POS (Persistent Object) definitions
├── providers/                # Namespaced service classes
├── settings/
│   └── settings.ini.default.php  # Configuration with defaults
└── translations/             # Language files
```

See: `.github/instructions/4-domains/extensions.md` for comprehensive extension development guide

# Important Service Classes

- `erLhcoreClassUser` - Authentication, permissions (singleton)
- `erLhcoreClassChat` - Chat operations, session handling
- `erLhcoreClassChatEventDispatcher` - Event system (singleton)
- `erLhcoreClassGenericBotWorkflow` - Bot execution
- `erLhcoreClassRestAPIHandler` - API authentication/responses
- `CSCacheAPC` - Cache operations
- `erLhcoreClassDesign` - URL generation, asset paths
- `erLhcoreClassTemplate` - Template rendering
- `erLhcoreClassModule` - Module routing, redirects

For domain-specific service classes, see: `.github/instructions/4-domains/`

# Department Access Control

```php
// Get user's accessible departments
$departments = erLhcoreClassUserDep::getUserReadDepartments($currentUser->getUserID());

// Apply department limitation to queries
$limitation = erLhcoreClassChat::getDepartmentLimitation('lh_chat', $departments);
if ($limitation !== false && $limitation !== true) {
    $filter['customfilter'][] = $limitation;
}
```

For detailed department structure, see: `.github/instructions/4-domains/departments.md`

# Bot Workflow

Bot triggers are processed via:
1. `erLhcoreClassGenericBotWorkflow::process($chat, $trigger)`
2. Actions array in trigger defines responses
3. Events processed: `Update current chat`, `Send message`, `Collect info`, etc.
4. Transfer to human: `chat.genericbot_chat_command_transfer` event

For comprehensive bot development, see: `.github/instructions/4-domains/bot.md`

# Mail Conversation Integration

- Model: `erLhcoreClassModelMailconvConversation`
- Messages: `erLhcoreClassModelMailconvMsg`
- Mailbox config: `erLhcoreClassModelMailconvMailbox`
- Sync via IMAP cron jobs

For comprehensive mail integration details, see: `.github/instructions/4-domains/data-layer.md`

# React Widget State (Redux)

```javascript
// Store structure
{
    chatwidget: {
        chatData: {},        // Chat configuration
        chatLiveData: {},    // Real-time chat state
        onlineData: {}       // Online visitor tracking
    }
}

// Common actions
store.dispatch({type: 'chat/addMessage', data: message});
store.dispatch({type: 'chatLiveData/setStatus', data: status});
```

See: `.github/instructions/4-domains/ui.md` for detailed UI architecture

# Svelte Dashboard

```javascript
// Store pattern
import { writable, derived } from 'svelte/store';

export const chats = writable([]);
export const activeChat = derived(chats, $chats => 
    $chats.find(c => c.selected)
);
```

# Security Considerations

- Use `erLhcoreClassUser::instance()->hasAccessTo()` for permissions
- Always validate API requests with `erLhcoreClassRestAPIHandler::validateRequest()`
- Escape output in templates with `htmlspecialchars()` or `<?php echo erLhcoreClassDesign::shrt($var,100,'',true,true); ?>`
- Use parameterized queries via `getList()` filters
- Never expose internal IDs without hash validation

For comprehensive security guidance, see: `.github/instructions/9-security.md`