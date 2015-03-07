<?php

$Module = array( "name" => "Translation module");

$ViewList = array();

$ViewList['configuration'] = array(
		'params' => array(),
		'functions' => array('configuration'),
		'uparams' => array()
);

$ViewList['starttranslation'] = array(
		'params' => array('chat_id','visitor_language','operator_language'),
		'functions' => array('use'),
		'uparams' => array()
);

$FunctionList = array();
$FunctionList['configuration'] = array('explain' => 'Allow operator to configure automatic translations module');
$FunctionList['use'] = array('explain' => 'Allow operator to use automatic translations');

?>