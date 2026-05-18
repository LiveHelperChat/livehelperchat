<?php

$Module = ['name' => 'Notifications'];

$ViewList = [
    'subscribe' => [
        'params' => [],
        'uparams' => ['hash', 'vid', 'hash_resume', 'theme', 'action'],
    ],
    'subscribeop' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'list' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'oplist' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'index' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'read' => [
        'params' => [],
        'uparams' => ['id', 'hash', 'theme', 'mode', 'hashread'],
    ],
    'editsubscriber' => [
        'params' => ['id'],
        'functions' => ['use'],
    ],
    'editsubscriberop' => [
        'params' => ['id'],
        'functions' => ['use'],
    ],
    'downloadworker' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'downloadworkerop' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'settings' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'opsettings' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'deletesubscriber' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use'],
    ],
    'opdeletesubscriber' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use'],
    ],
    'serviceworkerop' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'opdeletesubscribermy' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_operator'],
    ],
    'sendtest' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_operator'],
    ],
    'loadsubscriptions' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use_operator'],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'Notifications module'],
    'use_operator' => ['explain' => 'Allow operator to use push notifications'],
];
