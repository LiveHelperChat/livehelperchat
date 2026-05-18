<?php

$Module = ['name' => 'Chat commands', 'variable_params' => true];

$ViewList = [
    'command' => [
        'params' => ['chat_id', 'command_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'Allow operator to use chat commands defined in bot commands section'],
];
