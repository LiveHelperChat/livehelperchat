<?php

$Module = ['name' => 'Audit', 'variable_params' => true];

$ViewList = [
    'configuration' => [
        'params' => [],
        'uparams' => ['csfr', 'action', 'id'],
        'functions' => ['use'],
    ],
    'loginhistory' => [
        'params' => [],
        'uparams' => ['user_id'],
        'functions' => ['use'],
    ],
    'debuginvitation' => [
        'params' => ['ouser_id', 'invitation_id', 'tag'],
        'uparams' => ['action'],
        'functions' => ['use'],
    ],
    'logrecord' => [
        'params' => ['id'],
        'functions' => ['log_preview'],
    ],
    'logjserror' => [
        'params' => [],
        'uparams' => [],
        'functions' => [],
    ],
    'test' => [
        'params' => [],
        'uparams' => [],
    ],
    'copycurl' => [
        'params' => ['id', 'scope'],
        'uparams' => [],
        'functions' => ['see_audit_system'],
    ],
    'previewmessages' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['preview_messages'],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'Allow operator to configure audit module'],
    'log_preview' => ['explain' => 'Allow operator to preview log record'],
    'see_system' => ['explain' => 'Allow operator to see system status'],
    'see_audit_system' => ['explain' => 'Allow operator to see audit system messages'],
    'ignore_view_actions' => ['explain' => 'Do not log view actions from operator'],
    'see_op_actions' => ['explain' => 'Allow operator to see other operator logged actions'],
    'preview_messages' => ['explain' => 'Allow operator to preview chat messages as visitor'],
];
