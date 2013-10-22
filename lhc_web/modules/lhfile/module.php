<?php

$Module = array( "name" => "Files module");

$ViewList = array();

$ViewList['configuration'] = array(
    'script' => 'configuration.php',
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['uploadfile'] = array(
		'script' => 'uploadfile.php',
		'params' => array('chat_id','hash'),
		'uparams' => array(),
);

$ViewList['downloadfile'] = array(
		'script' => 'downloadfile.php',
		'params' => array('file_id','hash'),
		'uparams' => array(),
);

$ViewList['uploadfileadmin'] = array(
		'script' => 'uploadfileadmin.php',
		'params' => array('chat_id'),
		'uparams' => array(),
);

$FunctionList['use'] = array('explain' => 'Allow user to configure files upload');


?>