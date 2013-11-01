<?php

$Module = array( "name" => "Chat",
				 'variable_params' => true );

$ViewList = array();

$ViewList['adminchat'] = array(
    'script' => 'adminchat.php',
    'params' => array('chat_id'),
    'uparams' => array('remember'),
    'functions' => array( 'use' )
);

$ViewList['printchatadmin'] = array(
    'script' => 'printchatadmin.php',
    'params' => array('chat_id'),
    'uparams' => array(),
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

$ViewList['reopenchat'] = array(
    'script' => 'reopenchat.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['adminleftchat'] = array(
    'script' => 'adminleftchat.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['notificationsettings'] = array(
    'script' => 'notificationsettings.php',
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['startchatwithoperator'] = array(
    'script' => 'startchatwithoperator.php',
    'params' => array('user_id'),
    'functions' => array( 'use' )
);

$ViewList['closechat'] = array(
    'script' => 'closechat.php',
    'params' => array('chat_id'),
    'uparams' => array('csfr'),
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
    'uparams' => array('postaction'),
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
    'uparams' => array('csfr'),
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

$ViewList['chatfootprint'] = array(
    'script' => 'chatfootprint.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['refreshonlineinfo'] = array(
    'script' => 'refreshonlineinfo.php',
    'params' => array('chat_id'),
    'functions' => array( 'use' )
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

$ViewList['operatorschats'] = array(
    'script' => 'operatorschats.php',
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
    'uparams' => array('remove_block','csfr'),
    'functions' => array( 'allowblockusers' )
);

$ViewList['getstatus'] = array(
    'script' => 'getstatus.php',
    'params' => array(),
    'uparams' => array('priority','disable_pro_active','click','position','hide_offline','check_operator_messages','top','units','leaveamessage','department','identifier'),
    );

$ViewList['getstatusembed'] = array(
    'script' => 'getstatusembed.php',
    'params' => array(),
    'uparams' => array('hide_offline','leaveamessage','department','priority'),
);

$ViewList['startchat'] = array (
    'script' => 'startchat.php',
    'params' => array(),
    'uparams' => array('vid','hash_resume','sound','hash','offline','leaveamessage','department','priority')
);

$ViewList['chatwidget'] = array(
    'script' => 'chatwidget.php',
    'params' => array(),
    'uparams' => array('vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority'),
);

$ViewList['reopen'] = array(
    'script' => 'reopen.php',
    'params' => array('chat_id','hash'),
    'uparams' => array('mode','embedmode'),
);

$ViewList['readoperatormessage'] = array(
    'script' => 'readoperatormessage.php',
    'params' => array(),
    'uparams' => array('priority','vid','department','playsound')
);

$ViewList['chatcheckoperatormessage'] = array(
    'script' => 'chatcheckoperatormessage.php',
    'params' => array(),
    'uparams' => array('priority','vid','count_page','identifier','department')
);

$ViewList['chatwidgetclosed'] = array(
    'script' => 'chatwidgetclosed.php',
    'params' => array(),
    'uparams' => array('vid','hash'),
);

$ViewList['chat'] = array(
    'script' => 'chat.php',
    'params' => array('chat_id','hash')
);

$ViewList['printchat'] = array(
    'script' => 'printchat.php',
    'params' => array('chat_id','hash')
);

$ViewList['sendchat'] = array(
		'script' => 'sendchat.php',
		'params' => array('chat_id','hash')
);

$ViewList['chatwidgetchat'] = array(
    'script' => 'chatwidgetchat.php',
    'params' => array('chat_id','hash'),
	'uparams' => array('sound','mode')
);

$ViewList['userclosechat'] = array(
    'script' => 'userclosechat.php',
    'params' => array('chat_id','hash')
);

$ViewList['onlineusers'] = array(
    'script' => 'onlineusers.php',
    'params' => array(),
    'uparams' => array('clear_list','method','deletevisitor','timeout','csfr'),
    'functions' => array( 'use_onlineusers' )
);

$ViewList['jsononlineusers'] = array(
    'script' => 'jsononlineusers.php',
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_onlineusers' )
);

$ViewList['getonlineuserinfo'] = array(
    'script' => 'getonlineuserinfo.php',
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use_onlineusers' )
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
    'uparams' => array('action','id','csfr'),
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

$ViewList['statistic'] = array(
    'script' => 'statistic.php',
    'params' => array(),
    'functions' => array( 'viewstatistic' )
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
$FunctionList['allowreopenremote'] = array('explain' =>'Allow user to reopen other users chats');
$FunctionList['allowtransfertoanyuser'] = array('explain' =>'Allow user to transfer chat to any online user, not only his own department users');
$FunctionList['viewstatistic'] = array('explain' =>'Allow user to view statistic');
$FunctionList['use_onlineusers'] = array('explain' =>'Allow user to view online visitors');


?>