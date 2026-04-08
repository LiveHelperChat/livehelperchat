<?php

$Module = ['name' => 'FAQ'];

$ViewList = [
    'list' => [
        'params' => [],
        'functions' => ['manage_faq'],
    ],
    'delete' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['manage_faq'],
    ],
    'view' => [
        'params' => ['id'],
        'functions' => ['manage_faq'],
    ],
    'new' => [
        'params' => ['id'],
        'functions' => ['manage_faq'],
    ],
    'faqwidget' => [
        'params' => [],
        'uparams' => ['theme', 'url', 'mode', 'identifier', 'search'],
    ],
    'getstatus' => [
        'params' => [],
        'functions' => [],
        'uparams' => ['theme', 'noresponse', 'position', 'top', 'units'],
    ],
    'embed' => [
        'params' => [],
        'uparams' => ['theme'],
        'functions' => [],
    ],
    'embedcode' => [
        'params' => [],
        'functions' => ['manage_faq'],
    ],
    'htmlcode' => [
        'params' => [],
        'functions' => ['manage_faq'],
    ],
];

$FunctionList = [
    'manage_faq' => ['explain' => 'Allow user to manage FAQ'],
];
