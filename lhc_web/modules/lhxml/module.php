<?php

$Module = array( "name" => "Live helper Chat XML service");

$ViewList = array();

$ViewList['checklogin'] = array(
    'script' => 'checklogin.php',
    'params' => array()
);

$ViewList['closedchats'] = array(
    'script' => 'closedchats.php',
    'params' => array()
);

$ViewList['lists'] = array(
    'script' => 'lists.php',
    'params' => array()
);

$ViewList['getuseronlinestatus'] = array(
    'script' => 'getuseronlinestatus.php',
    'params' => array()
);

$ViewList['setonlinestatus'] = array(
    'params' => array('status')
);

$ViewList['deletechat'] = array(
    'params' => array('chat_id')
);

$ViewList['chatdata'] = array(
    'params' => array('chat_id')
);

$ViewList['cannedresponses'] = array(
    'params' => array('chat_id')
);

$ViewList['chatssynchro'] = array(
    'params' => array()
);

$ViewList['closechat'] = array(
    'params' => array('chat_id')
);

$ViewList['addmsgadmin'] = array(
    'params' => array('chat_id')
);

$ViewList['transferchat'] = array(
    'params' => array('chat_id')
);

$ViewList['transferuser'] = array(
    'params' => array('chat_id','user_id')
);

$ViewList['accepttransfer'] = array(
    'params' => array('transfer_id')
);

$ViewList['accepttransferbychat'] = array(
    'params' => array('chat_id')
);

$ViewList['sendnotice'] = array(
		'params' => array('online_id')
);

$FunctionList = array();

?>