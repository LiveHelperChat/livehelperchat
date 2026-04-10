<?php

$Module = ['name' => 'Chat settings', 'variable_params' => true];

$ViewList = [
    'startsettingslist' => [
        'params' => [],
        'functions' => ['administrate'],
    ],
    'newstartsettings' => [
        'params' => [],
        'functions' => ['administrate'],
    ],
    'copyfrom' => [
        'params' => ['from'],
        'uparams' => ['csfr'],
        'functions' => ['administrate'],
    ],
    'editstartsettings' => [
        'params' => ['id'],
        'functions' => ['administrate'],
    ],
    'deletestartsettings' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['administrate'],
    ],
    'startchatformsettings' => [
        'params' => [],
        'functions' => ['administrate'],
    ],
    'startchatformsettingsindex' => [
        'params' => [],
        'functions' => ['administrate'],
    ],
    'testencryption' => [
        'params' => [],
        'functions' => ['administrate'],
    ],
    'editeventsettings' => [
        'params' => ['id'],
        'functions' => ['events'],
    ],
    'eventlist' => [
        'params' => [],
        'functions' => ['events'],
    ],
    'neweventsettings' => [
        'params' => [],
        'functions' => ['events'],
    ],
    'eventindex' => [
        'params' => [],
        'functions' => ['events'],
    ],
    'deleteevent' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['administrate'],
    ],
];

$FunctionList = [
    'administrate' => ['explain' => 'Allow to configure chat start form'],
    'events' => ['explain' => 'Allow to configure events tracking'],
];
