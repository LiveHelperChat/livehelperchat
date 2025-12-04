# User Management Domain

## Overview

The user management domain handles operators, administrators, authentication, sessions, and user preferences.

## User Model

```php
// lib/models/lhuser/erlhcoreclassmodeluser.php
class erLhcoreClassModelUser {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_users';
    public static $dbTableId = 'id';
    
    public $id;
    public $username;
    public $password;           // bcrypt hash
    public $email;
    public $name;
    public $surname;
    public $filepath;           // Avatar path
    public $filename;
    public $chat_nickname;      // Display name in chats
    public $job_title;
    public $skype;
    public $disabled;
    public $hide_online;        // Hide from online list
    public $all_departments;    // Access to all departments
    public $invisible_mode;     // Don't appear online
    public $inactive_mode;      // Don't receive chats
    public $auto_accept;        // Auto-accept incoming chats
    public $max_active_chats;   // Chat limit
    public $time_zone;
    public $session_id;         // Current session
    public $llogin;             // Last login timestamp
    public $pswd_updated;       // Password last changed
    public $force_logout;       // Force re-login
    public $cache_version;
    
    // Custom attributes
    public $attr_int_1;
    public $attr_int_2;
    public $attr_int_3;
}
```

## User Service Class

```php
// lib/core/lhuser/lhuser.php
class erLhcoreClassUser {
    
    private $userid = false;
    private $userData = false;
    private $userGroups = array();
    
    /**
     * Singleton instance
     */
    static function instance()
    {
        if (empty($GLOBALS['LhUserInstance'])) {
            $GLOBALS['LhUserInstance'] = new erLhcoreClassUser();
        }
        return $GLOBALS['LhUserInstance'];
    }
    
    /**
     * Check if logged in
     */
    public function isLogged()
    {
        if ($this->userid !== false) {
            return true;
        }
        
        // Check session
        if (isset($_SESSION['lhc_user_id'])) {
            $this->userid = $_SESSION['lhc_user_id'];
            return true;
        }
        
        // Check remember me cookie
        if ($this->checkRememberToken()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get current user ID
     */
    public function getUserID()
    {
        return $this->userid;
    }
    
    /**
     * Get user data object
     */
    public function getUserData($forceReload = false)
    {
        if ($this->userData === false || $forceReload) {
            $this->userData = erLhcoreClassModelUser::fetch($this->userid);
        }
        return $this->userData;
    }
}
```

## Authentication

### Login Process

```php
public function authenticate($username, $password)
{
    // Find user
    $user = erLhcoreClassModelUser::findOne(array(
        'filterlorf' => array(
            $username => array('username', 'email')
        ),
        'filter' => array('disabled' => 0)
    ));
    
    if (!$user) {
        $this->logLogin($username, 0, 'User not found');
        return false;
    }
    
    // Verify password
    if (!password_verify($password, $user->password)) {
        // Try legacy MD5
        if (strlen($user->password) == 32 && md5($password) === $user->password) {
            // Upgrade hash
            $user->password = password_hash($password, PASSWORD_BCRYPT);
            $user->updateThis();
        } else {
            $this->logLogin($username, $user->id, 'Invalid password');
            return false;
        }
    }
    
    // Create session
    session_regenerate_id(true);
    $_SESSION['lhc_user_id'] = $user->id;
    
    // Update user
    $user->session_id = session_id();
    $user->llogin = time();
    $user->updateThis();
    
    $this->logLogin($username, $user->id, 'Success', true);
    
    return $user;
}
```

### Logout

```php
public function logout()
{
    // Update online status
    $this->setOffline();
    
    // Clear remember token
    if (isset($_COOKIE['lhc_remember'])) {
        $token = $_COOKIE['lhc_remember'];
        erLhcoreClassModelUserRemember::findOne(array(
            'filter' => array('user_id' => $this->userid)
        ))?->removeThis();
        
        setcookie('lhc_remember', '', time() - 3600, '/');
    }
    
    // Destroy session
    $_SESSION = array();
    session_destroy();
    
    $this->userid = false;
    $this->userData = false;
}
```

## Permission System

### Check Access

```php
public function hasAccessTo($module, $functions, $returnLimitation = false)
{
    if (!is_array($functions)) {
        $functions = array($functions);
    }
    
    // Get all role functions for user
    $roleFunctions = $this->getRoleFunctions();
    
    foreach ($functions as $function) {
        foreach ($roleFunctions as $rf) {
            if ($rf->module == $module && $rf->function == $function) {
                if ($returnLimitation) {
                    return $this->parseLimitation($rf->limitation);
                }
                return true;
            }
        }
    }
    
    return false;
}

private function getRoleFunctions()
{
    $cache = CSCacheAPC::getMem();
    $cacheKey = 'user_role_functions_' . $this->userid;
    
    $functions = $cache->restore($cacheKey);
    if ($functions === false) {
        // Get user groups
        $groups = erLhcoreClassGroupUser::getGroupsByUserId($this->userid);
        
        // Get roles from groups
        $roleIds = array();
        foreach ($groups as $group) {
            $groupRoles = erLhcoreClassGroupRole::getList(array(
                'filter' => array('group_id' => $group->group_id)
            ));
            foreach ($groupRoles as $gr) {
                $roleIds[] = $gr->role_id;
            }
        }
        
        // Get role functions
        if (!empty($roleIds)) {
            $functions = erLhcoreClassRoleFunction::getList(array(
                'filterin' => array('role_id' => array_unique($roleIds))
            ));
        } else {
            $functions = array();
        }
        
        $cache->store($cacheKey, $functions, 300);
    }
    
    return $functions;
}
```

### Permission Limitations

```php
// Limitations allow granular control
// Examples:
// - Only own department chats
// - Only chats assigned to self
// - Read-only access

private function parseLimitation($limitation)
{
    if (empty($limitation)) {
        return true;  // Full access
    }
    
    $limits = json_decode($limitation, true);
    
    return array(
        'department' => $limits['department'] ?? null,
        'own_only' => $limits['own_only'] ?? false,
        'read_only' => $limits['read_only'] ?? false
    );
}
```

## Department Assignment

### User Department Model

```php
// lib/models/lhuser/erlhcoreclassmodeluser.php
class erLhcoreClassModelUserDep {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_userdep';
    
    public $id;
    public $user_id;
    public $dep_id;
    public $last_activity;      // Last operator activity
    public $hide_online;        // Hidden in this department
    public $exclude_autoasign;  // Exclude from auto-assign
    public $active_chats;       // Current active chat count
    public $pending_chats;      // Pending chats
    public $max_chats;          // Per-department chat limit
    public $type;               // 0=individual, 1=group-based
    public $ro;                 // Read-only
}
```

### Get User Departments

```php
class erLhcoreClassUserDep {
    
    public static function getUserReadDepartments($userId)
    {
        $user = erLhcoreClassModelUser::fetch($userId);
        
        // All departments access
        if ($user->all_departments == 1) {
            return true;  // No limitation
        }
        
        // Get individual assignments
        $userDeps = erLhcoreClassModelUserDep::getList(array(
            'filter' => array('user_id' => $userId)
        ));
        
        $depIds = array();
        foreach ($userDeps as $ud) {
            $depIds[] = $ud->dep_id;
        }
        
        // Get group-based departments
        $groupDeps = self::getDepartmentsFromGroups($userId);
        
        return array_unique(array_merge($depIds, $groupDeps));
    }
    
    public static function getUserWriteDepartments($userId)
    {
        // Similar to read, but excludes read-only assignments
        $userDeps = erLhcoreClassModelUserDep::getList(array(
            'filter' => array(
                'user_id' => $userId,
                'ro' => 0
            )
        ));
        
        // ...
    }
}
```

## Online Status Management

```php
// Set operator online
public function setOnline($depId = null)
{
    $userDeps = erLhcoreClassModelUserDep::getList(array(
        'filter' => array('user_id' => $this->userid)
    ));
    
    foreach ($userDeps as $ud) {
        if ($depId === null || $ud->dep_id == $depId) {
            $ud->hide_online = 0;
            $ud->last_activity = time();
            $ud->updateThis();
        }
    }
    
    // Dispatch event
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch(
        'user.set_online',
        array('user_id' => $this->userid, 'dep_id' => $depId)
    );
}

// Set operator offline
public function setOffline()
{
    $userDeps = erLhcoreClassModelUserDep::getList(array(
        'filter' => array('user_id' => $this->userid)
    ));
    
    foreach ($userDeps as $ud) {
        $ud->hide_online = 1;
        $ud->hide_online_ts = time();
        $ud->updateThis();
    }
}

// Check if operator is online
public static function isOnline($userId, $depId = null)
{
    $config = erConfigClassLhConfig::getInstance();
    $timeout = $config->getSetting('chat', 'online_timeout', 300);
    
    $filter = array(
        'user_id' => $userId,
        'hide_online' => 0
    );
    
    if ($depId) {
        $filter['dep_id'] = $depId;
    }
    
    $userDep = erLhcoreClassModelUserDep::findOne(array(
        'filter' => $filter,
        'filtergt' => array('last_activity' => time() - $timeout)
    ));
    
    return $userDep !== false;
}
```

## User Settings

```php
// lib/models/lhuser/erlhcoreclassmodelusersetting.php
class erLhcoreClassModelUserSetting {
    
    public static function getSetting($userId, $identifier, $default = null)
    {
        $cache = CSCacheAPC::getMem();
        $cacheKey = 'user_setting_' . $userId . '_' . $identifier;
        
        $value = $cache->restore($cacheKey);
        if ($value === false) {
            $setting = self::findOne(array(
                'filter' => array(
                    'user_id' => $userId,
                    'identifier' => $identifier
                )
            ));
            
            $value = $setting ? $setting->value : $default;
            $cache->store($cacheKey, $value);
        }
        
        return $value;
    }
    
    public static function setSetting($userId, $identifier, $value)
    {
        $setting = self::findOne(array(
            'filter' => array(
                'user_id' => $userId,
                'identifier' => $identifier
            )
        ));
        
        if ($setting) {
            $setting->value = $value;
            $setting->updateThis();
        } else {
            $setting = new self();
            $setting->user_id = $userId;
            $setting->identifier = $identifier;
            $setting->value = $value;
            $setting->saveThis();
        }
        
        // Clear cache
        CSCacheAPC::getMem()->delete('user_setting_' . $userId . '_' . $identifier);
    }
}
```

## Common Settings

| Identifier | Description |
|------------|-------------|
| `chat_columns` | Dashboard chat column visibility |
| `notifications_enabled` | Browser notifications |
| `sound_enabled` | Sound alerts |
| `auto_accept_chats` | Auto-accept setting |
| `speech_language` | Default speech language |
| `filter_settings` | Saved filter preferences |

## User Groups

```php
// lib/models/lhpermission/erlhcoreclassmodelgroup.php
class erLhcoreClassModelGroup {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_group';
    
    public $id;
    public $name;
    public $disabled;
    public $required;  // Required group (cannot be removed)
}

// lib/models/lhpermission/erlhcoreclassmodelgroupuser.php
class erLhcoreClassModelGroupUser {
    
    public $id;
    public $group_id;
    public $user_id;
}
```

## Login Auditing

```php
// lib/models/lhuser/erlhcoreclassmodeluserlogin.php
class erLhcoreClassModelUserLogin {
    
    public $id;
    public $user_id;
    public $type;      // 0=login, 1=logout, 2=failed
    public $ctime;
    public $status;
    public $ip;
    public $msg;
}
```
