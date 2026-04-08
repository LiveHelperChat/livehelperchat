<?php

$Module = ['name' => 'Translation module'];

$ViewList = [];

$ViewList['configuration'] = [
		'params' => [],
		'functions' => ['configuration'],
		'uparams' => ['csfr', 'action']
];

$ViewList['starttranslation'] = [
		'params' => ['chat_id', 'visitor_language', 'operator_language'],
		'functions' => ['use'],
		'uparams' => []
];

$ViewList['translateoperatormessage'] = [
		'params' => ['chat_id'],
		'functions' => ['use'],
		'uparams' => []
];

$ViewList['translatevisitormessage'] = [
		'params' => ['chat_id', 'msg_id'],
		'functions' => ['use'],
		'uparams' => []
];

$FunctionList = [];
$FunctionList['configuration'] = ['explain' => 'Allow operator to configure automatic translations module'];
$FunctionList['use'] = ['explain' => 'Allow operator to use automatic translations'];
