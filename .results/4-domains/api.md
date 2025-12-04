# API Domain - REST API Layer

## Overview

Live Helper Chat provides REST APIs through two main modules: `lhrestapi` (admin/operator APIs) and `lhwidgetrestapi` (visitor widget APIs).

## API Handler

### Setting Headers

```php
// lib/core/lhrestapi/lhrestapivalidator.php
class erLhcoreClassRestAPIHandler
{
    public static function setHeaders($content = 'Content-Type: application/json', $origin = "*")
    {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, API-Key, Authorization');
        header($content);
        self::setOptionHeaders();
    }

    public static function setOptionHeaders()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
            header("Access-Control-Max-Age: 1728000");
            exit(0);
        }
    }

    public static function outputResponse($data)
    {
        return json_encode($data);
    }
}
```

### Authentication

```php
public static function validateRequest()
{
    self::setHeaders();
    
    $headers = self::getHeaders();
    
    // Basic authentication
    if (isset($headers['Authorization']) && strpos($headers['Authorization'], 'Basic') === 0) {
        $auth = base64_decode(substr($headers['Authorization'], 6));
        list($username, $apiKey) = explode(':', $auth, 2);
        
        $user = erLhcoreClassModelUser::findOne(array(
            'filter' => array('username' => $username, 'disabled' => 0)
        ));
        
        if ($user) {
            $key = erLhAbstractModelRestAPIKey::findOne(array(
                'filter' => array(
                    'api_key' => $apiKey,
                    'user_id' => $user->id,
                    'active' => 1
                )
            ));
            
            if ($key) {
                // Check IP restrictions
                if (!empty($key->ip_restrictions)) {
                    $allowedIps = explode(',', $key->ip_restrictions);
                    if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIps)) {
                        return false;
                    }
                }
                return $user;
            }
        }
    }
    
    return false;
}
```

## Admin REST API Endpoints

### Module Definition

```php
// modules/lhrestapi/module.php
$ViewList['chats'] = array('params' => array());
$ViewList['chat'] = array('params' => array('id'));
$ViewList['fetchchat'] = array('params' => array());
$ViewList['fetchchatmessages'] = array('params' => array());
$ViewList['addmsgadmin'] = array('params' => array());
$ViewList['closechat'] = array('params' => array('chat_id'));
$ViewList['transferchat'] = array('params' => array('chat_id'));
```

### API Controller Pattern

```php
// modules/lhrestapi/chats.php
erLhcoreClassRestAPIHandler::setHeaders();

$userData = erLhcoreClassRestAPIHandler::validateRequest();

if ($userData === false) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'message' => 'Invalid API key'
    ));
    exit;
}

// Get request parameters
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$status = isset($_GET['status']) ? (int)$_GET['status'] : null;

$filter = array('limit' => $limit, 'offset' => $offset);

if ($status !== null) {
    $filter['filter']['status'] = $status;
}

// Apply department limitations
$limitation = erLhcoreClassChat::getDepartmentLimitation('lh_chat', 
    erLhcoreClassUserDep::getUserReadDepartments($userData->id)
);
if ($limitation !== false && $limitation !== true) {
    $filter['customfilter'][] = $limitation;
}

$chats = erLhcoreClassModelChat::getList($filter);

$output = array();
foreach ($chats as $chat) {
    $output[] = array(
        'id' => $chat->id,
        'nick' => $chat->nick,
        'status' => $chat->status,
        'time' => $chat->time,
        // ...
    );
}

echo erLhcoreClassRestAPIHandler::outputResponse(array(
    'error' => false,
    'chats' => $output
));
exit;
```

### Send Message API

```php
// modules/lhrestapi/addmsgadmin.php
erLhcoreClassRestAPIHandler::setHeaders();

$userData = erLhcoreClassRestAPIHandler::validateRequest();

if ($userData === false) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'message' => 'Unauthorized'
    ));
    exit;
}

$chatId = isset($_POST['chat_id']) ? (int)$_POST['chat_id'] : 0;
$message = isset($_POST['msg']) ? $_POST['msg'] : '';

$chat = erLhcoreClassModelChat::fetch($chatId);

if (!$chat) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'message' => 'Chat not found'
    ));
    exit;
}

// Create message
$msg = new erLhcoreClassModelmsg();
$msg->msg = $message;
$msg->chat_id = $chat->id;
$msg->user_id = $userData->id;
$msg->time = time();
$msg->name_support = $userData->name_support;

// Dispatch event before save
erLhcoreClassChatEventDispatcher::getInstance()->dispatch(
    'chat.before_msg_admin_saved',
    array('msg' => &$msg, 'chat' => &$chat)
);

$msg->saveThis();

// Update chat
$chat->last_msg_id = $msg->id;
$chat->last_op_msg_time = time();
$chat->updateThis();

// Dispatch event after save
erLhcoreClassChatEventDispatcher::getInstance()->dispatch(
    'chat.web_add_msg_admin',
    array('msg' => &$msg, 'chat' => &$chat)
);

echo erLhcoreClassRestAPIHandler::outputResponse(array(
    'error' => false,
    'message_id' => $msg->id
));
exit;
```

## Widget REST API

### Public Endpoints (No Auth Required)

```php
// modules/lhwidgetrestapi/fetchmessages.php
erLhcoreClassRestAPIHandler::setHeaders();

$hash = isset($_POST['hash']) ? $_POST['hash'] : '';
$chatId = isset($_POST['chat_id']) ? (int)$_POST['chat_id'] : 0;
$lastMessageId = isset($_POST['lmid']) ? (int)$_POST['lmid'] : 0;

// Validate hash
$chat = erLhcoreClassModelChat::findOne(array(
    'filter' => array(
        'id' => $chatId,
        'hash' => $hash
    )
));

if (!$chat) {
    erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true
    ));
    exit;
}

// Fetch new messages
$messages = erLhcoreClassModelmsg::getList(array(
    'filter' => array('chat_id' => $chatId),
    'filtergt' => array('id' => $lastMessageId),
    'sort' => 'id ASC'
));

$output = array();
foreach ($messages as $msg) {
    $output[] = array(
        'id' => $msg->id,
        'msg' => $msg->msg,
        'time' => $msg->time,
        'user_id' => $msg->user_id,
        'name_support' => $msg->name_support
    );
}

erLhcoreClassRestAPIHandler::outputResponse(array(
    'error' => false,
    'messages' => $output,
    'chat_status' => $chat->status
));
```

## Swagger/OpenAPI Documentation

```php
// modules/lhrestapi/swagger.php
header('Content-Type: application/json');

ob_start();
include 'modules/lhrestapi/swagger.json';
$content = ob_get_clean();

// Allow extensions to add definitions
$append_definitions = '';
$append_paths = '';

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('restapi.swagger', array(
    'append_definitions' => &$append_definitions,
    'append_paths' => &$append_paths,
));

echo str_replace(
    array('{{base_path}}', '{{append_definitions}}', '{{append_paths}}'),
    array(erLhcoreClassDesign::baseurldirect(), $append_definitions, $append_paths),
    $content
);
```

## Remote API Calls

```php
// For calling remote LHC instances
$apiKey = erLhAbstractModelRestAPIKeyRemote::fetch($keyId);

$response = erLhcoreClassRestAPIHandler::executeRequest(
    $apiKey,
    'restapi/chats',           // Endpoint
    array('status' => 1),      // Parameters
    array(),                   // URL parameters
    'GET'                      // Method
);

$data = json_decode($response, true);
```

## Best Practices

1. **Always validate input:**
   ```php
   $chatId = filter_input(INPUT_POST, 'chat_id', FILTER_VALIDATE_INT);
   if (!$chatId) {
       echo erLhcoreClassRestAPIHandler::outputResponse(array(
           'error' => true,
           'message' => 'Invalid chat_id'
       ));
       exit;
   }
   ```

2. **Use proper HTTP status codes:**
   ```php
   if (!$authenticated) {
       http_response_code(401);
       echo json_encode(array('error' => 'Unauthorized'));
       exit;
   }
   ```

3. **Rate limit sensitive endpoints:**
   ```php
   $cache = CSCacheAPC::getMem();
   $rateKey = 'api_rate_' . $_SERVER['REMOTE_ADDR'];
   $requests = (int)$cache->restore($rateKey);
   
   if ($requests > 100) {
       http_response_code(429);
       echo json_encode(array('error' => 'Rate limit exceeded'));
       exit;
   }
   
   $cache->store($rateKey, $requests + 1, 60);
   ```

4. **Document API responses:**
   ```php
   // Success response
   array(
       'error' => false,
       'data' => $result
   )
   
   // Error response
   array(
       'error' => true,
       'message' => 'Human readable error',
       'code' => 'ERROR_CODE'
   )
   ```

5. **Handle CORS properly:**
   ```php
   // For specific origins
   $allowedOrigins = array('https://example.com', 'https://app.example.com');
   $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
   
   if (in_array($origin, $allowedOrigins)) {
       erLhcoreClassRestAPIHandler::setHeaders('Content-Type: application/json', $origin);
   }
   ```
