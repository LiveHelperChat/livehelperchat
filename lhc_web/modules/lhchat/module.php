<?php

$Module = array( "name" => "Chat",
				 'variable_params' => true );

$ViewList = array();

$ViewList['adminchat'] = array(
    'params' => array('chat_id'),
    'uparams' => array('remember'),
    'functions' => array( 'use' )
);

$ViewList['redirectcontact'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['changestatus'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'canchangechatstatus' )
);

$ViewList['editprevious'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['updatemsg'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
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

$ViewList['modifychat'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'modifychat' )
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
    'uparams' => array('departmentd','operatord','actived','pendingd','closedd','unreadd','limita','limitp','limitc','limitu','limito','limitd'),
    'ajax' => true,
    'functions' => array( 'use' ),
    'multiple_arguments' => array ( 'operatord', 'actived', 'closedd' , 'pendingd', 'unreadd','departmentd')
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
    'uparams' => array('nick','email','timefrom','timeto','department_id','user_id','print','xls','fbst'),
    'functions' => array( 'use' )
);

$ViewList['closedchats'] = array(
    'params' => array(),
    'uparams' => array('nick','email','timefrom','timeto','department_id','user_id','print','xls','fbst'),
    'functions' => array( 'use' )
);

$ViewList['operatorschats'] = array(
    'params' => array(),
	'uparams' => array('nick','email','timefrom','timeto','department_id','user_id','print','xls','fbst'),
    'functions' => array( 'use' )
);

$ViewList['unreadchats'] = array(
    'script' => 'unreadchats.php',
    'params' => array(),
	'uparams' => array('nick','email','timefrom','timeto','department_id','user_id','print','xls','fbst'),
    'functions' => array( 'use' )
);

$ViewList['pendingchats'] = array(
    'params' => array(),
    'uparams' => array('nick','email','timefrom','timeto','department_id','user_id','print','xls','fbst'),
    'functions' => array( 'use' )
    );

$ViewList['addmsgadmin'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['updatechatstatus'] = array(
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

$ViewList['addonlineoperationiframe'] = array(
    'params' => array('online_user_id'),
    'functions' => array( 'use' )
);

$ViewList['saveremarks'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
    );

$ViewList['saveonlinenotes'] = array(
    'params' => array('online_user_id'),
    'functions' => array( 'use' )
);

/* Anonymous functions */
$ViewList['addmsguser'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('mode'),
);

$ViewList['editprevioususer'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array(),
);

$ViewList['updatemsguser'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('mode'),
);

$ViewList['getmessage'] = array(
    'params' => array('chat_id','hash','msgid'),
    'uparams' => array('mode'),
);

$ViewList['getmessageadmin'] = array(
    'params' => array('chat_id','msgid'),
    'uparams' => array(),
	'functions' => array( 'use' )
);

$ViewList['voteaction'] = array(
    'params' => array('chat_id','hash','type'),
    'uparams' => array(),
);

$ViewList['syncuser'] = array(
    'params' => array('chat_id','message_id','hash'),
	'uparams' => array('mode','ot','theme','modeembed')
);

$ViewList['usertyping'] = array(
    'params' => array('chat_id','hash','status'),
	'uparams' => array()
);

$ViewList['checkchatstatus'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('mode','theme')
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
    'uparams' => array('ua','ma','operator','theme','noresponse','priority','disable_pro_active','click','position','hide_offline','check_operator_messages','top','units','leaveamessage','department','identifier'),
	'multiple_arguments' => array ( 'department', 'ua' )
);

$ViewList['chatcheckstatus'] = array(
    'params' => array(),
    'uparams' => array('status','department','vid'),
	'multiple_arguments' => array ( 'department' )
);

$ViewList['getstatusembed'] = array(
    'params' => array(),
    'uparams' => array('ua','operator','theme','hide_offline','leaveamessage','department','priority'),
	'multiple_arguments' => array ( 'department','ua' )
);

$ViewList['startchat'] = array (
    'params' => array(),
    'uparams' => array('ua','switchform','operator','theme','er','vid','hash_resume','sound','hash','offline','leaveamessage','department','priority','chatprefill'),
	'multiple_arguments' => array ( 'department','ua' )
);

$ViewList['chatwidget'] = array(
    'params' => array(),
    'uparams' => array('ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill'),
	'multiple_arguments' => array ( 'department', 'ua' )
);

$ViewList['reopen'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('mode','embedmode','theme'),
);

$ViewList['readoperatormessage'] = array(
    'params' => array(),
    'uparams' => array('operator','theme','priority','vid','department','playsound','ua'),
	'multiple_arguments' => array ( 'department', 'ua' )
);

$ViewList['chatcheckoperatormessage'] = array(
    'params' => array(),
    'uparams' => array('tz','operator','theme','priority','vid','count_page','identifier','department','ua'),
	'multiple_arguments' => array ( 'department','ua' )
);

$ViewList['logpageview'] = array(
    'params' => array(),
    'uparams' => array('tz','vid','identifier','department','ua'),
	'multiple_arguments' => array ( 'department','ua' )
);

$ViewList['chatwidgetclosed'] = array(
    'params' => array(),
    'uparams' => array('vid','hash'),
);

$ViewList['chat'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('theme','er')
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
	'uparams' => array('sound','mode','theme','cstarted')
);

$ViewList['userclosechat'] = array(
    'params' => array('chat_id','hash')
);

$ViewList['onlineusers'] = array(
    'params' => array(),
    'ajax' => true,
    'uparams' => array('clear_list','method','deletevisitor','timeout','csfr','department','maxrows'),
    'functions' => array( 'use_onlineusers' )
);

$ViewList['jsononlineusers'] = array(
    'params' => array(),
    'uparams' => array('department','maxrows'),
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
    'functions' => array( 'administrategeoconfig' )
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

$ViewList['maintenance'] = array(
    'params' => array(),
    'uparams' => array('csfr','action'),
    'functions' => array( 'maintenance' )
);

$ViewList['newcannedmsg'] = array(
    'params' => array(),
    'functions' => array( 'administratecannedmsg' )
);

$ViewList['cannedmsgedit'] = array(
    'params' => array('id'),
    'functions' => array( 'administratecannedmsg' )
);

$ViewList['geoadjustment'] = array(
    'params' => array(),
    'functions' => array( 'geoadjustment' )
);

$ViewList['accept'] = array(
    'params' => array('hash','validation_hash','email')
);

$ViewList['sendchat'] = array(
		'params' => array('chat_id','hash')
);

$FunctionList['use'] = array('explain' => 'General permission to use chat module');
$FunctionList['singlechatwindow'] = array('explain' =>'Allow operator to use single chat window functionality');
$FunctionList['allowchattabs'] = array('explain' =>'Allow operator to user chat rooms functionality');
$FunctionList['deletechat'] = array('explain' =>'Allow operator to delete his own chats');
$FunctionList['deleteglobalchat'] = array('explain' =>'Allow to delete all chats');
$FunctionList['allowtransfer'] = array('explain' =>'Allow user to transfer chat to another user');
$FunctionList['allowcloseremote'] = array('explain' =>'Allow operator to close another operator chat');
$FunctionList['allowblockusers'] = array('explain' =>'Allow operator to block visitors');
$FunctionList['administrateconfig'] = array('explain' =>'Allow to change chat config');
$FunctionList['allowclearonlinelist'] = array('explain' =>'Allow operator to clean online users list');
$FunctionList['administratecannedmsg'] = array('explain' =>'Allow operator change canned messages');
$FunctionList['allowopenremotechat'] = array('explain' =>'Allow operator to open other operators chats from same department');
$FunctionList['allowreopenremote'] = array('explain' =>'Allow operator to reopen other operators chats');
$FunctionList['allowtransfertoanyuser'] = array('explain' =>'Allow operator to transfer chat to any online operator, not only his own department users');
$FunctionList['use_onlineusers'] = array('explain' =>'Allow operator to view online visitors');
$FunctionList['chattabschrome'] = array('explain' =>'Allow operator to use chrome extension');
$FunctionList['canchangechatstatus'] = array('explain' =>'Allow operator to change chat status');
$FunctionList['administrateinvitations'] = array('explain' =>'Allow operator to change pro active invitations');
$FunctionList['administrateresponder'] = array('explain' =>'Allow operator to change auto responder');
$FunctionList['maintenance'] = array('explain' =>'Allow operator to run maintenance');
$FunctionList['sees_all_online_visitors'] = array('explain' =>'Operator can see all online visitors, not only his department');
$FunctionList['geoadjustment'] = array('explain' => 'Allow operator to edit geo adjustment for chat status');
$FunctionList['take_screenshot'] = array('explain' => 'Allow operator to take visitor browser page screenshots');
$FunctionList['modifychat'] = array('explain' => 'Allow operator modify main chat information');
$FunctionList['allowredirect'] = array('explain' => 'Allow operator to redirect user to another page');
$FunctionList['administrategeoconfig'] = array('explain' => 'Allow operator to edit geo detection configuration');


?>