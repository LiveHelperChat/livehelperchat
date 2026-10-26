<?php

$Module = array( "name" => "Chats export module");

$ViewList = array();

$ViewList['getchat'] = array(
    'params' => array('hash','chat_id'),
	'uparams' => array('format'),
    'functions' => array(),
);

$ViewList['getcount'] = array(
    'params' => array('hash'),
    'functions' => array(),
	'multiple_arguments' => array('status'),
    'uparams' => array('format','status')
);

$ViewList['getlist'] = array(
    'params' => array('hash'),
    'uparams' => array('limit','format','status'),
    'functions' => array(),
	'multiple_arguments' => array('status')
);

$FunctionList = array();

?>