<?php

$Module = ['name' => 'Chats export module'];

$ViewList = [
    'getchat' => [
        'params' => ['hash', 'chat_id'],
        'uparams' => ['format'],
    ],
    'getcount' => [
        'script' => 'getcount.php',
        'params' => ['hash'],
        'multiple_arguments' => ['status'],
        'uparams' => ['format', 'status'],
    ],
    'getlist' => [
        'params' => ['hash'],
        'uparams' => ['limit', 'format', 'status'],
        'functions' => [],
        'multiple_arguments' => ['status'],
    ],
];

$FunctionList = [];
