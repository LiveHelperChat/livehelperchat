<?php

$Module = ['name' => 'Chatbox'];

$ViewList = [
    'list' => [
        'script' => 'list.php',
        'params' => [],
        'functions' => ['manage_chatbox'],
    ],
    'syncuser' => [
        'script' => 'syncuser.php',
        'params' => ['chat_id', 'message_id', 'hash'],
        'uparams' => ['mode', 'ot'],
    ],
    'addmsguser' => [
        'script' => 'addmsguser.php',
        'params' => ['chat_id', 'hash'],
        'uparams' => ['mode', 'render'],
    ],
    'view' => [
        'script' => 'view.php',
        'params' => ['id'],
        'functions' => ['manage_chatbox'],
    ],
    'chatwidget' => [
        'script' => 'chatwidget.php',
        'params' => [],
        'uparams' => ['theme', 'sound', 'mode', 'identifier', 'chat_height', 'hashchatbox'],
    ],
    'getstatus' => [
        'script' => 'getstatus.php',
        'params' => [],
        'functions' => [],
        'uparams' => ['theme', 'noresponse', 'position', 'top', 'units', 'width', 'height', 'chat_height', 'sc', 'scm', 'dmn'],
    ],
    'embed' => [
        'script' => 'embed.php',
        'params' => [],
        'uparams' => ['theme', 'chat_height'],
        'functions' => [],
    ],
    'embedcode' => [
        'script' => 'embedcode.php',
        'params' => [],
        'functions' => ['manage_chatbox'],
    ],
    'edit' => [
        'params' => ['id'],
        'functions' => ['manage_chatbox'],
    ],
    'delete' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['manage_chatbox'],
    ],
    'generalsettings' => [
        'params' => ['id'],
        'functions' => ['manage_chatbox'],
    ],
    'new' => [
        'params' => ['id'],
        'functions' => ['manage_chatbox'],
    ],
    'htmlcode' => [
        'script' => 'htmlcode.php',
        'params' => [],
        'functions' => ['manage_chatbox'],
    ],
    'chatwidgetclosed' => [
        'script' => 'chatwidgetclosed.php',
        'params' => [],
    ],
    'configuration' => [
        'script' => 'configuration.php',
        'params' => [],
    ],
];

$FunctionList = [
    'manage_chatbox' => ['explain' => 'Allow user to manage Chatbox module'],
];
