<?php

$Module = ['name' => 'Webhooks'];

$ViewList = [];

$ViewList['configuration'] = [
    'params' => [],
    'uparams' => ['name','enabled','event'],
    'functions' => ['configuration']
];

$ViewList['pushchat'] = [
    'params' => [],
    'functions' => ['configuration']
];

$ViewList['new'] = [
    'params' => [],
    'functions' => ['configuration']
];

$ViewList['newincoming'] = [
    'params' => [],
    'functions' => ['configuration']
];

$ViewList['editincoming'] = [
    'params' => ['id'],
    'functions' => ['configuration']
];

$ViewList['incoming'] = [
    'params' => ['identifier'],
];

$ViewList['edit'] = [
    'params' => ['id'],
    'uparams' => ['action','csfr'],
    'functions' => ['configuration']
];

$ViewList['delete'] = [
    'params' => ['id'],
    'uparams' => ['csfr'],
    'functions' => ['configuration']
];

$ViewList['deleteincoming'] = [
    'params' => ['id'],
    'uparams' => ['csfr'],
    'functions' => ['configuration']
];

$ViewList['incomingwebhooks'] = [
    'params' => [],
    'uparams' => [],
    'functions' => ['configuration']
];

$ViewList['dispatch'] = [
    'params' => [],
    'uparams' => [],
    'functions' => ['configuration']
];

$FunctionList['configuration'] = ['explain' => 'Webhooks module configuration'];
