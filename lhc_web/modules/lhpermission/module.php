<?php

$Module = ['name' => 'Permissions configuration'];

$ViewList = [
    'roles' => [
        'params' => [],
        'functions' => ['list'],
    ],
    'newrole' => [
        'script' => 'newrole.php',
        'params' => [],
        'functions' => ['new'],
    ],
    'editrole' => [
        'params' => ['role_id'],
        'functions' => ['edit'],
    ],
    'clonerole' => [
        'params' => ['role_id'],
        'uparams' => ['csfr'],
        'functions' => ['edit'],
    ],
    'editfunction' => [
        'params' => ['function_id'],
        'functions' => ['edit'],
    ],
    'getpermissionsummary' => [
        'params' => ['user_id'],
        'functions' => ['see_permissions'],
    ],
    'request' => [
        'params' => ['permissions'],
        'functions' => ['see_permissions'],
    ],
    'modulefunctions' => [
        'params' => ['module_path'],
        'functions' => ['edit'],
    ],
    'deleterole' => [
        'params' => ['role_id'],
        'uparams' => ['csfr'],
        'functions' => ['delete'],
    ],
    'groupassignrole' => [
        'params' => ['group_id'],
        'functions' => ['delete'],
    ],
    'roleassigngroup' => [
        'params' => ['role_id'],
        'functions' => ['delete'],
    ],
    'explorer' => [
        'params' => [],
        'uparams' => ['action'],
        'functions' => ['explorer'],
    ],
    'whogrants' => [
        'params' => ['user_id', 'module_check', 'function_check'],
        'functions' => ['list'],
    ],
];

$FunctionList = [
    'edit' => ['explain' => 'Access to edit role'],
    'delete' => ['explain' => 'Access to delete role'],
    'list' => ['explain' => 'Access to list roles'],
    'new' => ['explain' => 'Access to create new role'],
    'see_permissions' => ['explain' => 'Allow operator to see their permissions'],
    'see_permissions_users' => ['explain' => 'Allow operator to see all users permissions'],
    'explorer' => ['explain' => 'Permissions explorer'],
];
