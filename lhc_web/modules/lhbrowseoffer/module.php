<?php

$Module = ['name' => 'Browse offers'];

$ViewList = [
    'getstatus' => [
        'script' => 'getstatus.php',
        'params' => [],
        'functions' => [],
        'uparams' => ['size', 'units', 'identifier', 'height', 'canreopen', 'showoverlay', 'timeout'],
    ],
    'htmlcode' => [
        'script' => 'htmlcode.php',
        'params' => [],
        'functions' => ['manage_bo'],
    ],
    'index' => [
        'script' => 'index.php',
        'params' => [],
        'functions' => ['manage_bo'],
    ],
    'widget' => [
        'script' => 'widget.php',
        'params' => ['hash'],
        'functions' => [],
    ],
    'widgetclosed' => [
        'script' => 'widgetclosed.php',
        'params' => ['id'],
        'functions' => [],
    ],
    'addhit' => [
        'script' => 'addhit.php',
        'params' => ['hash'],
        'functions' => [],
    ],
];

$FunctionList = [
    'manage_bo' => ['explain' => 'Allow user to manage browse offers'],
];
