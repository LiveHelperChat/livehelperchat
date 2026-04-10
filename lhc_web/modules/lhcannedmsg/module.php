<?php

$Module = ['name' => 'Canned Messages'];

$ViewList = [
    'showsuggester' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'subject' => [
        'params' => ['canned_id'],
        'uparams' => ['subject', 'status'],
        'functions' => ['use'],
    ],
    'clonereplace' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_replace'],
    ],
    'filter' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'listreplace' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use_replace'],
    ],
    'newreplace' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use_replace'],
    ],
    'editreplace' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use_replace'],
    ],
    'deletereplace' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_replace'],
    ],
    'import' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use_import'],
    ],
    'suggesterconfiguration' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['suggesterconfig'],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'General permission to use canned messages module'],
    'see_global' => ['explain' => 'Allow operator to see global canned messages'],
    'use_import' => ['explain' => 'Allow operator to import canned messages'],
    'use_replace' => ['explain' => 'Allow operator manage replaceable variables'],
    'suggesterconfig' => ['explain' => 'Allow operator configure canned messages suggester'],
];
