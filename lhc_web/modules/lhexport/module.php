<?php

$Module = array( "name" => "Chats export module");

$ViewList = array();

$ViewList['getchat'] = array(
    'params' => array('hash','chat_id')
);

$ViewList['getcount'] = array(
    'script' => 'getcount.php',
    'params' => array('hash'),
	'multiple_arguments' => array('status'),
    'uparams' => array('format','status')
);

$ViewList['getlist'] = array(
    'params' => array('hash'),
    'uparams' => array('limit','format'),
    'functions' => array( 'selfedit' )
);


$FunctionList = array();

?>