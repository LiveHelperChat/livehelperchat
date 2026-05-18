<?php

$Module = ['name' => 'Mail archive module'];

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
    'list' => [
        'params' => [],
        'functions' => ['archive'],
    ],
    'scheduledpurge' => [
        'params' => [],
        'functions' => ['archive'],
    ],
    'scheduledpurgedelete' => [
        'params' => ['schedule', 'class'],
        'uparams' => ['csfr'],
        'functions' => ['archive'],
    ],
    'listarchivemails' => [
        'params' => ['id'],
        'uparams' => ['is_external', 'ipp', 'timefromts', 'opened', 'phone', 'lang_ids', 'is_followup', 'sortby', 'conversation_status_ids', 'undelivered', 'view', 'has_attachment', 'mailbox_ids', 'conversation_id', 'subject', 'department_ids', 'department_group_ids', 'user_ids', 'group_ids', 'subject_id', 'wait_time_from', 'wait_time_till', 'conversation_id', 'nick', 'email', 'timefrom', 'timeto', 'user_id', 'export', 'conversation_status', 'hum', 'product_id', 'timefrom', 'timefrom_minutes', 'timefrom_hours', 'timeto', 'timeto_minutes', 'timeto_hours', 'department_group_id', 'group_id'],
        'functions' => ['archive'],
        'multiple_arguments' => ['department_ids', 'department_group_ids', 'user_ids', 'group_ids', 'bot_ids', 'mailbox_ids', 'conversation_status_ids', 'lang_ids', 'subject_id'],
    ],
    'edit' => [
        'params' => ['id'],
        'functions' => ['configuration'],
    ],
    'process' => [
        'params' => ['id'],
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
];

$FunctionList = [
    'archive' => ['explain' => 'Allow user to use archive functionality'],
    'configuration' => ['explain' => 'Allow user to configure archive'],
];
