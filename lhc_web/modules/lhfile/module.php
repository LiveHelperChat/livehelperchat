<?php

$Module = array( "name" => "Files module");

$ViewList = array();

$ViewList['configuration'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['uploadfile'] = array(
		'params' => array('chat_id','hash'),
		'uparams' => array(),
);

$ViewList['chatfileslist'] = array(
		'params' => array('chat_id'),
		'uparams' => array(),
		'functions' => array( 'use_operator' )
);

$ViewList['downloadfile'] = array(
		'params' => array('file_id','hash'),
		'uparams' => array(),
);

$ViewList['uploadfileadmin'] = array(
		'params' => array('chat_id'),
		'uparams' => array(),
		'functions' => array( 'use_operator' )
);

$ViewList['list'] = array(
		'params' => array(),
		'uparams' => array(),
		'functions' => array( 'file_list' )
);

$ViewList['delete'] = array(
		'params' => array('file_id'),
		'uparams' => array('csfr'),
		'functions' => array( 'file_delete' )
);

$ViewList['deletechatfile'] = array(
		'params' => array('file_id'),
		'uparams' => array('csfr'),
		'functions' => array( 'file_delete_chat' )
);

$FunctionList['use'] = array('explain' => 'Allow user to configure files upload');
$FunctionList['use_operator'] = array('explain' => 'Allow operators to upload files');
$FunctionList['file_list'] = array('explain' => 'Allow operators to list all uploaded files');
$FunctionList['file_delete'] = array('explain' => 'Allow operators to delete all files');
$FunctionList['file_delete_chat'] = array('explain' => 'Allow operators to delete his chat files');


?>