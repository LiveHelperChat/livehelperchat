<?php

$Module = ['name' => 'Live helper Chat XML service'];

$ViewList = [];

$ViewList['checklogin'] = [
    'script' => 'checklogin.php',
    'params' => []
];

$ViewList['closedchats'] = [
    'script' => 'closedchats.php',
    'params' => []
];

$ViewList['lists'] = [
    'script' => 'lists.php',
    'params' => []
];

$ViewList['getuseronlinestatus'] = [
    'script' => 'getuseronlinestatus.php',
    'params' => []
];

$ViewList['setonlinestatus'] = [
    'params' => ['status']
];

$ViewList['deletechat'] = [
    'params' => ['chat_id']
];

$ViewList['chatdata'] = [
    'params' => ['chat_id']
];

$ViewList['cannedresponses'] = [
    'params' => ['chat_id']
];

$ViewList['chatssynchro'] = [
    'params' => []
];

$ViewList['closechat'] = [
    'params' => ['chat_id']
];

$ViewList['addmsgadmin'] = [
    'params' => ['chat_id']
];

$ViewList['transferchat'] = [
    'params' => ['chat_id']
];

$ViewList['transferuser'] = [
    'params' => ['chat_id','user_id']
];

$ViewList['accepttransfer'] = [
    'params' => ['transfer_id']
];

$ViewList['accepttransferbychat'] = [
    'params' => ['chat_id']
];

$ViewList['sendnotice'] = [
		'params' => ['online_id']
];

$FunctionList = [];
