<?php

$Module = ['name' => 'Voice & Video & ScreenShare'];

$ViewList = [
    'configuration' => [
        'params' => [],
        'functions' => ['configuration'],
    ],
    'sessions' => [
        'params' => [],
        'functions' => ['configuration'],
    ],
    'call' => [
        'params' => ['id', 'hash'],
    ],
    'join' => [
        'params' => ['id', 'hash'],
        'uparams' => ['action'],
    ],
    'joinop' => [
        'params' => ['id'],
        'uparams' => ['action'],
        'functions' => ['use'],
    ],
    'joinoperator' => [
        'params' => ['id'],
        'functions' => ['use'],
    ],
];

$FunctionList = [
    'configuration' => ['explain' => 'Voice & Video & ScreenShare module configuration'],
    'use' => ['explain' => 'Allow operator to use Voice & Video & ScreenShare calls'],
];
