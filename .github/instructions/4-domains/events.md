# Events Domain - Event Dispatcher and Webhooks

## Overview

Live Helper Chat uses an event-driven architecture via `erLhcoreClassChatEventDispatcher`. This enables extensibility through event listeners and webhook integrations.

## Event Dispatcher

### Singleton Pattern

```php
// lib/core/lhchat/lhchateventdispatcher.php
class erLhcoreClassChatEventDispatcher {
    
    private $listeners = array();
    private $finishListeners = array();
    
    const STOP_WORKFLOW = 1;
    
    static private $evenDispather = NULL;
    
    static function getInstance()
    {
        if (self::$evenDispather === NULL) {
            self::$evenDispather = new erLhcoreClassChatEventDispatcher();
        }
        return self::$evenDispather;
    }
}
```

### Registering Listeners

```php
// Listen for an event
erLhcoreClassChatEventDispatcher::getInstance()->listen(
    'chat.chat_started',
    'MyExtension::onChatStarted'
);

// Multiple listeners for same event
erLhcoreClassChatEventDispatcher::getInstance()->listen(
    'chat.chat_started',
    array($object, 'handleChatStarted')
);
```

### Dispatching Events

```php
// Dispatch an event
erLhcoreClassChatEventDispatcher::getInstance()->dispatch(
    'chat.chat_started',
    array('chat' => &$chat, 'msg' => &$msg)
);

// Dispatch and check for workflow stop
$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch(
    'chat.before_chat_started',
    array('chat' => &$chat, 'input' => $inputData)
);

if (isset($response['status']) && $response['status'] === erLhcoreClassChatEventDispatcher::STOP_WORKFLOW) {
    // Event handler stopped the workflow
    return $response;
}
```

### Event Handler Implementation

```php
class MyExtension {
    
    public static function onChatStarted($params)
    {
        $chat = $params['chat'];
        
        // Do something with the chat
        erLhcoreClassLog::write('Chat started: ' . $chat->id);
        
        // Optionally stop workflow
        if ($shouldStop) {
            return array('status' => erLhcoreClassChatEventDispatcher::STOP_WORKFLOW);
        }
        
        return null;
    }
}
```

## Common Events

### Chat Events

```php
// Chat lifecycle
'chat.chat_started'              // New chat created
'chat.chat_accepted'             // Operator accepted chat
'chat.chat_closed'               // Chat closed
'chat.chat_deleted'              // Chat deleted

// Messages
'chat.addmsguser'                // Visitor sent message
'chat.web_add_msg_admin'         // Operator sent message
'chat.before_msg_admin_saved'    // Before operator message saved
'chat.messages_added_passive'    // Passive message added

// Status changes
'chat.data_changed'              // Chat data updated
'chat.data_changed_auto_assign'  // Auto-assigned to operator
'chat.status_changed'            // Status changed

// Transfers
'chat.chat_transfered'           // Chat transferred
'chat.genericbot_chat_command_transfer' // Bot triggered transfer
```

### Other Events

```php
// Surveys
'survey.filled'                  // Survey completed

// Bot
'chat.genericbot_event'          // Bot event triggered
'chat.genericbot_handler'        // Bot handling message

// Online visitors
'online_user.pageview_logged'    // Visitor page view

// REST API
'restapi.swagger'                // Swagger definition extension
```

## Global Listeners

Built-in global listeners are set automatically:

```php
// lib/core/lhchat/lhchateventdispatcher.php
public function setGlobalListeners($event = null, $param = array())
{
    if ($this->globalListenersSet == false) {
        $this->globalListenersSet = true;

        if ($this->disableMobile == false) {
            $this->listen('chat.chat_started', 'erLhcoreClassLHCMobile::chatStarted');
            $this->listen('chat.data_changed_auto_assign', 'erLhcoreClassLHCMobile::chatStarted');
            $this->listen('chat.addmsguser', 'erLhcoreClassLHCMobile::newMessage');
            $this->listen('chat.messages_added_passive', 'erLhcoreClassLHCMobile::newMessage');
            $this->listen('chat.genericbot_chat_command_transfer', 'erLhcoreClassLHCMobile::botTransfer');
            $this->listen('chat.chat_transfered', 'erLhcoreClassLHCMobile::chatTransferred');
            $this->listen('group_chat.web_add_msg_admin', 'erLhcoreClassLHCMobile::newGroupMessage');
            $this->listen('chat.subject_add', 'erLhcoreClassLHCMobile::newSubject');
        }
    }
}
```

## Webhooks

### Configuration

```php
// settings/settings.ini.php
'webhooks' => array(
    'enabled' => true,
    'worker' => 'http',       // 'http', 'resque', etc.
    'single_event' => false   // Process one event at a time
),
```

### Webhook Model

```php
// lib/models/lhwebhooks/erlhcoreclassmodelwebhook.php
class erLhcoreClassModelWebhook {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_webhook';
    
    public $id;
    public $name;
    public $event;          // Event name to listen for
    public $bot_id;         // Bot to trigger
    public $trigger_id;     // Trigger to execute
    public $disabled;
    public $configuration;  // JSON config
    public $type;           // 0=bot trigger, 1=http webhook
    public $delay;          // Execution delay in seconds
}
```

### Webhook Worker

```php
// lib/core/lhchat/lhchatwebhookhttp.php
class erLhcoreClassChatWebhookHttp {
    
    public function processEvent($event, $param, $singleEvent = false)
    {
        $webhooks = erLhcoreClassModelWebhook::getList(array(
            'filter' => array(
                'event' => $event,
                'disabled' => 0
            )
        ));
        
        foreach ($webhooks as $webhook) {
            if ($webhook->type == 1) {
                // HTTP webhook
                $this->sendHttpWebhook($webhook, $param);
            } else {
                // Bot trigger
                $this->executeBotTrigger($webhook, $param);
            }
        }
    }
    
    protected function sendHttpWebhook($webhook, $param)
    {
        $config = json_decode($webhook->configuration, true);
        $url = $config['url'];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_exec($ch);
        curl_close($ch);
    }
}
```

## Finish Request Events

For low-priority tasks executed after response is sent:

```php
// Add finish request callback
erLhcoreClassChatEventDispatcher::getInstance()->addFinishRequestEvent(
    'MyClass::cleanup',
    array('chat_id' => $chatId)
);

// Executed in index.php after output
// erLhcoreClassChatEventDispatcher::getInstance()->executeFinishRequest();
```

## Extension Event Registration

```php
// extension/myext/bootstrap.php
$dispatcher = erLhcoreClassChatEventDispatcher::getInstance();

$dispatcher->listen('chat.chat_started', 'erLhcoreClassExtMyext::onChatStarted');
$dispatcher->listen('chat.web_add_msg_admin', 'erLhcoreClassExtMyext::onOperatorMessage');
$dispatcher->listen('chat.chat_closed', 'erLhcoreClassExtMyext::onChatClosed');
```

## Best Practices

1. **Use reference parameters when modifying data:**
   ```php
   erLhcoreClassChatEventDispatcher::getInstance()->dispatch(
       'chat.before_save',
       array('chat' => &$chat)  // Pass by reference
   );
   ```

2. **Return STOP_WORKFLOW when needed:**
   ```php
   public static function validateMessage($params)
   {
       $msg = $params['msg'];
       if (containsSpam($msg)) {
           return array(
               'status' => erLhcoreClassChatEventDispatcher::STOP_WORKFLOW,
               'error' => 'Message contains spam'
           );
       }
   }
   ```

3. **Keep event handlers lightweight:**
   ```php
   // For heavy operations, use finish request
   public static function onChatClosed($params)
   {
       erLhcoreClassChatEventDispatcher::getInstance()->addFinishRequestEvent(
           'self::sendAnalytics',
           array('chat' => $params['chat'])
       );
   }
   ```

4. **Log event handler errors:**
   ```php
   public static function onEvent($params)
   {
       try {
           // Handler logic
       } catch (Exception $e) {
           erLhcoreClassLog::write('Event handler error: ' . $e->getMessage());
       }
   }
   ```

5. **Check event availability:**
   ```php
   // Include caller info for debugging
   $params['lhc_caller'] = debug_backtrace(2, 2)[1];
   $dispatcher->dispatch('event.name', $params);
   ```
