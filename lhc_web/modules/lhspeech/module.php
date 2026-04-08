<?php

$Module = ['name' => 'Speech module'];

$ViewList = [
    'setchatspeechlanguage' => [
        'params' => ['chat_id'],
        'functions' => ['change_chat_recognition'],
        'uparams' => [],
    ],
    'getchatdialect' => [
        'params' => ['chat_id'],
        'functions' => ['use'],
        'uparams' => [],
    ],
    'getdialect' => [
        'params' => ['language'],
        'functions' => ['use'],
        'uparams' => [],
    ],
    'defaultlanguage' => [
        'params' => [],
        'functions' => ['manage'],
        'uparams' => [],
    ],
    'languages' => [
        'params' => [],
        'functions' => ['manage'],
        'uparams' => [],
    ],
    'dialects' => [
        'params' => [],
        'functions' => ['manage'],
        'uparams' => [],
    ],
    'deletedialect' => [
        'params' => ['id'],
        'functions' => ['manage'],
        'uparams' => ['csfr'],
    ],
    'editdialect' => [
        'params' => ['id'],
        'functions' => ['manage'],
        'uparams' => [],
    ],
    'editlanguage' => [
        'params' => ['id'],
        'functions' => ['manage'],
        'uparams' => [],
    ],
    'newdialect' => [
        'params' => [],
        'functions' => ['manage'],
        'uparams' => [],
    ],
    'newlanguage' => [
        'params' => [],
        'functions' => ['manage'],
        'uparams' => [],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'Allow operator to use speech recognition module'],
    'manage' => ['explain' => 'Allow user to set application default speech recognition language'],
    'changedefaultlanguage' => ['explain' => 'Allow user to change their personal speech recognition language'],
    'change_chat_recognition' => ['explain' => 'Allow operator to change chat recognition language'],
];
