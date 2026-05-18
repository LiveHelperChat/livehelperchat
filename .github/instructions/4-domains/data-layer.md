# Data Layer Domain - Database and ORM

## Overview

Live Helper Chat uses eZ Components PersistentObject ORM with a custom `erLhcoreClassDBTrait` trait that provides Active Record style database operations.

## Model Structure

### Basic Model Definition

```php
// lib/models/lhchat/erlhcoreclassmodelchat.php
#[\AllowDynamicProperties]
class erLhcoreClassModelChat {

    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_chat';
    public static $dbTableId = 'id';
    public static $dbSessionHandler = 'erLhcoreClassChat::getSession';
    public static $dbSortOrder = 'DESC';
    
    // For custom default sort
    // public static $dbDefaultSort = 'priority DESC, id DESC';
    
    public function getState()
    {
        return [
            'id' => $this->id,
            'nick' => $this->nick,
            'status' => $this->status,
            'time' => $this->time,
            'user_id' => $this->user_id,
            'dep_id' => $this->dep_id,
            // ... all persisted fields
        ];
    }
}
```

### POS Definition

Each model needs a POS (Persistent Object Schema) file:

```php
// pos/lhchat/lhchat.php
$def = new ezcPersistentObjectDefinition();
$def->table = "lh_chat";
$def->class = "erLhcoreClassModelChat";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition('ezcPersistentNativeGenerator');

$def->properties['nick'] = new ezcPersistentObjectProperty();
$def->properties['nick']->columnName = 'nick';
$def->properties['nick']->propertyName = 'nick';
$def->properties['nick']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;
```

## CRUD Operations

### Create

```php
$chat = new erLhcoreClassModelChat();
$chat->nick = 'Visitor';
$chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
$chat->time = time();
$chat->dep_id = $departmentId;
$chat->saveThis();

// Access new ID
$chatId = $chat->id;
```

### Read

```php
// Fetch by ID
$chat = erLhcoreClassModelChat::fetch($chatId);
$chat = erLhcoreClassModelChat::fetch($chatId, false);  // Skip cache
$chat = erLhcoreClassModelChat::fetch($chatId, true, true);  // Throw exception if not found

// Fetch with cache
$chat = erLhcoreClassModelChat::fetchCache($chatId);

// Find one with conditions
$chat = erLhcoreClassModelChat::findOne([
    'filter' => ['hash' => $hash]
]);
```

### Update

```php
$chat = erLhcoreClassModelChat::fetch($chatId);
$chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
$chat->user_id = $operatorId;
$chat->updateThis();

// Or saveThis() which handles both insert and update
$chat->saveThis();
```

### Delete

```php
$chat = erLhcoreClassModelChat::fetch($chatId);
$chat->removeThis();
```

## Query Methods

### getList()

```php
// Basic list
$chats = erLhcoreClassModelChat::getList([
    'limit' => 50,
    'offset' => 0
]);

// With filters
$chats = erLhcoreClassModelChat::getList([
    'filter' => [
        'status' => erLhcoreClassModelChat::STATUS_PENDING_CHAT,
        'dep_id' => $departmentId
    ],
    'limit' => 20
]);

// Multiple values (IN clause)
$chats = erLhcoreClassModelChat::getList([
    'filterin' => [
        'status' => [0, 1],
        'dep_id' => [1, 2, 3]
    ]
]);

// LIKE search
$chats = erLhcoreClassModelChat::getList([
    'filterlike' => ['nick' => 'John']
]);

// Comparison operators
$chats = erLhcoreClassModelChat::getList([
    'filtergt' => ['time' => $startTime],
    'filterlt' => ['time' => $endTime]
]);

// NOT conditions
$chats = erLhcoreClassModelChat::getList([
    'filternot' => ['status' => 2],
    'filternotin' => ['dep_id' => [10, 20]]
]);

// Custom SQL conditions
$chats = erLhcoreClassModelChat::getList([
    'customfilter' => [
        '(nick != "" OR email != "")'
    ]
]);

// Sorting
$chats = erLhcoreClassModelChat::getList([
    'sort' => 'priority DESC, time ASC'
]);

// Joins
$chats = erLhcoreClassModelChat::getList([
    'innerjoin' => [
        'lh_departament' => ['lh_chat.dep_id', 'lh_departament.id']
    ],
    'filter' => ['lh_departament.disabled' => 0]
]);
```

### getCount()

```php
// Simple count
$count = erLhcoreClassModelChat::getCount([
    'filter' => ['status' => 0]
]);

// Count with specific field
$count = erLhcoreClassModelChat::getCount(
    ['filter' => ['status' => 1]],
    'COUNT',           // Operation
    'DISTINCT user_id' // Field
);

// Sum operation
$total = erLhcoreClassModelChat::getCount(
    ['filter' => ['status' => 2]],
    'SUM',
    'chat_duration'
);
```

## Lifecycle Hooks

```php
class erLhcoreClassModelChat {
    use erLhcoreClassDBTrait;
    
    public function beforeSave($params = [])
    {
        if (!$this->id) {
            $this->time = time();
            $this->hash = sha1(microtime() . rand());
        }
    }
    
    public function afterSave($params = [])
    {
        // Dispatch event
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch(
            'chat.chat_saved',
            ['chat' => &$this]
        );
    }
    
    public function beforeUpdate($params = [])
    {
        $this->lsync = time();
    }
    
    public function beforeRemove()
    {
        // Clean up related records
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM lh_msg WHERE chat_id = :chat_id');
        $stmt->bindValue(':chat_id', $this->id);
        $stmt->execute();
    }
    
    public function clearCache()
    {
        parent::clearCache();
        // Custom cache clearing
        $cache = CSCacheAPC::getMem();
        $cache->delete('chat_messages_' . $this->id);
    }
}
```

## Raw Database Access

```php
// Get PDO instance
$db = ezcDbInstance::get();

// Prepared statement
$stmt = $db->prepare('UPDATE lh_chat SET status = :status WHERE id = :id');
$stmt->bindValue(':status', $newStatus, PDO::PARAM_INT);
$stmt->bindValue(':id', $chatId, PDO::PARAM_INT);
$stmt->execute();

// Fetch results
$stmt = $db->prepare('SELECT * FROM lh_chat WHERE status = ?');
$stmt->execute([$status]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

## Locking

```php
// Fetch and lock for update
$chat = erLhcoreClassModelChat::fetchAndLock($chatId);

// Manual lock sync
$chat->syncAndLock('status, user_id');

// Lock in getList
$chats = erLhcoreClassModelChat::getList([
    'filter' => ['status' => 0],
    'lock' => true,
    'limit' => 1
]);
```

## Caching

```php
// Enable SQL cache
$chats = erLhcoreClassModelChat::getList([
    'filter' => ['status' => 1],
    'enable_sql_cache' => true,
    'sql_cache_timeout' => 300  // 5 minutes
]);

// Custom cache key
$chats = erLhcoreClassModelChat::getList([
    'filter' => ['dep_id' => $depId],
    'enable_sql_cache' => true,
    'cache_key' => 'dep_chats_' . $depId
]);
```

## Best Practices

1. **Always use getState() for serializable fields:**
   ```php
   public function getState()
   {
       return [
           // Only include fields that should be persisted
           'id' => $this->id,
           'name' => $this->name,
       ];
   }
   ```

2. **Use filters for performance:**
   ```php
   // Good - uses index
   $chats = erLhcoreClassModelChat::getList([
       'filter' => ['status' => 0],
       'use_index' => 'status'
   ]);
   ```

3. **Limit result sets:**
   ```php
   // Always set reasonable limits
   $chats = erLhcoreClassModelChat::getList([
       'limit' => 100,
       'offset' => ($page - 1) * 100
   ]);
   ```

4. **Use transactions for related operations:**
   ```php
   $db = ezcDbInstance::get();
   $db->beginTransaction();
   try {
       $chat->saveThis();
       $message->saveThis();
       $db->commit();
   } catch (Exception $e) {
       $db->rollBack();
       throw $e;
   }
   ```
