# Extensions Domain - Plugin System

## Overview

Live Helper Chat supports extensions that can override controllers, templates, add event listeners, and extend functionality. Extensions are self-contained modules placed in the `extension/` directory.

## Extension Structure

Based on production extensions like fbmessenger, the complete structure is:

```
extension/
└── myextension/
    ├── bootstrap/
    │   └── bootstrap.php           # Main bootstrap class with event listeners
    ├── classes/
    │   └── erlhcoreclassmodel*.php # Model classes with custom session handler
    ├── design/
    │   └── myextensiontheme/
    │       └── tpl/                # Template overrides
    │           └── lhchat/
    │               └── chat.tpl.php
    ├── doc/
    │   └── structure.json          # Database schema definition
    ├── modules/
    │   └── lhmyextension/
    │       ├── module.php          # Route and permission definitions
    │       └── *.php               # Controller files
    ├── pos/
    │   └── lhmyextension/          # POS (Persistent Object) definitions
    ├── providers/                  # Namespaced service classes (PSR-4)
    │   └── MyService.php
    ├── settings/
    │   └── settings.ini.default.php # Configuration with env var support
    └── translations/               # Language files
```

## Enabling Extensions

```php
// settings/settings.ini.php
'site' => array(
    'extensions' => array(
        'myextension',
        'anotherextension'
    ),
),
```

## Bootstrap Class Pattern (Production Style)

Modern extensions use a class-based bootstrap with the `#[\AllowDynamicProperties]` attribute:

```php
<?php
// extension/myextension/bootstrap/bootstrap.php
#[\AllowDynamicProperties]
class erLhcoreClassExtensionMyextension
{
    public function __construct() {}

    public function run()
    {
        $dispatcher = erLhcoreClassChatEventDispatcher::getInstance();

        // Register event listeners
        $dispatcher->listen('chat.chat_started', array($this, 'onChatStarted'));
        $dispatcher->listen('chat.addmsguser', array($this, 'onMessageReceived'));
        $dispatcher->listen('chat.web_add_msg_admin', array($this, 'onOperatorMessage'));
        $dispatcher->listen('chat.chat_closed', array($this, 'onChatClosed'));
        
        // Include extension classes
        include_once __DIR__ . '/../classes/autoload.php';
    }

    public function onChatStarted($params)
    {
        $chat = $params['chat'];
        // Extension logic here
    }
    
    public function onMessageReceived($params)
    {
        $chat = $params['chat'];
        $msg = $params['msg'];
        // Process incoming message
    }

    /**
     * Custom session handler for extension models
     * This allows extension models to use their own POS definitions
     */
    public static function getSession($url = false)
    {
        if (!isset($GLOBALS['ext_myextension_persistent_session'])) {
            $GLOBALS['ext_myextension_persistent_session'] = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager('./extension/myextension/pos')
            );
        }
        return $GLOBALS['ext_myextension_persistent_session'];
    }
}
```

## Controller Override

### Module Definition

```php
// extension/myextension/modules/lhchat/module.php
<?php
$Module = array("name" => "Chat module");

$ViewList = array();

// Override existing view
$ViewList['single'] = array(
    'params' => array('chat_id'),
    'functions' => array('use')
);

// Add new view
$ViewList['mycustom'] = array(
    'params' => array('id'),
    'uparams' => array('tab'),
    'functions' => array('use')
);

$FunctionList = array();
$FunctionList['myfunction'] = array('explain' => 'My custom permission');
```

### Controller File

```php
// extension/myextension/modules/lhchat/single.php
<?php

// Get parameters
$chatId = $Params['user_parameters']['chat_id'];

// Load chat
$chat = erLhcoreClassModelChat::fetch($chatId);

// Custom extension logic
$extensionData = erLhcoreClassExtMyExtension::getCustomData($chat);

// Render template (uses extension's template if exists)
$tpl = erLhcoreClassTemplate::getInstance('lhchat/single.tpl.php');
$tpl->set('chat', $chat);
$tpl->set('extension_data', $extensionData);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'pagelayouts/parts/main.php';
```

## Template Override

Templates placed in extension theme folder take precedence:

```php
// extension/myextension/design/myextensiontheme/tpl/lhchat/chat.tpl.php
<div class="my-extension-wrapper">
    <?php include(erLhcoreClassDesign::designtpl('lhchat/parts/header.tpl.php')); ?>
    
    <div class="custom-content">
        <h2><?php echo htmlspecialchars($chat->nick); ?></h2>
        
        <?php if (isset($extension_data)): ?>
            <div class="extension-info">
                <?php echo $extension_data['custom_field']; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include(erLhcoreClassDesign::designtpl('lhchat/parts/messages.tpl.php')); ?>
</div>
```

## Extension Classes

```php
// extension/myextension/lib/myextension.php
<?php

class erLhcoreClassExtMyExtension {
    
    public static function onChatStarted($params)
    {
        $chat = $params['chat'];
        
        // Log custom event
        erLhcoreClassLog::write('Extension: Chat started - ' . $chat->id);
        
        // Store custom data
        $customData = array(
            'extension_version' => '1.0',
            'started_at' => time()
        );
        
        $chatVars = json_decode($chat->chat_variables, true) ?? array();
        $chatVars['myextension'] = $customData;
        $chat->chat_variables = json_encode($chatVars);
        $chat->updateThis();
    }
    
    public static function getCustomData($chat)
    {
        $chatVars = json_decode($chat->chat_variables, true) ?? array();
        return $chatVars['myextension'] ?? array();
    }
    
    public static function extendSwagger($params)
    {
        // Add custom API endpoints to Swagger
        $params['append_paths'] .= ',
            "/restapi/myextension/custom": {
                "get": {
                    "summary": "My custom endpoint",
                    "responses": {
                        "200": {"description": "Success"}
                    }
                }
            }';
    }
}
```

## Extension Settings

```php
// extension/myextension/settings/settings.ini.php
<?php
return array(
    'myextension' => array(
        'enabled' => true,
        'api_key' => '',
        'custom_option' => 'value'
    )
);
```

### Accessing Settings

```php
// In extension code
$settings = include 'extension/myextension/settings/settings.ini.php';
$apiKey = $settings['myextension']['api_key'];

// Or through config system
erConfigClassLhConfig::getInstance()->conf()->getSetting('myextension', 'api_key');
```

## Extension Autoloading

### Static Autoload

```php
// lhc_web/lib/autoloads/lhautoload.php
$classes = array(
    // ... existing classes ...
);

// Extension classes can be added here
// But dynamic loading in bootstrap is preferred
```

### Dynamic Autoload in Bootstrap

```php
// extension/myextension/bootstrap/bootstrap.php
spl_autoload_register(function ($class) {
    // PSR-4 style autoloading for providers
    $prefix = 'LiveHelperChatExtension\\myextension\\providers\\';
    $base_dir = 'extension/myextension/providers/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});
```

## Providers Namespace (PSR-4 Style)

For complex extensions, use namespaced service classes:

```php
<?php
// extension/myextension/providers/WebhookHandler.php
namespace LiveHelperChatExtension\myextension\providers;

class WebhookHandler
{
    private $config;
    private static $instance = null;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function processIncoming($payload)
    {
        // Process webhook payload
        $data = json_decode($payload, true);
        return $this->handleMessage($data);
    }
    
    private function handleMessage($data)
    {
        // Message handling logic
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            $settings = include 'extension/myextension/settings/settings.ini.php';
            self::$instance = new self($settings['myextension']);
        }
        return self::$instance;
    }
}
```

### Using Providers in Controllers

```php
<?php
// extension/myextension/modules/lhmyextension/webhook.php
use LiveHelperChatExtension\myextension\providers\WebhookHandler;

// Auto-load the provider
include_once 'extension/myextension/providers/WebhookHandler.php';

$handler = WebhookHandler::getInstance();
$result = $handler->processIncoming(file_get_contents('php://input'));

echo json_encode($result);
exit;
```

## Extension Models with Custom Session Handler

Extension models use their own session handler pointing to extension POS definitions:

```php
<?php
// extension/myextension/classes/erlhcoreclassmodelmydata.php
class erLhcoreClassModelMyextensionData
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_myextension_data';
    public static $dbTableId = 'id';
    // Custom session handler pointing to extension's getSession method
    public static $dbSessionHandler = 'erLhcoreClassExtensionMyextension::getSession';
    public static $dbSortOrder = 'DESC';

    public $id;
    public $chat_id;
    public $external_id;
    public $data;
    public $ctime;

    public function getState()
    {
        return array(
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'external_id' => $this->external_id,
            'data' => $this->data,
            'ctime' => $this->ctime
        );
    }
    
    public function __toString()
    {
        return $this->external_id;
    }
}
```

### POS Definition File

```php
<?php
// extension/myextension/pos/lhmyextension/erlhcoreclassmodelmydata.php
$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_myextension_data";
$def->class = "erLhcoreClassModelMyextensionData";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition('ezcPersistentNativeGenerator');

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['external_id'] = new ezcPersistentObjectProperty();
$def->properties['external_id']->columnName = 'external_id';
$def->properties['external_id']->propertyName = 'external_id';
$def->properties['external_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['data'] = new ezcPersistentObjectProperty();
$def->properties['data']->columnName = 'data';
$def->properties['data']->propertyName = 'data';
$def->properties['data']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['ctime'] = new ezcPersistentObjectProperty();
$def->properties['ctime']->columnName = 'ctime';
$def->properties['ctime']->propertyName = 'ctime';
$def->properties['ctime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;
```

## Database Schema Definition

Use `doc/structure.json` for declarative schema definition:

```json
// extension/myextension/doc/structure.json
{
    "lhc_myextension_data": {
        "id": "bigint(20) NOT NULL AUTO_INCREMENT",
        "chat_id": "bigint(20) NOT NULL",
        "external_id": "varchar(100) NOT NULL",
        "data": "longtext NOT NULL",
        "ctime": "int(11) NOT NULL"
    },
    "lhc_myextension_data_index": {
        "PRIMARY": ["id"],
        "chat_id": ["chat_id"],
        "external_id": ["external_id"]
    }
}
```

## Extension Settings with Environment Variables

```php
<?php
// extension/myextension/settings/settings.ini.default.php
return array(
    'myextension' => array(
        'enabled' => true,
        // Support environment variables with fallback defaults
        'api_key' => getenv('MYEXT_API_KEY') ?: '',
        'api_secret' => getenv('MYEXT_API_SECRET') ?: '',
        'webhook_url' => getenv('MYEXT_WEBHOOK_URL') ?: '',
        'debug_mode' => getenv('MYEXT_DEBUG') ?: false,
        'verify_token' => getenv('MYEXT_VERIFY_TOKEN') ?: '',
    )
);
```

## Best Practices

1. **Use unique prefixes:**
   ```php
   // Classes: erLhcoreClassExtMyExtension
   // Tables: lh_ext_myextension_*
   // Config keys: myextension_*
   ```

2. **Check extension is enabled:**
   ```php
   $extensions = erConfigClassLhConfig::getInstance()->getSetting('site', 'extensions');
   if (!in_array('myextension', $extensions)) {
       return;
   }
   ```

3. **Handle missing dependencies:**
   ```php
   if (!class_exists('SomeRequiredClass')) {
       erLhcoreClassLog::write('MyExtension: Missing dependency');
       return;
   }
   ```

4. **Use #[\AllowDynamicProperties] for bootstrap class:**
   ```php
   #[\AllowDynamicProperties]
   class erLhcoreClassExtensionMyextension
   {
       // Required for PHP 8.2+ compatibility
   }
   ```

5. **Use providers namespace for complex services:**
   ```php
   // extension/myextension/providers/MyService.php
   namespace LiveHelperChatExtension\myextension\providers;
   
   class MyService { /* ... */ }
   ```

6. **Define database schema in doc/structure.json:**
   ```json
   {
       "lhc_myextension_data": {
           "id": "bigint(20) NOT NULL AUTO_INCREMENT",
           "chat_id": "bigint(20) NOT NULL"
       },
       "lhc_myextension_data_index": {
           "PRIMARY": ["id"],
           "chat_id": ["chat_id"]
       }
   }
   ```

7. **Version your extension:**
   ```php
   class erLhcoreClassExtensionMyextension {
       const VERSION = '1.2.3';
       
       public static function checkVersion() {
           // Migration logic based on stored version
       }
   }
   ```
