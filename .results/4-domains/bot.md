# Bot Domain - Generic Bot Framework

## Overview

Live Helper Chat includes a comprehensive bot framework that enables automated responses, workflows, and integrations. Bots are configured through a visual builder (React app) and executed by the PHP backend.

## Bot Structure

### Bot Model

```php
// lib/models/lhgenericbot/erlhcoreclassmodelgenericbotbot.php
class erLhcoreClassModelGenericBotBot {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_generic_bot_bot';
    
    public $id;
    public $name;
    public $nick;
    public $configuration;    // JSON configuration
    public $avatar;           // Avatar image path
    public $attr_str_1;       // Custom attributes
    public $attr_str_2;
    public $attr_str_3;
}
```

### Trigger Model

```php
// lib/models/lhgenericbot/erlhcoreclassmodelgenericbottrigger.php
class erLhcoreClassModelGenericBotTrigger {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_generic_bot_trigger';
    
    public $id;
    public $name;
    public $bot_id;
    public $group_id;
    public $actions;          // JSON array of actions
    public $default;          // Is default trigger
    public $default_unknown;  // Trigger on unknown input
    public $default_always;   // Always execute
    public $in_progress;      // Active workflow trigger
}
```

### Trigger Event (Pattern Matching)

```php
// lib/models/lhgenericbot/erlhcoreclassmodelgenericbottriggerevent.php
class erLhcoreClassModelGenericBotTriggerEvent {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_generic_bot_trigger_event';
    
    public $id;
    public $trigger_id;
    public $bot_id;
    public $pattern;          // Match patterns (regex, keywords)
    public $pattern_exc;      // Exclusion patterns
    public $type;             // Event type
    public $priority;
    public $configuration;    // Additional config
    public $on_start_type;    // Trigger on chat start
}
```

## Bot Handler

### Processing Messages

```php
// lib/core/lhgenericbot/lhgenericbot.php
class erLhcoreClassGenericBot {
    
    public static function processMessage($chat, $msg)
    {
        $bot = erLhcoreClassModelGenericBotBot::fetch($chat->gbot_id);
        if (!$bot) return;
        
        // Find matching trigger
        $trigger = self::findMatchingTrigger($bot, $msg->msg, $chat);
        
        if ($trigger) {
            self::executeTrigger($trigger, $chat, $msg);
        } else {
            // Execute default unknown trigger
            $defaultTrigger = self::getDefaultUnknownTrigger($bot);
            if ($defaultTrigger) {
                self::executeTrigger($defaultTrigger, $chat, $msg);
            }
        }
    }
    
    public static function findMatchingTrigger($bot, $message, $chat)
    {
        $events = erLhcoreClassModelGenericBotTriggerEvent::getList(array(
            'filter' => array(
                'bot_id' => $bot->id,
                'skip' => 0
            ),
            'sort' => 'priority ASC'
        ));
        
        foreach ($events as $event) {
            if (self::matchesPattern($event->pattern, $message)) {
                // Check exclusion patterns
                if (!empty($event->pattern_exc) && 
                    self::matchesPattern($event->pattern_exc, $message)) {
                    continue;
                }
                
                return erLhcoreClassModelGenericBotTrigger::fetch($event->trigger_id);
            }
        }
        
        return null;
    }
}
```

### Pattern Matching

```php
public static function matchesPattern($pattern, $message)
{
    $patterns = json_decode($pattern, true);
    if (!$patterns) return false;
    
    foreach ($patterns as $p) {
        $type = $p['type'] ?? 'contains';
        $value = $p['pattern'] ?? '';
        
        switch ($type) {
            case 'contains':
                if (stripos($message, $value) !== false) {
                    return true;
                }
                break;
                
            case 'regex':
                if (preg_match('/' . $value . '/i', $message)) {
                    return true;
                }
                break;
                
            case 'exact':
                if (strtolower($message) === strtolower($value)) {
                    return true;
                }
                break;
                
            case 'starts':
                if (stripos($message, $value) === 0) {
                    return true;
                }
                break;
        }
    }
    
    return false;
}
```

## Trigger Actions

### Action Execution

```php
public static function executeTrigger($trigger, $chat, $msg = null)
{
    $actions = json_decode($trigger->actions, true);
    
    foreach ($actions as $action) {
        switch ($action['type']) {
            case 'text':
                self::executeTextAction($action, $chat);
                break;
                
            case 'rest_api':
                self::executeRestApiAction($action, $chat);
                break;
                
            case 'transfer':
                self::executeTransferAction($action, $chat);
                break;
                
            case 'collect_custom':
                self::executeCollectAction($action, $chat);
                break;
                
            case 'trigger':
                // Execute another trigger
                $subTrigger = erLhcoreClassModelGenericBotTrigger::fetch($action['trigger_id']);
                if ($subTrigger) {
                    self::executeTrigger($subTrigger, $chat);
                }
                break;
        }
    }
}
```

### Text Action

```php
public static function executeTextAction($action, $chat)
{
    $msg = new erLhcoreClassModelmsg();
    $msg->chat_id = $chat->id;
    $msg->user_id = -2;  // Bot user ID
    $msg->time = time();
    
    // Process message with variables
    $text = self::processVariables($action['content']['text'], $chat);
    $msg->msg = $text;
    
    // Handle rich content (buttons, cards, etc.)
    if (isset($action['content']['buttons'])) {
        $msg->meta_msg = json_encode(array(
            'content' => array(
                'buttons' => $action['content']['buttons']
            )
        ));
    }
    
    $msg->saveThis();
    
    // Update chat last message
    $chat->last_msg_id = $msg->id;
    $chat->updateThis();
}
```

### REST API Action

```php
public static function executeRestApiAction($action, $chat)
{
    $restApi = erLhcoreClassModelGenericBotRestApi::fetch($action['api_id']);
    if (!$restApi) return;
    
    $config = json_decode($restApi->configuration, true);
    
    // Build request URL with variables
    $url = self::processVariables($config['url'], $chat);
    
    // Make HTTP request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    if ($config['method'] === 'POST') {
        $body = self::processVariables($config['body'], $chat);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    
    // Headers
    $headers = array();
    foreach ($config['headers'] ?? array() as $header) {
        $headers[] = $header['key'] . ': ' . self::processVariables($header['value'], $chat);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Store response in chat variables
    $responseData = json_decode($response, true);
    self::storeBotResponse($chat, $action['output_var'] ?? 'api_response', $responseData);
    
    // Execute success/failure trigger
    if ($responseData && isset($action['success_trigger'])) {
        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['success_trigger']);
        if ($trigger) {
            self::executeTrigger($trigger, $chat);
        }
    }
}
```

### Transfer Action

```php
public static function executeTransferAction($action, $chat)
{
    // Set transfer department
    if (isset($action['department_id'])) {
        $chat->dep_id = $action['department_id'];
    }
    
    // Set transfer user
    if (isset($action['user_id'])) {
        $chat->transfer_uid = $action['user_id'];
    }
    
    // Change status back to pending
    $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
    $chat->gbot_id = 0;  // Remove bot assignment
    $chat->updateThis();
    
    // Dispatch transfer event
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch(
        'chat.genericbot_chat_command_transfer',
        array('chat' => &$chat)
    );
}
```

## Bot Variables

### Variable Processing

```php
public static function processVariables($text, $chat)
{
    // System variables
    $text = str_replace('{lhc.nick}', $chat->nick, $text);
    $text = str_replace('{lhc.email}', $chat->email, $text);
    $text = str_replace('{lhc.phone}', $chat->phone, $text);
    $text = str_replace('{lhc.department}', $chat->department->name, $text);
    
    // Chat variables (from custom fields)
    $chatVars = json_decode($chat->chat_variables, true) ?? array();
    foreach ($chatVars as $key => $value) {
        $text = str_replace('{chat.' . $key . '}', $value, $text);
    }
    
    // Bot collected data
    $workflow = self::getChatWorkflow($chat);
    if ($workflow) {
        $collected = json_decode($workflow->collected_data, true) ?? array();
        foreach ($collected as $key => $value) {
            $text = str_replace('{collected.' . $key . '}', $value, $text);
        }
    }
    
    return $text;
}
```

## Bot Workflow State

### Workflow Model

```php
// Tracks bot conversation state
class erLhcoreClassModelGenericBotChatWorkflow {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_generic_bot_chat_workflow';
    
    public $id;
    public $chat_id;
    public $trigger_id;
    public $time;
    public $identifier;
    public $status;
    public $collected_data;  // JSON data collected from visitor
}
```

### State Management

```php
// Set workflow state
$workflow = new erLhcoreClassModelGenericBotChatWorkflow();
$workflow->chat_id = $chat->id;
$workflow->trigger_id = $trigger->id;
$workflow->time = time();
$workflow->status = 1;  // Active
$workflow->collected_data = json_encode(array());
$workflow->saveThis();

// Update collected data
$collected = json_decode($workflow->collected_data, true);
$collected['field_name'] = $userInput;
$workflow->collected_data = json_encode($collected);
$workflow->updateThis();
```

## Bot Translation

### Translation Group

```php
class erLhcoreClassModelGenericBotTrGroup {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_generic_bot_tr_group';
    
    public $id;
    public $name;
    public $bot_lang;                  // Target language code
    public $use_translation_service;   // Auto-translate
    public $configuration;
}
```

### Translation Items

```php
class erLhcoreClassModelGenericBotTrItem {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_generic_bot_tr_item';
    
    public $id;
    public $group_id;
    public $identifier;    // Translation key
    public $translation;   // Translated text
    public $auto_translate;
}
```

## Best Practices

1. **Use meaningful trigger names:**
   ```
   greeting_flow
   collect_email
   order_status_check
   transfer_to_support
   ```

2. **Set appropriate priorities:**
   ```php
   // More specific patterns should have lower priority (execute first)
   // Priority 10: exact matches
   // Priority 50: keyword matches
   // Priority 100: regex patterns
   ```

3. **Handle API failures gracefully:**
   ```php
   if (!$response || curl_errno($ch)) {
       self::executeTrigger($action['failure_trigger'], $chat);
       return;
   }
   ```

4. **Limit bot response frequency:**
   ```php
   $lastResponse = erLhcoreClassModelGenericBotRepeatRestrict::findOne(array(
       'filter' => array(
           'chat_id' => $chat->id,
           'trigger_id' => $trigger->id
       )
   ));
   
   if ($lastResponse && $lastResponse->counter >= $maxRepeats) {
       return; // Don't repeat same response
   }
   ```

5. **Log bot interactions:**
   ```php
   $event = new erLhcoreClassModelGenericBotChatEvent();
   $event->chat_id = $chat->id;
   $event->content = json_encode(array(
       'trigger' => $trigger->name,
       'input' => $msg->msg,
       'actions' => $actions
   ));
   $event->ctime = time();
   $event->saveThis();
   ```
