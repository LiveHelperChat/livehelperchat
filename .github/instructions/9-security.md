# Security Model

## Overview

Live Helper Chat implements a comprehensive security model covering authentication, authorization, input validation, and data protection.

## Authentication

### Password Handling

```php
// lib/core/lhuser/lhuser.php
class erLhcoreClassUser {
    
    public function authenticate($username, $password)
    {
        // Find user by username or email
        $user = erLhcoreClassModelUser::findOne(array(
            'filterlorf' => array(
                $username => array('username', 'email')
            ),
            'filter' => array('disabled' => 0)
        ));
        
        if (!$user) {
            $this->logFailedAttempt($username);
            return false;
        }
        
        // Modern password verification
        if (password_verify($password, $user->password)) {
            return $user;
        }
        
        // Legacy MD5 fallback (for migration)
        if (strlen($user->password) == 32 && md5($password) === $user->password) {
            // Upgrade to bcrypt
            $user->password = password_hash($password, PASSWORD_BCRYPT);
            $user->saveThis();
            return $user;
        }
        
        $this->logFailedAttempt($username, $user->id);
        return false;
    }
}
```

### Password Requirements

```php
// Password update validation
public function validatePassword($password)
{
    $config = erConfigClassLhConfig::getInstance();
    
    $minLength = $config->getSetting('password', 'min_length', 8);
    $requireUppercase = $config->getSetting('password', 'require_uppercase', true);
    $requireNumber = $config->getSetting('password', 'require_number', true);
    $requireSpecial = $config->getSetting('password', 'require_special', false);
    
    if (strlen($password) < $minLength) {
        return 'Password must be at least ' . $minLength . ' characters';
    }
    
    if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
        return 'Password must contain uppercase letter';
    }
    
    if ($requireNumber && !preg_match('/[0-9]/', $password)) {
        return 'Password must contain number';
    }
    
    return true;
}
```

### Session Security

```php
// Session regeneration on login
session_regenerate_id(true);

// Session fixation protection
$_SESSION['lhc_user_id'] = $user->id;
$_SESSION['lhc_session_token'] = bin2hex(random_bytes(32));

// Session timeout check
$sessionTimeout = erConfigClassLhConfig::getInstance()->getSetting('site', 'session_timeout', 3600);
if (time() - $_SESSION['lhc_last_activity'] > $sessionTimeout) {
    session_destroy();
    // Redirect to login
}
$_SESSION['lhc_last_activity'] = time();
```

### API Authentication

```php
// lib/core/lhrestapi/lhrestapivalidator.php
public static function validateRequest()
{
    $headers = self::getHeaders();
    
    // Basic Auth: base64(username:api_key)
    if (isset($headers['Authorization'])) {
        $auth = $headers['Authorization'];
        
        if (strpos($auth, 'Basic ') === 0) {
            $credentials = base64_decode(substr($auth, 6));
            list($username, $apiKey) = explode(':', $credentials, 2);
            
            $user = self::validateApiKey($username, $apiKey);
            if ($user) {
                return $user;
            }
        }
    }
    
    return false;
}

public static function validateApiKey($username, $apiKey)
{
    $user = erLhcoreClassModelUser::findOne(array(
        'filter' => array('username' => $username, 'disabled' => 0)
    ));
    
    if (!$user) return false;
    
    $key = erLhAbstractModelRestAPIKey::findOne(array(
        'filter' => array(
            'api_key' => $apiKey,
            'user_id' => $user->id,
            'active' => 1
        )
    ));
    
    if (!$key) return false;
    
    // IP restriction check
    if (!empty($key->ip_restrictions)) {
        $allowedIps = array_map('trim', explode(',', $key->ip_restrictions));
        if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIps)) {
            return false;
        }
    }
    
    return $user;
}
```

## Authorization (RBAC)

### Role Structure

```
┌─────────────────────────────────────────┐
│              USER                       │
│  └── belongs to GROUPS                  │
│       └── have ROLES                    │
│            └── contain FUNCTIONS        │
│                 └── with LIMITATIONS    │
└─────────────────────────────────────────┘
```

### Permission Check Implementation

```php
// lib/core/lhuser/lhuser.php
public function hasAccessTo($module, $functions, $returnLimitation = false)
{
    if (!is_array($functions)) {
        $functions = array($functions);
    }
    
    // Get user's groups
    $groups = $this->getUserGroups();
    
    // Get roles from groups
    $roles = array();
    foreach ($groups as $group) {
        $groupRoles = erLhcoreClassGroupRole::getGroupRoles($group->id);
        $roles = array_merge($roles, $groupRoles);
    }
    
    // Check each function
    foreach ($functions as $function) {
        foreach ($roles as $role) {
            $roleFunction = erLhcoreClassRoleFunction::findOne(array(
                'filter' => array(
                    'role_id' => $role->id,
                    'module' => $module,
                    'function' => $function
                )
            ));
            
            if ($roleFunction) {
                if ($returnLimitation) {
                    return $this->parseLimitation($roleFunction->limitation);
                }
                return true;
            }
        }
    }
    
    return false;
}
```

### Module Function Definitions

```php
// modules/lhchat/module.php
$FunctionList = array();
$FunctionList['use'] = array('explain' => 'General chat access');
$FunctionList['allowcloseremote'] = array('explain' => 'Allow close any chat');
$FunctionList['allowtransfer'] = array('explain' => 'Allow transfer chats');
$FunctionList['takeown'] = array('explain' => 'Allow take ownership');
$FunctionList['deletechat'] = array('explain' => 'Allow delete chats');
$FunctionList['allowblockuser'] = array('explain' => 'Allow block users');
// ...

// Usage in controller
if (!$currentUser->hasAccessTo('lhchat', 'deletechat')) {
    throw new Exception('No permission to delete chat');
}
```

### Department-Based Access

```php
// lib/core/lhuser/lhuserdep.php
class erLhcoreClassUserDep {
    
    public static function getUserReadDepartments($userId)
    {
        // Get departments where user is member
        $userDeps = erLhcoreClassModelUserDep::getList(array(
            'filter' => array('user_id' => $userId)
        ));
        
        $depIds = array();
        foreach ($userDeps as $ud) {
            $depIds[] = $ud->dep_id;
        }
        
        // Check group-based department access
        $groupDeps = self::getDepartmentsFromGroups($userId);
        $depIds = array_merge($depIds, $groupDeps);
        
        return array_unique($depIds);
    }
}

// Apply limitation to queries
$limitation = erLhcoreClassChat::getDepartmentLimitation(
    'lh_chat',
    erLhcoreClassUserDep::getUserReadDepartments($userId)
);

if ($limitation !== false && $limitation !== true) {
    $filter['customfilter'][] = $limitation;
}
```

## Input Validation

### SQL Injection Prevention

```php
// Use prepared statements (via eZ Components)
$db = ezcDbInstance::get();
$stmt = $db->prepare('SELECT * FROM lh_chat WHERE id = :id AND hash = :hash');
$stmt->bindValue(':id', $chatId, PDO::PARAM_INT);
$stmt->bindValue(':hash', $hash, PDO::PARAM_STR);
$stmt->execute();

// Model queries use parameter binding automatically
$chats = erLhcoreClassModelChat::getList(array(
    'filter' => array('status' => $status),  // Safe - bound
    'filterlike' => array('nick' => $search)  // Safe - bound with %
));
```

### XSS Prevention

```php
// Template output escaping
<?php echo htmlspecialchars($chat->nick, ENT_QUOTES, 'UTF-8'); ?>

// Or use helper
<?php echo erLhcoreClassDesign::shrt($chat->nick, 100, '...', true); ?>

// Rich text sanitization
$cleanHtml = erLhcoreClassXMP::sanitizeHTML($userInput);
```

### CSRF Protection

```php
// Generate token
$csrfToken = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;

// In form
<input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

// Validate on submit
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    throw new Exception('CSRF token validation failed');
}
```

### File Upload Validation

```php
// lib/core/lhchat/lhchatfile.php
public static function validateUpload($file)
{
    // Check file size
    $maxSize = erConfigClassLhConfig::getInstance()->getSetting('chat', 'max_file_size', 5242880);
    if ($file['size'] > $maxSize) {
        throw new Exception('File too large');
    }
    
    // Check extension whitelist
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx');
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedExtensions)) {
        throw new Exception('File type not allowed');
    }
    
    // Check MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    $allowedMimes = array(
        'image/jpeg', 'image/png', 'image/gif',
        'application/pdf', 'application/msword'
    );
    
    if (!in_array($mimeType, $allowedMimes)) {
        throw new Exception('Invalid file type');
    }
    
    return true;
}
```

## Data Protection

### Chat Hash Security

```php
// Each chat has a unique hash for visitor access
$chat->hash = sha1(microtime() . mt_rand() . uniqid('', true));

// Visitor must provide correct hash to access chat
$chat = erLhcoreClassModelChat::findOne(array(
    'filter' => array(
        'id' => $chatId,
        'hash' => $hash
    )
));

if (!$chat) {
    throw new Exception('Invalid chat access');
}
```

### Sensitive Data Encryption

```php
// Configuration for encryption
$secretKey = erConfigClassLhConfig::getInstance()->getSetting('site', 'secrethash');

// Encrypt sensitive data
function encryptData($data, $key)
{
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Decrypt
function decryptData($encrypted, $key)
{
    $data = base64_decode($encrypted);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}
```

### Data Anonymization

```php
// lib/models/lhchat/erlhcoreclassmodelchat.php
public function anonymize()
{
    $this->nick = 'Anonymous';
    $this->email = '';
    $this->phone = '';
    $this->ip = '0.0.0.0';
    $this->chat_variables = '{}';
    $this->additional_data = '';
    $this->anonymized = 1;
    $this->updateThis();
    
    // Anonymize messages
    $messages = erLhcoreClassModelmsg::getList(array(
        'filter' => array('chat_id' => $this->id)
    ));
    
    foreach ($messages as $msg) {
        $msg->msg = '[Content removed]';
        $msg->updateThis();
    }
}
```

### IP Blocking

```php
// lib/models/lhchat/erlhcoreclassmodelchatblockeduser.php
class erLhcoreClassModelChatBlockedUser {
    
    public static function isBlocked($ip, $onlineUserId = null)
    {
        $filter = array('ip' => $ip);
        
        // Check IP block
        $block = self::findOne(array('filter' => $filter));
        
        if ($block && ($block->expires == 0 || $block->expires > time())) {
            return true;
        }
        
        // Check online user block
        if ($onlineUserId) {
            $block = self::findOne(array(
                'filter' => array('online_user_id' => $onlineUserId)
            ));
            
            if ($block && ($block->expires == 0 || $block->expires > time())) {
                return true;
            }
        }
        
        return false;
    }
}
```

## Security Headers

```php
// Set in .htaccess or application bootstrap
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// CSP header example
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
```

## Audit Logging

```php
// lib/models/lhaudit/erlhcoreclassmodelaudit.php
class erLhcoreClassModelAudit {
    
    public static function log($category, $message, $objectId = 0, $severity = 'info')
    {
        $audit = new self();
        $audit->category = $category;
        $audit->message = $message;
        $audit->object_id = $objectId;
        $audit->severity = $severity;
        $audit->user_id = erLhcoreClassUser::instance()->getUserID();
        $audit->source = $_SERVER['REMOTE_ADDR'];
        $audit->time = time();
        $audit->saveThis();
    }
}

// Usage
erLhcoreClassModelAudit::log(
    'chat',
    'Chat deleted by operator',
    $chatId,
    'warning'
);
```
