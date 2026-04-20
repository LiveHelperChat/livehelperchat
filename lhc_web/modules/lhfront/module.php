<?php

$Module = ['name' => 'Frontpage'];

$ViewList = [
    'default' => [
        'params' => [],
        'uparams' => ['cid', 'mid'],
        'functions' => ['use'],
    ],
    'settings' => [
        'params' => [],
        'uparams' => ['action', 'csfr'],
        'functions' => ['use'],
    ],
    'tabs' => [
        'params' => [],
        'uparams' => ['id', 'idmail'],
        'functions' => ['use'],
        'multiple_arguments' => ['id', 'idmail'],
    ],
    'switchdashboard' => [
        'params' => [],
        'uparams' => ['action', 'csfr'],
        'functions' => ['use'],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'General frontpage use permission'],
    'switch_dashboard' => ['explain' => 'Allow operator to switch between new/old dashboards'],
];
