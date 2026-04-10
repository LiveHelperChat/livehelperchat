<?php

$Module = ['name' => 'Forms module'];

$ViewList = [
    'index' => [
        'params' => [],
        'functions' => ['manage_fm'],
    ],
    'fill' => [
        'params' => ['form_id'],
        'functions' => [],
    ],
    'collected' => [
        'params' => ['form_id'],
        'uparams' => ['action', 'id', 'csfr'],
        'functions' => ['manage_fm'],
    ],
    'viewcollected' => [
        'params' => ['collected_id'],
        'functions' => ['manage_fm'],
    ],
    'embedcode' => [
        'params' => [],
        'functions' => ['generate_js'],
    ],
    'downloadcollected' => [
        'params' => ['form_id'],
        'functions' => ['manage_fm'],
    ],
    'downloaditem' => [
        'params' => ['collected_id'],
        'functions' => ['manage_fm'],
    ],
    'download' => [
        'params' => ['collected_id', 'attr_name'],
        'functions' => ['manage_fm'],
    ],
    'embed' => [
        'params' => ['form_id'],
        'functions' => [],
    ],
    'formwidget' => [
        'params' => ['form_id'],
    ],
];

$FunctionList = [
    'manage_fm' => ['explain' => 'Allow user to manage form module'],
    'delete_fm' => ['explain' => 'Allow user to delete forms'],
    'generate_js' => ['explain' => 'Allow user to generate page embed js'],
];
