<?php

$Module = ['name' => 'Departments configuration'];

$ViewList = [
    'departments' => [
        'params' => [],
        'uparams' => ['visible_if_online', 'hidden', 'disabled', 'name', 'export', 'alias', 'identifier', 'empty_alias', 'empty_identifier'],
        'functions' => ['list'],
    ],
    'new' => [
        'params' => [],
        'functions' => ['create'],
    ],
    'edit' => [
        'params' => ['departament_id'],
        'uparams' => ['action'],
        'functions' => ['edit'],
    ],
    'clone' => [
        'params' => ['departament_id'],
        'functions' => ['edit'],
        'uparams' => ['csfr'],
    ],
    'index' => [
        'params' => [],
        'functions' => ['list'],
    ],
    'brands' => [
        'params' => [],
        'functions' => ['managebrands'],
    ],
    'newbrand' => [
        'params' => [],
        'functions' => ['managebrands'],
    ],
    'editbrand' => [
        'params' => ['id'],
        'functions' => ['managebrands'],
    ],
    'group' => [
        'params' => [],
        'functions' => ['managegroups'],
    ],
    'limitgroup' => [
        'params' => [],
        'functions' => ['managegroups'],
    ],
    'newgroup' => [
        'params' => [],
        'functions' => ['managegroups'],
    ],
    'newlimitgroup' => [
        'params' => [],
        'functions' => ['managegroups'],
    ],
    'editlimitgroup' => [
        'params' => ['id'],
        'functions' => ['managegroups'],
    ],
    'editgroup' => [
        'params' => ['id'],
        'uparams' => ['action'],
        'functions' => ['managegroups'],
    ],
    'deletegroup' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['managegroups'],
    ],
    'deletebrand' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['managebrands'],
    ],
    'deletelimitgroup' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['managegroups'],
    ],
];

$FunctionList = [
    'list' => ['explain' => 'Access to list departments'],
    'create' => ['explain' => 'Permission to create a new department'],
    'edit' => ['explain' => 'Permission to edit department'],
    'delete' => ['explain' => 'Permission to delete department'],
    'see_all' => ['explain' => 'Allow user to see all departments. Even if they do not have permission to see chats.'],
    'actworkflow' => ['explain' => 'Allow user to change transfer workflow'],
    'actautoassignment' => ['explain' => 'Allow user to change auto assignment'],
    'manageall' => ['explain' => 'Allow user to manage all departments, not only assigned to him'],
    'managegroups' => ['explain' => 'Allow user to manage all department groups, not only assigned to him'],
    'managesurvey' => ['explain' => 'Allow operator to change department surveys'],
    'managealias' => ['explain' => 'Allow operator to change department alias'],
    'managedesign' => ['explain' => 'Allow operator to change design section'],
    'managebrands' => ['explain' => 'Allow operator to manage brands'],
];
