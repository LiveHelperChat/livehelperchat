<?php

$Module = ["name" => "XMPP module configuration"];

$ViewList = [];

$ViewList['configuration'] = [
    'params' => [],
    'uparams' => ['gtalkoauth'],
	'functions' => ['configurexmp']
];

$FunctionList = [];
$FunctionList['configurexmp'] = ['explain' => 'Allow user to configure XMPP'];
