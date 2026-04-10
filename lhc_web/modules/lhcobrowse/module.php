<?php

$Module = ['name' => 'CO Browse module'];

$ViewList = [
    'browse' => [
        'params' => ['chat_id'],
        'functions' => ['browse'],
        'uparams' => [],
    ],
    'mirror' => [
        'params' => [],
        'functions' => ['browse'],
        'uparams' => [],
    ],
    'checkmirrorchanges' => [
        'params' => ['chat_id'],
        'functions' => ['browse'],
        'uparams' => ['cobrowsemode'],
    ],
    'checkinitializer' => [
        'params' => ['chat_id'],
        'functions' => ['browse'],
        'uparams' => [],
    ],
    'storenodemap' => [
        'params' => [],
        'uparams' => ['vid', 'hash', 'hash_resume', 'sharemode'],
    ],
    'finishsession' => [
        'params' => [],
        'uparams' => ['vid', 'hash', 'hash_resume', 'sharemode'],
    ],
    'proxycss' => [
        'params' => ['chat_id'],
        'functions' => ['browse'],
        'uparams' => ['cobrowsemode'],
    ],
];

$FunctionList = [
    'browse' => ['explain' => 'Allow operator to use co-browse functionality'],
];
