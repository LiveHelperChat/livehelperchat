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
    'script' => 'setonlinestatus.php',
    'params' => array('status')
);

$ViewList['deletechat'] = array(
    'script' => 'deletechat.php',
    'params' => array('chat_id')
);

$ViewList['chatdata'] = array(
    'script' => 'chatdata.php',
    'params' => array('chat_id')
);

$ViewList['chatssynchro'] = array(
    'script' => 'chatssynchro.php',
    'params' => array()
);

$ViewList['closechat'] = array(
    'script' => 'closechat.php',
    'params' => array('chat_id')
);

$ViewList['addmsgadmin'] = array(
    'script' => 'addmsgadmin.php',
    'params' => array('chat_id')
);

$ViewList['transferchat'] = array(
    'script' => 'transferchat.php',
    'params' => array('chat_id')
);

$ViewList['transferuser'] = array(
    'script' => 'transferuser.php',
    'params' => array('chat_id','user_id')
);

$ViewList['accepttransfer'] = array(
    'script' => 'accepttransfer.php',
    'params' => array('transfer_id')
);

$ViewList['accepttransferbychat'] = array(
    'script' => 'accepttransferbychat.php',
    'params' => array('chat_id')
);

$ViewList['sendnotice'] = array(
		'script' => 'sendnotice.php',
		'params' => array('online_id')
);

$ViewList['userinfo'] = array(
		'script' => 'userinfo.php',
		'params' => array('user_id')
);

$FunctionList = array();

?>