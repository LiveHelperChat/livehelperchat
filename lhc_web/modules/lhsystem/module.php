<?php

$Module = ['name' => 'System configuration'];

$ViewList = [
    'htmlcode' => [
        'params' => [],
        'functions' => ['generatejs'],
    ],
    'htmlcodebeta' => [
        'params' => [],
        'functions' => ['generatejs'],
    ],
    'embedcode' => [
        'params' => [],
        'functions' => ['generatejs'],
    ],
    'configuration' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'offlinesettings' => [
        'params' => [],
        'functions' => ['offlinesettings'],
    ],
    'usersactions' => [
        'params' => [],
        'functions' => ['usersactions'],
    ],
    'expirecache' => [
        'params' => [],
        'functions' => ['expirecache'],
        'uparams' => ['csfr'],
    ],
    'smtp' => [
        'params' => [],
        'functions' => ['configuresmtp'],
    ],
    'recaptcha' => [
        'params' => [],
        'functions' => ['configurerecaptcha'],
    ],
    'timezone' => [
        'params' => [],
        'functions' => ['timezone'],
    ],
    'languages' => [
        'params' => [],
        'uparams' => ['updated', 'sa'],
        'functions' => ['changelanguage'],
    ],
    'update' => [
        'params' => [],
        'uparams' => ['action', 'scope'],
        'functions' => ['performupdate'],
    ],
    'ga' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['ga_configuration'],
    ],
    'transferconfiguration' => [
        'params' => [],
        'uparams' => ['action'],
        'functions' => ['transferconfiguration'],
    ],
    'autodbupdate' => [
        'params' => ['hash'],
        'uparams' => [],
    ],
    'singlesetting' => [
        'params' => ['identifier'],
        'uparams' => [],
        'functions' => ['singlesetting'],
    ],
    'bbcode' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['messagecontentprotection'],
    ],
    'notice' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['notice'],
    ],
    'confirmdialog' => [
        'params' => [],
        'uparams' => [],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'Allow user to see configuration links'],
    'expirecache' => ['explain' => 'Allow user to clear cache'],
    'generatejs' => ['explain' => 'Allow user to access HTML generation'],
    'configuresmtp' => ['explain' => 'Allow user to configure SMTP'],
    'configurelanguages' => ['explain' => 'Allow user to configure languages'],
    'changelanguage' => ['explain' => 'Allow user to change their languages'],
    'timezone' => ['explain' => 'Allow user to change global time zone'],
    'performupdate' => ['explain' => 'Allow user to update Live Helper Chat'],
    'changetemplates' => ['explain' => 'Allow user to change e-mail templates'],
    'generate_js_tab' => ['explain' => 'User can see embed code tab'],
    'transferconfiguration' => ['explain' => 'User can configure transfer options'],
    'offlinesettings' => ['explain' => 'Allow user to change offline settings'],
    'configurerecaptcha' => ['explain' => 'Allow user to configure recaptcha'],
    'auditlog' => ['explain' => 'Allow user to see audit log'],
    'usersactions' => ['explain' => 'Allow user to see operators real time chats statistic'],
    'ga_configuration' => ['explain' => 'Allow user to configure Events Tracking'],
    'singlesetting' => ['explain' => 'Allow user to change app settings'],
    'messagecontentprotection' => ['explain' => 'Allow user to configure message content protection'],
    'notice' => ['explain' => 'Allow operator to configure static notice message'],
];
