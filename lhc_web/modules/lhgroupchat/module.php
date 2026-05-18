<?php

$Module = ['name' => 'Group chats module', 'variable_params' => true];

$ViewList = [
    'loadgroupchat' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
        'multiple_arguments' => [],
    ],
    'loadpublichat' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
        'multiple_arguments' => [],
    ],
    'loadpreviousmessages' => [
        'params' => ['id', 'msg_id'],
        'uparams' => ['initial'],
        'functions' => ['use'],
        'multiple_arguments' => [],
    ],
    'inviteoperator' => [
        'params' => ['id', 'op_id'],
        'uparams' => [],
        'functions' => ['use'],
        'multiple_arguments' => [],
    ],
    'startchatwithoperator' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
        'multiple_arguments' => [],
    ],
    'leave' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
        'multiple_arguments' => [],
    ],
    'addmessage' => [
        'params' => ['id'],
        'uparams' => [],
        'multiple_arguments' => [],
    ],
    'sync' => [
        'params' => [],
        'uparams' => ['opt'],
        'multiple_arguments' => [],
    ],
    'list' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['manage'],
    ],
    'options' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['manage'],
    ],
    'edit' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['manage'],
    ],
    'delete' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['manage'],
    ],
    'new' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['manage'],
    ],
    'newgroupajax' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'searchoperator' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'cancelinvite' => [
        'params' => ['id', 'op_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'Allow operator to use private/public groups'],
    'manage' => ['explain' => 'Permission to manage group chat module'],
    'public_chat' => ['explain' => 'Allow operator to create a public group chat'],
];
