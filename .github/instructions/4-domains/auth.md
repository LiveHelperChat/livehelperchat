# Authentication Domain - Auth and Permissions

## Overview

Live Helper Chat uses session-based authentication with a Role-Based Access Control (RBAC) system. The authentication layer is built on eZ Components authentication session handling.

## User Authentication

### User Instance

The user singleton manages authentication state:

```php
// lib/core/lhuser/lhuser.php
class erLhcoreClassUser {

    static function instance()
    {
        if (empty($GLOBALS['LhUserInstance'])) {
            $GLOBALS['LhUserInstance'] = new erLhcoreClassUser();
        }
        return $GLOBALS['LhUserInstance'];
    }

    function __construct()
    {
        $options = new ezcAuthenticationSessionOptions();
        $options->validity = 3600 * 24;
        $options->idKey = 'lhc_ezcAuth_id';
        $options->timestampKey = 'lhc_ezcAuth_timestamp';

        $this->session = new ezcAuthenticationSession($options);
        $this->session->start();

        $this->credentials = new ezcAuthenticationPasswordCredentials(
            $this->session->load(), 
            null
        );

        if (!$this->session->isValid($this->credentials)) {
            // Try remember-me cookie
            if (isset($_COOKIE['lhc_rm_u'])) {
                $this->validateRemember($_COOKIE['lhc_rm_u']);
            }
        } else {
            $this->userid = $_SESSION['lhc_user_id'];
            $this->authenticated = true;
        }
    }
}
```

### Login Flow

```php
// lib/core/lhuser/lhuser.php
function authenticate($username, $password, $remember = false)
{
    session_regenerate_id(true);
    $this->session->destroy();

    $user = erLhcoreClassModelUser::findOne(array(
        'filterlor' => array(
            'username' => array($username),
            'email' => array($username)
        )
    ));

    if ($user === false) {
        return false;
    }

    // Password verification
    if (!password_verify($password, $user->password)) {
        // Check legacy MD5 hash
        if (md5($password) !== $user->password) {
            return false;
        }
        // Upgrade to modern hash
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->updateThis();
    }

    // Set session
    $_SESSION['lhc_user_id'] = $user->id;
    $this->userid = $user->id;
    $this->authenticated = true;

    if ($remember) {
        $this->setRememberMe($user);
    }

    return true;
}
```

### Check Authentication Status

```php
$currentUser = erLhcoreClassUser::instance();

if ($currentUser->isLogged()) {
    $userData = $currentUser->getUserData();
    $userId = $currentUser->getUserID();
} else {
    // Redirect to login
    erLhcoreClassModule::redirect('user/login');
}
```

## Permission System

### RBAC Structure

- **Users** belong to **Groups**
- **Groups** have **Roles**
- **Roles** have **Functions** (permissions)

### Permission Check

```php
// Basic permission check
$currentUser = erLhcoreClassUser::instance();
$hasAccess = $currentUser->hasAccessTo('lhchat', 'use');

// Check with limitation return
$limitation = $currentUser->hasAccessTo('lhchat', array('use'), true);
if ($limitation === false) {
    // No permission
} elseif ($limitation === true) {
    // Full access
} else {
    // Has access with limitations (department restrictions, etc.)
}
```

### Module Permission Definition

```php
// modules/lhchat/module.php
$FunctionList = array();
$FunctionList['use'] = array('explain' => 'General chat module usage');
$FunctionList['allowopenremote'] = array('explain' => 'Open chats from other operators');
$FunctionList['allowcloseremote'] = array('explain' => 'Close chats from other operators');
$FunctionList['allowtransfer'] = array('explain' => 'Transfer chats');
$FunctionList['allowblockusers'] = array('explain' => 'Block visitors');
$FunctionList['setsubject'] = array('explain' => 'Set chat subjects');
```

### Department-Based Permissions

```php
// Get user's departments
$userDepartments = erLhcoreClassUserDep::getUserReadDepartments($userId);

// Check if user has access to specific department
$hasDepAccess = erLhcoreClassUserDep::userHasDepartmentAccess($userId, $depId);

// Get department limitation for queries
$limitation = erLhcoreClassChat::getDepartmentLimitation('lh_chat');
if ($limitation !== false && $limitation !== true) {
    $filter['customfilter'][] = $limitation;
}
```

## API Authentication

### REST API Key

```php
// lib/core/lhrestapi/lhrestapivalidator.php
public static function validateRequest()
{
    self::setHeaders();
    
    // Check for API key in header
    $headers = self::getHeaders();
    
    if (isset($headers['Authorization'])) {
        // Basic auth
        $auth = base64_decode(substr($headers['Authorization'], 6));
        list($username, $apiKey) = explode(':', $auth);
        
        $user = erLhcoreClassModelUser::findOne(array(
            'filter' => array('username' => $username)
        ));
        
        $key = erLhAbstractModelRestAPIKey::findOne(array(
            'filter' => array(
                'api_key' => $apiKey,
                'user_id' => $user->id,
                'active' => 1
            )
        ));
        
        if ($key) {
            return $user;
        }
    }
    
    return false;
}
```

### API Usage

```php
// In REST API controller
$userData = erLhcoreClassRestAPIHandler::validateRequest();
if ($userData === false) {
    echo json_encode(array('error' => true, 'message' => 'Invalid API key'));
    exit;
}

// User is authenticated, proceed with API logic
$chats = erLhcoreClassModelChat::getList(array(
    'filter' => array('user_id' => $userData->id)
));
```

## Session Management

### Session Data

```php
// Set session data
$_SESSION['lhc_user_id'] = $userId;

// Session-based caching
CSCacheAPC::getMem()->setSession('chat_hash_widget_resume', $hash, true, true);

// Get session data
$hash = CSCacheAPC::getMem()->getSession('chat_hash_widget_resume', true);
```

### One Login Per Account

```php
// settings/settings.ini.php
'one_login_per_account' => true,

// Enforced in erLhcoreClassUser::__construct()
if (self::$oneLoginPerAccount == true) {
    $sesid = $this->getUserData(true)->session_id;
    if ($sesid != $_COOKIE['PHPSESSID'] && $sesid != '') {
        $this->authenticated = false;
        $this->logout();
        $_SESSION['logout_reason'] = 1;
    }
}
```

## User Model

```php
// lib/models/lhuser/erlhcoreclassmodeluser.php
class erLhcoreClassModelUser {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_users';
    
    public $id;
    public $username;
    public $password;
    public $email;
    public $name;
    public $surname;
    public $disabled;
    public $all_departments;
    public $hide_online;
    // ...
    
    // Check if user has access to all departments
    public function hasAllDepartmentsAccess()
    {
        return $this->all_departments == 1;
    }
}
```

## Best Practices

1. **Always check authentication in admin controllers:**
   ```php
   $currentUser = erLhcoreClassUser::instance();
   if (!$currentUser->isLogged()) {
       erLhcoreClassModule::redirect('user/login');
       exit;
   }
   ```

2. **Use permission checks before sensitive operations:**
   ```php
   if (!$currentUser->hasAccessTo('lhchat', 'allowcloseremote')) {
       // Only allow closing own chats
       if ($chat->user_id != $currentUser->getUserID()) {
           throw new Exception('No permission');
       }
   }
   ```

3. **Hash passwords properly:**
   ```php
   $user->password = password_hash($plainPassword, PASSWORD_DEFAULT);
   ```

4. **Validate API keys securely:**
   ```php
   // Use constant-time comparison
   if (!hash_equals($storedKey, $providedKey)) {
       return false;
   }
   ```

5. **Log authentication events:**
   ```php
   $loginRecord = new erLhcoreClassModelUserLogin();
   $loginRecord->user_id = $user->id;
   $loginRecord->type = erLhcoreClassModelUserLogin::TYPE_LOGIN;
   $loginRecord->status = erLhcoreClassModelUserLogin::STATUS_SUCCESS;
   $loginRecord->ip = $_SERVER['REMOTE_ADDR'];
   $loginRecord->saveThis();
   ```
