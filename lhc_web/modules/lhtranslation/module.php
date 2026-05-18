<?php

$Module = ['name' => 'Translation module'];

$ViewList = [
    'configuration' => [
        'params' => [],
        'functions' => ['configuration'],
        'uparams' => ['csfr', 'action'],
    ],
    'starttranslation' => [
        'params' => ['chat_id', 'visitor_language', 'operator_language'],
        'functions' => ['use'],
        'uparams' => [],
    ],
    'translateoperatormessage' => [
        'params' => ['chat_id'],
        'functions' => ['use'],
        'uparams' => [],
    ],
    'translatevisitormessage' => [
        'params' => ['chat_id', 'msg_id'],
        'functions' => ['use'],
        'uparams' => [],
    ],
];

$FunctionList = [
    'configuration' => ['explain' => 'Allow operator to configure automatic translations module'],
    'use' => ['explain' => 'Allow operator to use automatic translations'],
];
