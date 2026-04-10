<?php

$Module = ['name' => 'Mobile configuration'];

$ViewList = [
    'settings' => [
        'params' => [],
        'functions' => ['manage'],
    ],
    'sessions' => [
        'params' => [],
        'functions' => ['manage'],
    ],
    'editsession' => [
        'params' => ['id'],
        'functions' => ['manage'],
    ],
    'deletesession' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['manage'],
    ],
];

$FunctionList = [
    'manage' => ['explain' => 'Allow operator to manage online sessions'],
];
