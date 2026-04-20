<?php

$Module = ['name' => 'Chat archive module'];

$ViewList = [
    'archive' => [
        'params' => [],
        'functions' => ['archive'],
    ],
    'newarchive' => [
        'params' => [],
        'functions' => ['configuration'],
    ],
    'configuration' => [
        'params' => [],
        'functions' => ['configuration'],
    ],
    'startarchive' => [
        'params' => [],
        'functions' => ['configuration'],
    ],
    'archivechats' => [
        'params' => [],
        'functions' => ['configuration'],
    ],
    'list' => [
        'params' => [],
        'functions' => ['archive'],
    ],
    'listarchivechats' => [
        'params' => ['id'],
        'uparams' => ['chat_duration_from', 'chat_duration_till', 'wait_time_from', 'wait_time_till', 'chat_id', 'nick', 'email', 'timefrom', 'timeto', 'department_id', 'user_id', 'print', 'xls', 'fbst', 'chat_status', 'hum', 'product_id', 'timefrom', 'timefrom_minutes', 'timefrom_hours', 'timeto', 'timeto_minutes', 'timeto_hours'],
        'functions' => ['archive'],
    ],
    'edit' => [
        'params' => ['id'],
        'functions' => ['configuration'],
    ],
    'viewarchivedchat' => [
        'params' => ['archive_id', 'chat_id'],
        'uparams' => ['mode'],
        'functions' => ['archive'],
    ],
    'previewchat' => [
        'params' => ['archive_id', 'chat_id'],
        'functions' => ['archive'],
    ],
    'printchatadmin' => [
        'params' => ['archive_id', 'chat_id'],
        'functions' => ['archive'],
    ],
    'sendmail' => [
        'params' => ['archive_id', 'chat_id'],
        'functions' => ['archive'],
    ],
    'deletearchivechat' => [
        'params' => ['archive_id', 'chat_id'],
        'uparams' => ['csfr'],
        'functions' => ['configuration'],
    ],
    'process' => [
        'params' => ['id'],
        'functions' => ['configuration'],
    ],
];

$FunctionList = [
    'archive' => ['explain' => 'Allow user to use archive functionality'],
    'configuration' => ['explain' => 'Allow user to configure archive'],
];
