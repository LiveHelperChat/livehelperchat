<?php

$Module = ['name' => 'Questionary/Voting'];

$ViewList = [
    'newquestion' => [
        'script' => 'newquestion.php',
        'params' => [],
        'functions' => ['manage_questionary'],
    ],
    'list' => [
        'script' => 'list.php',
        'params' => [],
        'functions' => ['manage_questionary'],
    ],
    'delete' => [
        'script' => 'delete.php',
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['manage_questionary'],
    ],
    'deleteanswer' => [
        'script' => 'deleteanswer.php',
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['manage_questionary'],
    ],
    'deleteoption' => [
        'script' => 'deleteoption.php',
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['manage_questionary'],
    ],
    'htmlcode' => [
        'script' => 'htmlcode.php',
        'params' => [],
        'functions' => ['manage_questionary'],
    ],
    'getstatus' => [
        'script' => 'getstatus.php',
        'params' => [],
        'functions' => [],
        'uparams' => ['theme', 'noresponse', 'position', 'expand', 'top', 'units', 'width', 'height'],
    ],
    'votingwidget' => [
        'script' => 'votingwidget.php',
        'params' => [],
        'uparams' => ['theme', 'mode'],
        'functions' => [],
    ],
    'votingwidgetclosed' => [
        'script' => 'votingwidgetclosed.php',
        'params' => [],
        'functions' => [],
    ],
    'previewanswer' => [
        'script' => 'previewanswer.php',
        'params' => ['id'],
        'functions' => ['manage_questionary'],
    ],
    'edit' => [
        'script' => 'edit.php',
        'params' => ['id'],
        'uparams' => ['tab', 'option_id'],
        'functions' => ['manage_questionary'],
    ],
    'embed' => [
        'script' => 'embed.php',
        'params' => [],
        'uparams' => ['theme'],
        'functions' => [],
    ],
    'embedcode' => [
        'script' => 'embedcode.php',
        'params' => [],
        'functions' => ['manage_questionary'],
    ],
];

$FunctionList = [
    'manage_questionary' => ['explain' => 'Allow user to manage questionary'],
];
