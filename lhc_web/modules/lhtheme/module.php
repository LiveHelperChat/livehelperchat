<?php

$Module = ['name' => 'Theme', 'variable_params' => true];

$ViewList = [
    'export' => [
        'params' => ['theme'],
        'functions' => ['administratethemes'],
    ],
    'import' => [
        'params' => [],
        'functions' => ['administratethemes'],
    ],
    'index' => [
        'params' => [],
        'functions' => ['administratethemes'],
    ],
    'default' => [
        'params' => [],
        'functions' => ['administratethemes'],
    ],
    'editthemebydepgroup' => [
        'params' => ['id'],
        'functions' => ['administratethemes'],
    ],
    'defaultadmintheme' => [
        'params' => [],
        'functions' => ['administratethemes'],
    ],
    'adminthemes' => [
        'params' => [],
        'functions' => ['administratethemes'],
    ],
    'personaltheme' => [
        'params' => [],
        'functions' => ['personaltheme'],
    ],
    'renderpreview' => [
        'params' => ['id'],
        'functions' => ['use_operator'],
    ],
    'admincss' => [
        'params' => ['id'],
    ],
    'adminnewtheme' => [
        'params' => [],
        'functions' => ['administratethemes'],
    ],
    'adminthemedelete' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['administratethemes'],
    ],
    'adminthemeedit' => [
        'params' => ['id'],
        'functions' => ['administratethemes'],
    ],
    'deleteresource' => [
        'params' => ['id', 'context', 'hash'],
        'functions' => ['administratethemes'],
    ],
    'gethash' => [
        'params' => [],
    ],
];

$FunctionList = [
    'administratethemes' => ['explain' => 'Allow users to maintain themes'],
    'personaltheme' => ['explain' => 'Allow operators have their own personal back office theme'],
    'use_operator' => ['explain' => 'Allow operator to preview trigger'],
];
