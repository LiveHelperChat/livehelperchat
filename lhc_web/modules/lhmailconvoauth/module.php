<?php

$Module = ['name' => 'Mail conversation OAuth module'];

$ViewList = [
    'mslogin' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'options' => [
        'params' => [],
        'functions' => ['manage_oauth'],
    ],
    'msoauth' => [
        'params' => [],
    ],
];

$FunctionList = [
    'use_admin' => ['explain' => 'Permission to use mail conversation OAuth module'],
    'manage_oauth' => ['explain' => 'Permission to use mail conversation OAuth module'],
];
