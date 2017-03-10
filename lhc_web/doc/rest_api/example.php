<?php

include 'lhrestapi.php';

$LHCRestAPI = new LHCRestAPI('<address>', '<username>', '<apikey>');

// Set operator status online or offline
$response = $LHCRestAPI->execute('setoperatorstatus', array(
    'status' => 'false', // false - offline, true - online
    // Any argument of below has to be provided
    //'user_id' => '1',
    //'username' => 'admin'
    'email' => 'remdex@gmail.com',
), true);
print_r($response);

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
));
print_r($response);

// Returns departments
$response = $LHCRestAPI->execute('departaments', array(   
));
print_r($response);

// Fetch chat
$response = $LHCRestAPI->execute('fetchchat', array(   
    'chat_id' => 5388
));
print_r($response);

$response = $LHCRestAPI->execute('fetchchatmessages', array(   
    'chat_id' => 5388,
    'last_message_id' => 3203, // Optional, return messages from this <optional>
));
print_r($response);

// Examples with XML
$response = $LHCRestAPI->execute('fetchchatmessages', array(
    'chat_id' => 6724,
    'last_message_id' => 0, // Optional, return messages from this <optional>
    'format' => 'xml'
),array(),'GET',false);
print_r($response);

