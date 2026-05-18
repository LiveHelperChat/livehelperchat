<?php

$Module = ['name' => 'Live helper Chat XML service'];

$ViewList = [
    'checklogin' => [
        'script' => 'checklogin.php',
        'params' => [],
    ],
    'closedchats' => [
        'script' => 'closedchats.php',
        'params' => [],
    ],
    'lists' => [
        'script' => 'lists.php',
        'params' => [],
    ],
    'getuseronlinestatus' => [
        'script' => 'getuseronlinestatus.php',
        'params' => [],
    ],
    'setonlinestatus' => [
        'params' => ['status'],
    ],
    'deletechat' => [
        'params' => ['chat_id'],
    ],
    'chatdata' => [
        'params' => ['chat_id'],
    ],
    'cannedresponses' => [
        'params' => ['chat_id'],
    ],
    'chatssynchro' => [
        'params' => [],
    ],
    'closechat' => [
        'params' => ['chat_id'],
    ],
    'addmsgadmin' => [
        'params' => ['chat_id'],
    ],
    'transferchat' => [
        'params' => ['chat_id'],
    ],
    'transferuser' => [
        'params' => ['chat_id', 'user_id'],
    ],
    'accepttransfer' => [
        'params' => ['transfer_id'],
    ],
    'accepttransferbychat' => [
        'params' => ['chat_id'],
    ],
    'sendnotice' => [
        'params' => ['online_id'],
    ],
];

$FunctionList = [];
