<?php

$Module = ['name' => 'XMPP module configuration'];

$ViewList = [
    'configuration' => [
        'params' => [],
        'uparams' => ['gtalkoauth'],
        'functions' => ['configurexmp'],
    ],
];

$FunctionList = [
    'configurexmp' => ['explain' => 'Allow user to configure XMPP'],
];
