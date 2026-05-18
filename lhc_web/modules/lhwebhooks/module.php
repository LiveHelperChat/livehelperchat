<?php

$Module = ['name' => 'Webhooks'];

$ViewList = [
    'configuration' => [
        'params' => [],
        'uparams' => ['name', 'enabled', 'event'],
        'functions' => ['configuration'],
    ],
    'pushchat' => [
        'params' => [],
        'functions' => ['configuration'],
    ],
    'new' => [
        'params' => [],
        'functions' => ['configuration'],
    ],
    'newincoming' => [
        'params' => [],
        'functions' => ['configuration'],
    ],
    'editincoming' => [
        'params' => ['id'],
        'functions' => ['configuration'],
    ],
    'incoming' => [
        'params' => ['identifier'],
    ],
    'edit' => [
        'params' => ['id'],
        'uparams' => ['action', 'csfr'],
        'functions' => ['configuration'],
    ],
    'delete' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['configuration'],
    ],
    'deleteincoming' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['configuration'],
    ],
    'incomingwebhooks' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['configuration'],
    ],
    'dispatch' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['configuration'],
    ],
];

$FunctionList = [
    'configuration' => ['explain' => 'Webhooks module configuration'],
];
