<?php

$Module = array( "name" => "Chat",
				 'variable_params' => true );

$ViewList = array();

$ViewList['adminchat'] = array(
    'params' => array('chat_id'),
    'uparams' => array('remember'),
    'functions' => array( 'use' )
);

$ViewList['printchatadmin'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['previewchat'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['closechatadmin'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['reopenchat'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['adminleftchat'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['notificationsettings'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['startchatwithoperator'] = array(
    'params' => array('user_id'),
    'functions' => array( 'use' )
);

$ViewList['closechat'] = array(
    'params' => array('chat_id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use' )
);

$ViewList['sendmail'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['transferchat'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'allowtransfer' )
);

$ViewList['accepttransfer'] = array(
    'params' => array('transfer_id'),
    'uparams' => array('postaction'),
    'functions' => array( 'use' )
);

$ViewList['deletechatadmin'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'deletechat' )
    );

$ViewList['delete'] = array(
    'params' => array('chat_id'),
    'uparams' => array('csfr'),
    'functions' => array( 'deletechat' )
    );

$ViewList['syncadmininterface'] = array(
    'params' => array(),
    'functions' => array( 'use' )
    );

$ViewList['lists'] = array(
    'params' => array(),
    'functions' => array( 'use' )
    );

$ViewList['chattabs'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'allowchattabs' )
    );

$ViewList['chattabschrome'] = array(
    'params' => array(),
    'uparams' => array('mode'),
    'functions' => array( )
);

$ViewList['single'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'singlechatwindow' )
);

$ViewList['chatfootprint'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['refreshonlineinfo'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['checkscreenshot'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['checkscreenshotonline'] = array(
    'params' => array('online_id'),
    'functions' => array( 'use' )
);

$ViewList['operatortyping'] = array(
    'params' => array('chat_id','status'),
    'functions' => array( 'use' )
);

$ViewList['syncadmin'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['activechats'] = array(
    'params' => array(),
    'uparams' => array('nick','email','timefrom','timeto','department_id','user_id'),
    'functions' => array( 'use' )
);

$ViewList['closedchats'] = array(
    'params' => array(),
    'uparams' => array('nick','email','timefrom','timeto','department_id','user_id'),
    'functions' => array( 'use' )
);

$ViewList['operatorschats'] = array(
    'params' => array(),
	'uparams' => array('nick','email','timefrom','timeto','department_id','user_id'),
    'functions' => array( 'use' )
);

$ViewList['unreadchats'] = array(
    'script' => 'unreadchats.php',
    'params' => array(),
	'uparams' => array('nick','email','timefrom','timeto','department_id','user_id'),
    'functions' => array( 'use' )
);

$ViewList['pendingchats'] = array(
    'params' => array(),
    'uparams' => array('nick','email','timefrom','timeto','department_id','user_id'),
    'functions' => array( 'use' )
    );

$ViewList['addmsgadmin'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['addoperation'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['addonlineoperation'] = array(
    'params' => array('online_user_id'),
    'functions' => array( 'use' )
);

$ViewList['saveremarks'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
    );

/* Anonymous functions */
$ViewList['addmsguser'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('mode'),
);

$ViewList['voteaction'] = array(
    'params' => array('chat_id','hash','type'),
    'uparams' => array(),
);

$ViewList['syncuser'] = array(
    'params' => array('chat_id','message_id','hash'),
	'uparams' => array('mode','ot')
);

$ViewList['usertyping'] = array(
    'params' => array('chat_id','hash','status'),
	'uparams' => array()
);

$ViewList['checkchatstatus'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('mode')
);

$ViewList['transferuser'] = array(
    'params' => array('chat_id','item_id'),
    'functions' => array( 'allowtransfer' )
    );

$ViewList['blockuser'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'allowblockusers' )
);

$ViewList['blockedusers'] = array(
    'params' => array(),
    'uparams' => array('remove_block','csfr'),
    'functions' => array( 'allowblockusers' )
);

$ViewList['getstatus'] = array(
    'params' => array(),
    'uparams' => array('priority','disable_pro_active','click','position','hide_offline','check_operator_messages','top','units','leaveamessage','department','identifier'),
    );

$ViewList['getstatusembed'] = array(
    'params' => array(),
    'uparams' => array('hide_offline','leaveamessage','department','priority'),
);

$ViewList['startchat'] = array (
    'params' => array(),
    'uparams' => array('vid','hash_resume','sound','hash','offline','leaveamessage','department','priority','chatprefill')
);

$ViewList['chatwidget'] = array(
    'params' => array(),
    'uparams' => array('vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill'),
);

$ViewList['reopen'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('mode','embedmode'),
);

$ViewList['readoperatormessage'] = array(
    'params' => array(),
    'uparams' => array('priority','vid','department','playsound')
);

$ViewList['chatcheckoperatormessage'] = array(
    'params' => array(),
    'uparams' => array('priority','vid','count_page','identifier','department')
);

$ViewList['chatwidgetclosed'] = array(
    'params' => array(),
    'uparams' => array('vid','hash'),
);

$ViewList['chat'] = array(
    'params' => array('chat_id','hash')
);

$ViewList['printchat'] = array(
    'params' => array('chat_id','hash')
);

$ViewList['printchat'] = array(
    'params' => array('chat_id','hash')
);

$ViewList['bbcodeinsert'] = array(
		'params' => array()
);

$ViewList['chatwidgetchat'] = array(
    'params' => array('chat_id','hash'),
	'uparams' => array('sound','mode')
);

$ViewList['userclosechat'] = array(
    'params' => array('chat_id','hash')
);

$ViewList['onlineusers'] = array(
    'params' => array(),
    'uparams' => array('clear_list','method','deletevisitor','timeout','csfr','department'),
    'functions' => array( 'use_onlineusers' )
);

$ViewList['jsononlineusers'] = array(
    'params' => array(),
    'uparams' => array('department'),
    'functions' => array( 'use_onlineusers' )
);

$ViewList['getonlineuserinfo'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use_onlineusers' )
);

$ViewList['sendnotice'] = array(
    'params' => array('online_id'),
    'functions' => array( 'use' )
);

$ViewList['geoconfiguration'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'administrateconfig' )
);

$ViewList['listchatconfig'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['editchatconfig'] = array(
    'params' => array('config_id'),
    'functions' => array( 'administrateconfig' )
);

$ViewList['syncandsoundesetting'] = array(
    'params' => array(),
    'functions' => array( 'administrateconfig' )
);

$ViewList['startchatformsettings'] = array(
    'params' => array(),
    'functions' => array( 'administrateconfig' )
);

$ViewList['cannedmsg'] = array(
    'params' => array(),
    'uparams' => array('action','id','csfr'),
    'functions' => array( 'administratecannedmsg' )
);

$ViewList['newcannedmsg'] = array(
    'params' => array(),
    'functions' => array( 'administratecannedmsg' )
);

$ViewList['cannedmsgedit'] = array(
    'params' => array('id'),
    'functions' => array( 'administratecannedmsg' )
);

$ViewList['statistic'] = array(
    'params' => array(),
    'functions' => array( 'viewstatistic' )
);

$ViewList['accept'] = array(
    'params' => array('hash','validation_hash','email')
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
$FunctionList['chattabschrome'] = array('explain' =>'Allow user to user chrome extension');


?>