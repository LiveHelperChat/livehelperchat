<?php

$Module = array( "name" => "Speech module");

$ViewList = array();

$ViewList['setchatspeechlanguage'] = array(
		'params' => array('chat_id'),
		'functions' => array('change_chat_recognition'),
		'uparams' => array()
);

$ViewList['getchatdialect'] = array(
		'params' => array('chat_id'),
		'functions' => array('use'),
		'uparams' => array()
);

$ViewList['getdialect'] = array(
		'params' => array('language'),
		'functions' => array('use'),
		'uparams' => array()
);

$ViewList['defaultlanguage'] = array(
		'params' => array(),
		'functions' => array('manage'),
		'uparams' => array()
);

$FunctionList = array();
$FunctionList['use'] = array('explain' => 'Allow operator to use speech recognition module');
$FunctionList['manage'] = array('explain' => 'Allow user to set application default speech recognition language');
$FunctionList['changedefaultlanguage'] = array('explain' => 'Allow user to change his personal speech recognition language');
$FunctionList['change_chat_recognition'] = array('explain' => 'Allow operator to change chat recognition language');

?>