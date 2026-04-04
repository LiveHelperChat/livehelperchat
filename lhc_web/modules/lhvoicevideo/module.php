<?php

$Module = ['name' => 'Voice & Video & ScreenShare'];

$ViewList = [];

$ViewList['configuration'] = [
    'params' => [],
    'functions' => ['configuration']
];

$ViewList['sessions'] = [
    'params' => [],
    'functions' => ['configuration']
];

$ViewList['call'] = [
    'params' => ['id', 'hash']
];

$ViewList['join'] = [
    'params' => ['id', 'hash'],
    'uparams' => ['action'],
];

$ViewList['joinop'] = [
    'params' => ['id'],
    'uparams' => ['action'],
    'functions' => ['use']
];

$ViewList['joinoperator'] = [
    'params' => ['id'],
    'functions' => ['use']
];

$FunctionList['configuration'] = ['explain' => 'Voice & Video & ScreenShare module configuration'];
$FunctionList['use'] = ['explain' => 'Allow operator to use Voice & Video & ScreenShare calls'];
