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

$ViewList['uploadfileonline'] = array(
		'params' => array('vid'),
		'uparams' => array(),
);

$ViewList['chatfileslist'] = array(
		'params' => array('chat_id'),
		'uparams' => array(),
		'functions' => array( 'use_operator' )
);

$ViewList['onlinefileslist'] = array(
		'params' => array('online_user_id'),
		'uparams' => array(),
		'functions' => array( 'use_operator' )
);

$ViewList['useronlinefileslist'] = array(
		'params' => array('vid'),
		'uparams' => array(),
		'functions' => array( )
);

$ViewList['downloadfile'] = array(
		'params' => array('file_id','hash'),
		'uparams' => array('inline'),
);

$ViewList['uploadfileadmin'] = array(
		'params' => array('chat_id'),
		'uparams' => array(),
		'functions' => array( 'use_operator' )
);

$ViewList['uploadfileadminonlineuser'] = array(
		'params' => array('online_user_id'),
		'uparams' => array(),
		'functions' => array( 'use_operator' )
);

$ViewList['new'] = array(
		'params' => array(),
		'uparams' => array('mode'),
		'functions' => array( 'use_operator' )
);

$ViewList['attatchfile'] = array(
		'params' => array('chat_id'),
		'uparams' => array('user_id'),
		'functions' => array( 'use_operator' )
);

$ViewList['attatchfilemail'] = array(
		'params' => array(),
		'uparams' => array('mode','user_id'),
		'functions' => array( 'use_operator' )
);

$ViewList['list'] = array(
		'params' => array(),
		'uparams' => array('user_id'),
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

$ViewList['storescreenshot'] = array(
		'params' => array(),
		'uparams' => array('vid','hash','hash_resume'),
);

$FunctionList['use'] = array('explain' => 'Allow user to configure files upload');
$FunctionList['use_operator'] = array('explain' => 'Allow operators to upload files');
$FunctionList['file_list'] = array('explain' => 'Allow operators to list all uploaded files');
$FunctionList['file_delete'] = array('explain' => 'Allow operators to delete all files');
$FunctionList['file_delete_chat'] = array('explain' => 'Allow operators to delete his chat files');


?>