# Departments Domain

## Overview

Departments are the core organizational unit in Live Helper Chat. They route chats, manage operator availability, enforce work hours, and control chat limits.

## Department Model

```php
// lib/models/lhdepartament/erlhcoreclassmodeldepartament.php
class erLhcoreClassModelDepartament {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_departament';
    public static $dbTableId = 'id';
    
    public $id;
    public $name;
    public $email;
    public $priority;               // Routing priority
    public $sort_priority;          // Display sort order
    public $disabled;               // Department disabled
    public $hidden;                 // Hidden from widget
    public $identifier;             // URL identifier for routing
    public $alias;                  // Short alias
    
    // Operator settings
    public $max_active_chats;       // Max chats per operator
    public $max_load;               // Max concurrent chats
    public $max_timeout_seconds;    // Chat timeout
    public $delay_before_assign;    // Delay before auto-assign
    public $active_balancing;       // Load balancing enabled
    public $exclude_inactive_chats; // Exclude inactive from count
    
    // Transfer settings
    public $department_transfer_id; // Auto-transfer department
    public $transfer_timeout;       // Transfer timeout seconds
    
    // Online hours
    public $online_hours_active;
    public $mod_start_hour;
    public $mod_end_hour;
    // ... (for each day of week)
    
    // Notifications
    public $xmpp_recipients;        // XMPP notification targets
    public $inform_delay;           // Notification delay
    public $inform_unread;          // Inform on unread
    public $inform_close;           // Inform on close
    
    // Counters (real-time)
    public $pending_chats_counter;
    public $active_chats_counter;
    public $bot_chats_counter;
    
    // Bot configuration
    public $bot_configuration;      // JSON bot settings
    
    // Status
    public $dep_offline;            // Manually set offline
    public $ignore_op_status;       // Ignore operator status
}
```

## Department Online Status

### Check If Department Is Online

```php
// lib/core/lhdepartament/lhdepartament.php
class erLhcoreClassDepartament {
    
    public static function isOnline($depId)
    {
        $dep = erLhcoreClassModelDepartament::fetch($depId);
        
        if (!$dep || $dep->disabled || $dep->hidden) {
            return false;
        }
        
        // Check if manually set offline
        if ($dep->dep_offline == 1) {
            return false;
        }
        
        // Check if ignoring operator status
        if ($dep->ignore_op_status == 1) {
            return self::isInWorkHours($dep);
        }
        
        // Check work hours
        if ($dep->online_hours_active && !self::isInWorkHours($dep)) {
            return false;
        }
        
        // Check for online operators
        return self::hasOnlineOperators($depId);
    }
    
    public static function isInWorkHours($dep)
    {
        if (!$dep->online_hours_active) {
            return true;  // No hours restriction
        }
        
        $now = new DateTime('now', new DateTimeZone($dep->time_zone ?? 'UTC'));
        $dayOfWeek = strtolower($now->format('D'));
        $currentHour = (int)$now->format('H');
        $currentMinute = (int)$now->format('i');
        $currentTime = $currentHour * 100 + $currentMinute;  // HHMM format
        
        // Map day to field names
        $dayMap = array(
            'mon' => array('mod_start_hour', 'mod_end_hour'),
            'tue' => array('tud_start_hour', 'tud_end_hour'),
            'wed' => array('wed_start_hour', 'wed_end_hour'),
            'thu' => array('thd_start_hour', 'thd_end_hour'),
            'fri' => array('frd_start_hour', 'frd_end_hour'),
            'sat' => array('sad_start_hour', 'sad_end_hour'),
            'sun' => array('sud_start_hour', 'sud_end_hour')
        );
        
        $fields = $dayMap[$dayOfWeek];
        $startHour = $dep->{$fields[0]};
        $endHour = $dep->{$fields[1]};
        
        // -1 means closed
        if ($startHour == -1 || $endHour == -1) {
            return false;
        }
        
        // Check custom work hours
        $customHours = self::getCustomWorkHours($dep->id, $now);
        if ($customHours !== false) {
            $startHour = $customHours['start'];
            $endHour = $customHours['end'];
        }
        
        return $currentTime >= $startHour && $currentTime <= $endHour;
    }
    
    public static function hasOnlineOperators($depId)
    {
        $config = erConfigClassLhConfig::getInstance();
        $timeout = $config->getSetting('chat', 'online_timeout', 300);
        
        $onlineOperator = erLhcoreClassModelUserDep::findOne(array(
            'filter' => array(
                'dep_id' => $depId,
                'hide_online' => 0
            ),
            'filtergt' => array(
                'last_activity' => time() - $timeout
            )
        ));
        
        return $onlineOperator !== false;
    }
}
```

### Custom Work Hours

```php
// lib/models/lhdepartament/erlhcoreclassmodeldepartamentcustomworkhours.php
class erLhcoreClassModelDepartamentCustomWorkHours {
    
    public $id;
    public $dep_id;
    public $date_from;        // Unix timestamp
    public $date_to;          // Unix timestamp
    public $start_hour;       // HHMM format
    public $end_hour;         // HHMM format
    public $repetitiveness;   // 0=one-time, 1=yearly
}

// Check for custom hours on specific date
public static function getCustomWorkHours($depId, $date)
{
    $timestamp = $date->getTimestamp();
    
    $custom = erLhcoreClassModelDepartamentCustomWorkHours::findOne(array(
        'filter' => array('dep_id' => $depId),
        'filterlte' => array('date_from' => $timestamp),
        'filtergte' => array('date_to' => $timestamp)
    ));
    
    if ($custom) {
        return array(
            'start' => $custom->start_hour,
            'end' => $custom->end_hour
        );
    }
    
    return false;
}
```

## Department Groups

```php
// lib/models/lhdepartament/erlhcoreclassmodeldepartamentgroup.php
class erLhcoreClassModelDepartamentGroup {
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_departament_group';
    
    public $id;
    public $name;
    public $achats_cnt;      // Active chats counter
    public $pchats_cnt;      // Pending chats counter
    public $bchats_cnt;      // Bot chats counter
    public $max_load;        // Max load for group
}

// Group membership
class erLhcoreClassModelDepartamentGroupMember {
    public $dep_id;
    public $dep_group_id;
}

// Operator group membership
class erLhcoreClassModelDepartamentGroupUser {
    public $dep_group_id;
    public $user_id;
    public $read_only;
    public $exc_indv_autoasign;
    public $assign_priority;
}
```

## Chat Routing and Assignment

### Auto-Assignment Logic

```php
// lib/core/lhchat/lhchatworkflow.php
public static function autoAssign($chat)
{
    $dep = erLhcoreClassModelDepartament::fetch($chat->dep_id);
    
    // Check if department uses load balancing
    if (!$dep->active_balancing) {
        return false;
    }
    
    // Get online operators for department
    $operators = self::getAvailableOperators($dep);
    
    if (empty($operators)) {
        return false;
    }
    
    // Apply assignment algorithm
    $selectedOperator = null;
    
    // Strategy 1: Least active chats
    $minChats = PHP_INT_MAX;
    foreach ($operators as $op) {
        if ($op->active_chats < $minChats) {
            $minChats = $op->active_chats;
            $selectedOperator = $op;
        }
    }
    
    // Check if under max load
    if ($selectedOperator && $selectedOperator->active_chats < $selectedOperator->max_chats) {
        return self::assignToOperator($chat, $selectedOperator->user_id);
    }
    
    return false;
}

public static function getAvailableOperators($dep)
{
    $config = erConfigClassLhConfig::getInstance();
    $timeout = $config->getSetting('chat', 'online_timeout', 300);
    
    return erLhcoreClassModelUserDep::getList(array(
        'filter' => array(
            'dep_id' => $dep->id,
            'hide_online' => 0,
            'exclude_autoasign' => 0
        ),
        'filtergt' => array(
            'last_activity' => time() - $timeout
        ),
        'sort' => 'last_accepted ASC, active_chats ASC'
    ));
}
```

### Chat Priority Routing

```php
// lib/models/lhabstract/erlhcoreclassmodelabtractchatpriority.php
class erLhAbstractModelChatPriority {
    
    public $id;
    public $value;              // Match value
    public $role_destination;   // Target role
    public $present_role_is;    // Source role (from visitor data)
    public $dep_id;             // Source department
    public $dest_dep_id;        // Destination department
    public $sort_priority;      // Processing order
    public $priority;           // Chat priority to set
    public $skip_bot;           // Skip bot processing
}

// Apply priority rules
public static function applyPriorityRules($chat)
{
    $rules = erLhAbstractModelChatPriority::getList(array(
        'filterin' => array(
            'dep_id' => array(0, $chat->dep_id)  // Global or department-specific
        ),
        'sort' => 'sort_priority ASC'
    ));
    
    foreach ($rules as $rule) {
        if (self::matchesRule($chat, $rule)) {
            $chat->priority = $rule->priority;
            
            if ($rule->dest_dep_id > 0) {
                $chat->dep_id = $rule->dest_dep_id;
            }
            
            if ($rule->skip_bot) {
                $chat->gbot_id = 0;
            }
            
            break;
        }
    }
}
```

## Department Availability Tracking

```php
// lib/models/lhdepartament/erlhcoreclassmodeldepartamentavailability.php
class erLhcoreClassModelDepartamentAvailability {
    
    public $id;
    public $dep_id;
    public $hour;          // Hour of day (0-23)
    public $hourminute;    // Hour and minute (HHMM)
    public $minute;        // Minute (0-59)
    public $time;          // Unix timestamp
    public $ymdhi;         // YearMonthDayHourMinute
    public $ymd;           // YearMonthDay
    public $status;        // 1=online, 0=offline
}

// Track availability (called by cron)
public static function trackAvailability()
{
    $departments = erLhcoreClassModelDepartament::getList(array(
        'filter' => array('disabled' => 0)
    ));
    
    $now = time();
    $ymd = date('Ymd', $now);
    $ymdhi = date('YmdHi', $now);
    
    foreach ($departments as $dep) {
        $isOnline = erLhcoreClassDepartament::isOnline($dep->id);
        
        $availability = new erLhcoreClassModelDepartamentAvailability();
        $availability->dep_id = $dep->id;
        $availability->time = $now;
        $availability->ymd = $ymd;
        $availability->ymdhi = $ymdhi;
        $availability->hour = date('H', $now);
        $availability->minute = date('i', $now);
        $availability->hourminute = date('Hi', $now);
        $availability->status = $isOnline ? 1 : 0;
        $availability->saveThis();
    }
}
```

## Widget Department Selection

```php
// Get departments for widget
public static function getDepartmentsForWidget($filter = array())
{
    $defaultFilter = array(
        'filter' => array(
            'disabled' => 0,
            'hidden' => 0
        ),
        'sort' => 'sort_priority ASC, name ASC'
    );
    
    $filter = array_merge($defaultFilter, $filter);
    
    $departments = erLhcoreClassModelDepartament::getList($filter);
    
    // Add online status to each
    foreach ($departments as &$dep) {
        $dep->is_online = erLhcoreClassDepartament::isOnline($dep->id);
    }
    
    return $departments;
}

// Template usage
<?php foreach ($departments as $dep): ?>
    <option value="<?php echo $dep->id; ?>" 
            data-online="<?php echo $dep->is_online ? '1' : '0'; ?>">
        <?php echo htmlspecialchars($dep->name); ?>
        <?php if (!$dep->is_online): ?>(Offline)<?php endif; ?>
    </option>
<?php endforeach; ?>
```

## Department Limits

```php
// lib/models/lhdepartament/erlhcoreclassmodeldepartamentlimitgroup.php
class erLhcoreClassModelDepartamentLimitGroup {
    
    public $id;
    public $name;
    public $pending_max;    // Max pending chats in group
}

// Limit group membership
class erLhcoreClassModelDepartamentLimitGroupMember {
    public $dep_id;
    public $dep_limit_group_id;
}

// Check if department is over limit
public static function isOverLimit($depId)
{
    $dep = erLhcoreClassModelDepartament::fetch($depId);
    
    // Check department pending limit
    if ($dep->pending_max > 0 && $dep->pending_chats_counter >= $dep->pending_max) {
        return true;
    }
    
    // Check limit groups
    $limitGroups = erLhcoreClassModelDepartamentLimitGroupMember::getList(array(
        'filter' => array('dep_id' => $depId)
    ));
    
    foreach ($limitGroups as $lgm) {
        $group = erLhcoreClassModelDepartamentLimitGroup::fetch($lgm->dep_limit_group_id);
        
        // Sum pending across all departments in group
        $totalPending = self::getPendingInLimitGroup($lgm->dep_limit_group_id);
        
        if ($group->pending_max > 0 && $totalPending >= $group->pending_max) {
            return true;
        }
    }
    
    return false;
}
```

## Department Chat Count Maintenance

```php
// Update counters (called when chat status changes)
public static function updateDepartmentCounters($depId)
{
    $db = ezcDbInstance::get();
    
    // Pending count
    $stmt = $db->prepare(
        'UPDATE lh_departament SET pending_chats_counter = 
         (SELECT COUNT(*) FROM lh_chat WHERE dep_id = :dep AND status = 0)
         WHERE id = :dep'
    );
    $stmt->execute(array(':dep' => $depId));
    
    // Active count
    $stmt = $db->prepare(
        'UPDATE lh_departament SET active_chats_counter = 
         (SELECT COUNT(*) FROM lh_chat WHERE dep_id = :dep AND status = 1)
         WHERE id = :dep'
    );
    $stmt->execute(array(':dep' => $depId));
    
    // Bot count
    $stmt = $db->prepare(
        'UPDATE lh_departament SET bot_chats_counter = 
         (SELECT COUNT(*) FROM lh_chat WHERE dep_id = :dep AND status = 5)
         WHERE id = :dep'
    );
    $stmt->execute(array(':dep' => $depId));
}
```
