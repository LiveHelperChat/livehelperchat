<?php

$Module = array( "name" => "Chat",
				 'variable_params' => true );

$ViewList = array();

$ViewList['adminchat'] = array(
    'script' => 'adminchat.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
    );

$ViewList['previewchat'] = array(
    'script' => 'previewchat.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
    );

$ViewList['closechatadmin'] = array(
    'script' => 'closechatadmin.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['notificationsettings'] = array(
    'script' => 'notificationsettings.php',
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['closechat'] = array(
    'script' => 'closechat.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['sendmail'] = array(
    'script' => 'sendmail.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['transferchat'] = array(
    'script' => 'transferchat.php',
    'params' => array('chat_id'),
    'functions' => array( 'allowtransfer' )
);

$ViewList['accepttransfer'] = array(
    'script' => 'accepttransfer.php',
    'params' => array('transfer_id'),
    'functions' => array( 'use' )
);

$ViewList['deletechatadmin'] = array(
    'script' => 'deletechatadmin.php',
    'params' => array('chat_id'),
    'functions' => array( 'deletechat' )
    );

$ViewList['delete'] = array(
    'script' => 'delete.php',
    'params' => array('chat_id'),
    'functions' => array( 'deletechat' )
    );

$ViewList['syncadmininterface'] = array(
    'script' => 'syncadmininterface.php',
    'params' => array(),
    'functions' => array( 'use' )
    );

$ViewList['lists'] = array(
    'script' => 'lists.php',
    'params' => array(),
    'functions' => array( 'use' )
    );

$ViewList['chattabs'] = array(
    'script' => 'chattabs.php',
    'params' => array('chat_id'),
    'functions' => array( 'allowchattabs' )
    );

$ViewList['single'] = array(
    'script' => 'single.php',
    'params' => array('chat_id'),
    'functions' => array( 'singlechatwindow' )
);

$ViewList['operatortyping'] = array(
    'script' => 'operatortyping.php',
    'params' => array('chat_id','status'),
    'functions' => array( 'use' )
);

$ViewList['syncadmin'] = array(
    'script' => 'syncadmin.php',
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['activechats'] = array(
    'script' => 'activechats.php',
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['closedchats'] = array(
    'script' => 'closedchats.php',
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['unreadchats'] = array(
    'script' => 'unreadchats.php',
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['pendingchats'] = array(
    'script' => 'pendingchats.php',
    'params' => array(),
    'functions' => array( 'use' )
    );

$ViewList['addmsgadmin'] = array(
    'script' => 'addmsgadmin.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
    );

/* Anonymous functions */
$ViewList['addmsguser'] = array(
    'script' => 'addmsguser.php',
    'params' => array('chat_id','hash'),
    'uparams' => array('mode'),
);

$ViewList['syncuser'] = array(
    'script' => 'syncuser.php',
    'params' => array('chat_id','message_id','hash'),
	'uparams' => array('mode')
);

$ViewList['usertyping'] = array(
    'script' => 'usertyping.php',
    'params' => array('chat_id','hash','status'),
	'uparams' => array()
);

$ViewList['checkchatstatus'] = array(
    'script' => 'checkchatstatus.php',
    'params' => array('chat_id','hash')
    );

$ViewList['transferuser'] = array(
    'script' => 'transferuser.php',
    'params' => array('chat_id','item_id'),
    'functions' => array( 'allowtransfer' )
    );

$ViewList['blockuser'] = array(
    'script' => 'blockuser.php',
    'params' => array('chat_id'),
    'functions' => array( 'allowblockusers' )
);

$ViewList['blockedusers'] = array(
    'script' => 'blockedusers.php',
    'params' => array(),
    'uparams' => array('remove_block'),
    'functions' => array( 'allowblockusers' )
);

$ViewList['getstatus'] = array(
    'script' => 'getstatus.php',
    'params' => array(),
    'uparams' => array('click','position','hide_offline','check_operator_messages','top','units','leaveamessage','department'),
    );

$ViewList['getstatusembed'] = array(
    'script' => 'getstatusembed.php',
    'params' => array(),
    'uparams' => array('hide_offline','leaveamessage','department'),
);

$ViewList['startchat'] = array (
    'script' => 'startchat.php',
    'params' => array(),
    'uparams' => array('offline','leaveamessage','department')
);

$ViewList['chatwidget'] = array(
    'script' => 'chatwidget.php',
    'params' => array(),
    'uparams' => array('mode','offline','leaveamessage','department'),
);

$ViewList['readoperatormessage'] = array(
    'script' => 'readoperatormessage.php',
    'params' => array()
);

$ViewList['chatcheckoperatormessage'] = array(
    'script' => 'chatcheckoperatormessage.php',
    'params' => array()
);

$ViewList['chatwidgetclosed'] = array(
    'script' => 'chatwidgetclosed.php',
    'params' => array()
);

$ViewList['chat'] = array(
    'script' => 'chat.php',
    'params' => array('chat_id','hash')
);

$ViewList['chatwidgetchat'] = array(
    'script' => 'chatwidgetchat.php',
    'params' => array('chat_id','hash'),
	'uparams' => array('mode')
);

$ViewList['userclosechat'] = array(
    'script' => 'userclosechat.php',
    'params' => array('chat_id','hash')
);

$ViewList['onlineusers'] = array(
    'script' => 'onlineusers.php',
    'params' => array(),
    'uparams' => array('clear_list','method'),
    'functions' => array( 'use' )
);

$ViewList['sendnotice'] = array(
    'script' => 'sendnotice.php',
    'params' => array('online_id'),
    'functions' => array( 'use' )
);

$ViewList['geoconfiguration'] = array(
    'script' => 'geoconfiguration.php',
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'administrateconfig' )
);

$ViewList['listchatconfig'] = array(
    'script' => 'listchatconfig.php',
    'params' => array(),
    'functions' => array( 'administrateconfig' )
);

$ViewList['editchatconfig'] = array(
    'script' => 'editchatconfig.php',
    'params' => array('config_id'),
    'functions' => array( 'administrateconfig' )
);

$ViewList['syncandsoundesetting'] = array(
    'script' => 'syncandsoundesetting.php',
    'params' => array(),
    'functions' => array( 'administrateconfig' )
);

$ViewList['startchatformsettings'] = array(
    'script' => 'startchatformsettings.php',
    'params' => array(),
    'functions' => array( 'administrateconfig' )
);

$ViewList['cannedmsg'] = array(
    'script' => 'cannedmsg.php',
    'params' => array(),
    'uparams' => array('action','id'),
    'functions' => array( 'administratecannedmsg' )
);

$ViewList['newcannedmsg'] = array(
    'script' => 'newcannedmsg.php',
    'params' => array(),
    'functions' => array( 'administratecannedmsg' )
);

$ViewList['cannedmsgedit'] = array(
    'script' => 'cannedmsgedit.php',
    'params' => array('id'),
    'functions' => array( 'administratecannedmsg' )
);

$FunctionList['use'] = array('explain' => 'General permission to use chat module');
$FunctionList['singlechatwindow'] = array('explain' =>'Allow user to use single chat window functionality');
$FunctionList['allowchattabs'] = array('explain' =>'Allow user to user chat rooms functionality');
$FunctionList['deletechat'] = array('explain' =>'Allow user to delete his own chats');
$FunctionList['deleteglobalchat'] = array('explain' =>'Allow to delete all chats');
$FunctionList['allowtransfer'] = array('explain' =>'Allow user to transfer chat to another user');
$FunctionList['allowcloseremote'] = array('explain' =>'Allow user to close another user chat');
$FunctionList['allowblockusers'] = array('explain' =>'Allow user to block visitors');
$FunctionList['administrateconfig'] = array('explain' =>'Allow to change chat config');
$FunctionList['allowclearonlinelist'] = array('explain' =>'Allow user to clean online users list');
$FunctionList['administratecannedmsg'] = array('explain' =>'Allow user change canned messages');
$FunctionList['allowopenremotechat'] = array('explain' =>'Allow user to open other users chats from same department');
$FunctionList['allowtransfertoanyuser'] = array('explain' =>'Allow user to transfer chat to any online user, not only his own department users');


?>