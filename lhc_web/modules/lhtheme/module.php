<?php

$Module = ['name' => 'Theme', 'variable_params' => true];

$ViewList = [];

$ViewList['export'] = [
    'params' => ['theme'],
    'functions' => ['administratethemes']
];

$ViewList['import'] = [
    'params' => [],
    'functions' => ['administratethemes']
];

$ViewList['index'] = [
    'params' => [],
    'functions' => ['administratethemes']
];

$ViewList['default'] = [
    'params' => [],
    'functions' => ['administratethemes']
];

$ViewList['editthemebydepgroup'] = [
    'params' => ['id'],
    'functions' => ['administratethemes']
];

$ViewList['defaultadmintheme'] = [
    'params' => [],
    'functions' => ['administratethemes']
];

$ViewList['adminthemes'] = [
    'params' => [],
    'functions' => ['administratethemes']
];

$ViewList['personaltheme'] = [
    'params' => [],
    'functions' => ['personaltheme']
];

$ViewList['renderpreview'] = [
    'params' => ['id'],
    'functions' => ['use_operator']
];

$ViewList['admincss'] = ['params' => ['id']];

$ViewList['adminnewtheme'] = [
    'params' => [],
    'functions' => ['administratethemes']
];

$ViewList['adminthemedelete'] = [
    'params' => ['id'],
    'uparams' => ['csfr'],
    'functions' => ['administratethemes'],
];

$ViewList['adminthemeedit'] = [
    'params' => ['id'],
    'functions' => ['administratethemes']
];

$ViewList['deleteresource'] = [
    'params' => ['id', 'context', 'hash'],
    'functions' => ['administratethemes'],
];

$ViewList['gethash'] = ['params' => []];

$FunctionList['administratethemes'] = ['explain' =>'Allow users to maintain themes'];
$FunctionList['personaltheme'] = ['explain' =>'Allow operators have their own personal back office theme'];
$FunctionList['use_operator'] = ['explain' =>'Allow operator to preview trigger'];
