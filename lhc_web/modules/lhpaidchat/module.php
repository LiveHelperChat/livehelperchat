<?php

$Module = ['name' => 'Paid chats module', 'variable_params' => true];

$ViewList = [
    'settings' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use_admin'],
    ],
    'expiredchat' => [
        'params' => ['pchat'],
        'uparams' => [],
    ],
    'removedpaidchat' => [
        'params' => ['pchat'],
        'uparams' => [],
    ],
    'invalidhash' => [
        'params' => ['pchat'],
        'uparams' => [],
    ],
];

$FunctionList = [
    'use_admin' => ['explain' => 'General permission to configure paid chats module'],
];
