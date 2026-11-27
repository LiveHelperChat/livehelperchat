<?php

$Module = ['name' => 'Jitsi Calls'];

$ViewList = [];

$ViewList['configuration'] = [
    'params' => [],
    'functions' => ['configuration'],
];

$ViewList['invite'] = [
    'params' => ['chat_id'],
    'functions' => ['use'],
];

$ViewList['cancel'] = [
    'params' => ['chat_id', 'msg_id'],
    'uparams' => ['action'],
    'functions' => ['use'],
];

$ViewList['joinoperator'] = [
    'params' => ['chat_id', 'msg_id'],
    'functions' => ['use'],
];

$ViewList['statusop'] = [
    'params' => ['chat_id', 'msg_id'],
    'uparams' => ['action'],
    'functions' => ['use'],
];

$ViewList['accept'] = [
    'params' => ['chat_id', 'hash', 'msg_id'],
];

$ViewList['deny'] = [
    'params' => ['chat_id', 'hash', 'msg_id'],
];

$ViewList['join'] = [
    'params' => ['chat_id', 'hash', 'msg_id'],
    'uparams' => ['embed', 'minimal'],
];

$ViewList['status'] = [
    'params' => ['chat_id', 'hash', 'msg_id'],
    'uparams' => ['action'],
];

$FunctionList['configuration'] = ['explain' => 'Jitsi Calls module configuration'];
$FunctionList['use'] = ['explain' => 'Allow operator to initiate and join Jitsi calls'];
