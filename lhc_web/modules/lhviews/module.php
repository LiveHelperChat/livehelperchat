<?php

$Module = ['name' => 'Views module'];

$ViewList = [
    'home' => [
        'params' => ['id'],
        'functions' => ['use'],
    ],
    'loadinitialdata' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'acceptinvites' => [
        'params' => [],
        'uparams' => ['view'],
        'functions' => ['use'],
    ],
    'shareview' => [
        'params' => ['id'],
        'functions' => ['use'],
    ],
    'updatepassivemode' => [
        'params' => ['id'],
        'functions' => ['use'],
    ],
    'loadview' => [
        'params' => ['id'],
        'uparams' => ['mode'],
        'functions' => ['use'],
    ],
    'updateviews' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'edit' => [
        'params' => ['id'],
        'functions' => ['use'],
    ],
    'exportview' => [
        'params' => ['id'],
        'functions' => ['use'],
    ],
    'deleteview' => [
        'params' => ['id'],
        'functions' => ['use'],
    ],
    'view' => [
        'params' => [],
        'functions' => ['configuration'],
    ],
];

$FunctionList = [
    'configuration' => ['explain' => 'Allow operator to configure views'],
    'use' => ['explain' => 'Allow operator to use views'],
    'use_chat' => ['explain' => 'Allow operator to use views for chats'],
    'use_mail' => ['explain' => 'Allow operator to use views for mails'],
];
