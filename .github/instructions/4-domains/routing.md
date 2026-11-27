# Routing Domain - URL and Module Resolution

## Overview

Live Helper Chat uses a custom MVC routing system built on eZ Components. URLs are mapped to module/view pairs through a front controller pattern.

## URL Structure

```
/{siteaccess}/{module}/{view}/{param1}/{param2}/(uparam1)/{value1}/(uparam2)/{value2}
```

**Examples:**
- `/site_admin/chat/single/123` → Chat module, single view, chat_id=123
- `/site_admin/user/account/(tab)/notifications` → User module, account view, tab=notifications
- `/eng/chat/startchat` → Chat module, startchat view (visitor-facing)

## Front Controller

The `index.php` file serves as the front controller:

```php
// lhc_web/index.php
erLhcoreClassSystem::init();

ezcBaseInit::setCallback(
    'ezcInitDatabaseInstance',
    'erLhcoreClassLazyDatabaseConfiguration'
);

set_exception_handler(array('erLhcoreClassModule', 'defaultExceptionHandler'));
set_error_handler(array('erLhcoreClassModule', 'defaultWarningHandler'));

$Result = erLhcoreClassModule::moduleInit();

$tpl = erLhcoreClassTemplate::getInstance('pagelayouts/main.php');
$tpl->set('Result', $Result);
echo $tpl->fetch();
```

## Module Definition

Each module has a `module.php` file that defines available views:

```php
// modules/lhchat/module.php
$Module = array("name" => "Chat");

$ViewList = array();

$ViewList['adminchat'] = array(
    'params' => array('chat_id'),              // Required URL parameters
    'uparams' => array('remember', 'arg'),     // Optional unordered parameters
    'functions' => array('use'),               // Required permissions
    'multiple_arguments' => array('arg')       // Parameters accepting multiple values
);

$ViewList['single'] = array(
    'params' => array('chat_id'),
    'functions' => array('use')
);

$ViewList['startchat'] = array(
    'params' => array(),
    'uparams' => array('hash', 'hash_resume', 'vid'),
    // No 'functions' = public access
);
```

## Module Runner

The `erLhcoreClassModule::runModule()` handles parameter extraction and permission checking:

```php
// lib/core/lhcore/lhmodule.php
static function runModule()
{
    $Params = array();
    $Params['module'] = self::$currentModule[self::$currentView];
    $Params['module']['name'] = self::$currentModule;
    $Params['module']['view'] = self::$currentView;

    // Extract ordered parameters
    foreach (self::$currentModule[self::$currentView]['params'] as $userParameter) {
        $urlCfgDefault->addOrderedParameter($userParameter);
    }

    // Extract unordered parameters
    foreach (self::$currentModule[self::$currentView]['uparams'] as $userParameter) {
        $urlCfgDefault->addUnorderedParameter($userParameter);
    }

    // Check permissions if functions defined
    if (isset($Params['module']['functions'])) {
        $currentUser = erLhcoreClassUser::instance();
        $limitation = $currentUser->hasAccessTo(
            'lh' . self::$currentModuleName,
            $Params['module']['functions'],
            true
        );

        if ($limitation === false) {
            // Redirect to login or show no permission page
        }
    }

    // Include and execute the view file
    include('modules/lh' . self::$currentModuleName . '/' . self::$currentView . '.php');
}
```

## Parameter Access in Controllers

Controllers access parameters through the `$Params` array:

```php
// modules/lhchat/single.php
$chat_id = $Params['user_parameters']['chat_id'];
$tab = $Params['user_parameters_unordered']['tab'] ?? 'messages';

$chat = erLhcoreClassModelChat::fetch($chat_id);
if (!$chat) {
    erLhcoreClassModule::redirect('chat/list');
    exit;
}
```

## Siteaccess Configuration

Siteaccesses are defined in settings:

```php
// settings/settings.ini.default.php
'default_site_access' => 'eng',
'default_admin_site_access' => ['site_admin'],
'available_site_access' => array(
    'eng', 'lit', 'esp', 'por', 'ger', 'fre', 
    'site_admin'  // Admin interface
),
```

## URL Generation

Use design helpers to generate URLs:

```php
// Base URL for a module/view
$url = erLhcoreClassDesign::baseurl('chat/single') . '/' . $chat_id;

// Full URL with host
$fullUrl = erLhcoreClassDesign::baseurldirect('chat/single/' . $chat_id);

// URL with unordered parameters
$url = erLhcoreClassDesign::baseurl('user/account') . '/(tab)/notifications';
```

## Redirects

```php
// Simple redirect
erLhcoreClassModule::redirect('chat/list');

// Redirect with parameters
erLhcoreClassModule::redirect('chat/single/' . $chat_id);

// Redirect with message
erLhcoreClassModule::redirect('chat/list', '/(msg)/saved');
```

## Extension Overrides

Extensions can override module controllers:

```php
// extension/customext/modules/lhchat/module.php
$Module = array("name" => "Chat module");
$ViewList = array();
$ViewList['single'] = array(
    'params' => array('chat_id'),
    'functions' => array('use')
);

// extension/customext/modules/lhchat/single.php
// Custom implementation
```

## Permission Functions

Common permission function patterns:

```php
// modules/lhchat/module.php
$FunctionList = array();
$FunctionList['use'] = array('explain' => 'General permission to use chat');
$FunctionList['allowopenremote'] = array('explain' => 'Allow to open remote chats');
$FunctionList['allowcloseremote'] = array('explain' => 'Allow to close remote chats');
$FunctionList['allowtransfer'] = array('explain' => 'Allow to transfer chats');
```

## Best Practices

1. **Always define permissions for admin views:**
   ```php
   $ViewList['adminview'] = array(
       'params' => array(),
       'functions' => array('use')  // Require authentication
   );
   ```

2. **Use unordered params for optional filters:**
   ```php
   $ViewList['list'] = array(
       'params' => array(),
       'uparams' => array('status', 'department', 'user'),
   );
   ```

3. **Handle missing parameters gracefully:**
   ```php
   $chat_id = $Params['user_parameters']['chat_id'] ?? null;
   if (!$chat_id || !($chat = erLhcoreClassModelChat::fetch($chat_id))) {
       // Handle error
   }
   ```

4. **Return proper $Result structure:**
   ```php
   $Result['content'] = $tpl->fetch();
   $Result['pagelayout'] = 'popup';  // Or 'login', default, etc.
   $Result['path'] = array(
       array('title' => 'Chats', 'url' => erLhcoreClassDesign::baseurl('chat/list')),
       array('title' => $chat->nick)
   );
   return $Result;
   ```
