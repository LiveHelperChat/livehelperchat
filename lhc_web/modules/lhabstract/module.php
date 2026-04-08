<?php

$Module = ['name' => 'Abstract module'];
$default = ['functions' => ['use']];

$ViewList = [
    'new' => $default + [
        'params' => ['identifier'],
        'uparams' => ['extension'],
    ],
    'list' => $default + [
        'params' => ['identifier'],
        'uparams' => [
            'extension', 'name', 'pinned', 'internal',
            'object_id', 'user_id', 'category', 'source',
            'message', 'timefrom', 'timefrom_hours',
            'timefrom_seconds', 'timefrom_minutes',
            'timeto', 'timeto_minutes', 'timeto_seconds',
            'timeto_hours', 'action', 'csfr', 'include_archive',
        ],
    ],
    'downloadbinnary' => $default + [
        'params' => ['identifier', 'object_id'],
    ],
    'copyautoresponder' => $default + [
        'params' => ['id'],
    ],
    'edit' => $default + [
        'params' => ['identifier', 'object_id'],
        'uparams' => ['extension', 'action', 'csfr'],
    ],
    'delete' => $default + [
        'params' => ['identifier', 'object_id'],
        'uparams' => ['csfr', 'extension'],
    ],
    'index' => $default + [
        'params' => [],
    ],
    'testmasking' => $default + [
        'params' => [],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'Allow to use abstract module'],
];
