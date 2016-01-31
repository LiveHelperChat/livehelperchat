<?php

include 'lhrestapi.php';

$LHCRestAPI = new LHCRestAPI('<address>', '<username>', '<apikey>');

/* 
 * Possible values for status
 * 
    const STATUS_PENDING_CHAT = 0;
    const STATUS_ACTIVE_CHAT = 1;
    const STATUS_CLOSED_CHAT = 2;
    const STATUS_CHATBOX_CHAT = 3;
    const STATUS_OPERATORS_CHAT = 4;
*/

// Fetch chats
$response = $LHCRestAPI->execute('chats', array(
    'status' => 1,
    'departament_id' => 5,
    'user_id' => 1,
    'update_activity' => 1 // Forces to update last acitivity, can be used with any call <optional>
), true);
print_r($response);

// Returns departments
$response = $LHCRestAPI->execute('departaments', array(   
), true);
print_r($response);

// Fetch chat
$response = $LHCRestAPI->execute('fetchchat', array(   
    'chat_id' => 5388
), true);
print_r($response);

$response = $LHCRestAPI->execute('fetchchatmessages', array(   
    'chat_id' => 5388,
    'last_message_id' => 3203, // Optional, return messages from this <optional>
), true);
print_r($response);
