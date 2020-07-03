<?php

$Module = array( "name" => "Chat");

$ViewList = array();

$ViewList['adminchat'] = array(
    'params' => array('chat_id'),
    'uparams' => array('remember','arg'),
    'functions' => array( 'use' ),
    'multiple_arguments' => array('arg')
);

$ViewList['loadmaindata'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['loadpreviousmessages'] = array(
    'params' => array('chat_id','message_id'),
    'uparams' => array('initial'),
    'functions' => array( 'use' ),
    'multiple_arguments' => array()
);

$ViewList['subject'] = array(
    'params' => array('chat_id'),
    'uparams' => array('subject','status'),
    'functions' => array( 'setsubject' )
);

$ViewList['getnotificationsdata'] = array(
    'params' => array(),
    'uparams' => array('id'),
    'ajax' => true,
    'functions' => array( 'use' ),
    'multiple_arguments' => array ( 'id')
);

$ViewList['getcannedfiltered'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['holdaction'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'holduse' )
);

$ViewList['copymessages'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['updateattribute'] = array(
		'params' => array(),
		'uparams' => array('hash','hash_resume','vid'),
);

$ViewList['updatejsvars'] = array(
		'params' => array(),
		'uparams' => array('hash','hash_resume','vid'),
);

$ViewList['logevent'] = array(
		'params' => array(),
		'uparams' => array('hash','hash_resume','vid'),
);

$ViewList['setnewvid'] = array(
		'params' => array(),
		'uparams' => array(),
);

$ViewList['redirectcontact'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'redirectcontact' )
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

$ViewList['loadactivechats'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['previewchat'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['previewmessage'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['closechatadmin'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['setsubstatus'] = array(
    'params' => array('chat_id','substatus'),
    'functions' => array( 'use' )
);

$ViewList['reopenchat'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'use' )
);

$ViewList['notificationsettings'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['startchatwithoperator'] = array(
    'params' => array('user_id'),
    'uparams' => array('mode'),
    'functions' => array( 'use' )
);

$ViewList['closechat'] = array(
    'params' => array('chat_id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use' )
);

$ViewList['sendmail'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'sendmail' )
);

$ViewList['modifychat'] = array(
    'params' => array('chat_id'),
    'uparams' => array('pos'),
    'functions' => array( 'modifychat' )
);

$ViewList['transferchat'] = array(
    'params' => array('chat_id'),
    'functions' => array( 'allowtransfer' )
);

$ViewList['accepttransfer'] = array(
    'params' => array('transfer_id'),
    'uparams' => array('postaction','mode'),
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
    'uparams' => array('w','clcs','limitgc','limitb','botd','odpgroups','ddgroups','udgroups','mdgroups', 'cdgroups', 'pdgroups','adgroups','pugroups','augroups','onop', 'acs', 'mcd', 'limitmc', 'mcdprod','activeu','pendingu','topen','departmentd','operatord','actived','pendingd','closedd','unreadd','limita','limitp','limitc','limitu','limito','limitd','activedprod','unreaddprod','pendingdprod','closeddprod','psort'),
    'ajax' => true,
    'functions' => array( 'use' ),
    'multiple_arguments' => array ('w','odpgroups','ddgroups','udgroups','mdgroups', 'cdgroups', 'pdgroups', 'adgroups', 'pugroups','augroups','mcd','operatord','mcdprod', 'activeu', 'pendingu', 'actived', 'closedd' , 'pendingd', 'unreadd','departmentd','activedprod','unreaddprod','pendingdprod','closeddprod')
);

$ViewList['loadinitialdata'] = array(
    'params' => array(),
    'uparams' => array('chatopen','chatgopen'),
    'ajax' => true,
    'functions' => array( 'use' ),
    'multiple_arguments' => array('chatopen','chatgopen')
);

$ViewList['list'] = array(
    'params' => array(),
    'uparams' => array('with_bot','no_operator','has_operator','without_bot','bot_ids','ip','department_ids','department_group_ids','user_ids','group_ids','subject_id','anonymized','una','chat_duration_from','chat_duration_till','wait_time_from','wait_time_till','chat_id','nick','email','timefrom','timeto','department_id','user_id','print','xls','fbst','chat_status','hum','product_id','timefrom','timefrom_minutes','timefrom_hours','timeto', 'timeto_minutes', 'timeto_hours', 'department_group_id', 'group_id', 'invitation_id'),
    'functions' => array( 'use' ),
    'multiple_arguments' => array(
        'department_ids',
        'department_group_ids',
        'user_ids',
        'group_ids',
        'bot_ids',
    )
);

$ViewList['dashboardwidgets'] = array(
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

$ViewList['refreshcustomfields'] = array(
    'params' => array(),
    'uparams' => array('vid','hash','hash_resume'),
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

$ViewList['transfertohuman'] = array(
    'params' => array('chat_id','hash'),
	'uparams' => array()
);

$ViewList['editnick'] = array(
    'params' => array('chat_id','hash'),
	'uparams' => array()
);

$ViewList['usertyping'] = array(
    'params' => array('chat_id','hash','status'),
	'uparams' => array()
);

$ViewList['checkchatstatus'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('mode','theme','dot')
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
    'uparams' => array('fresh','ua','ma','operator','theme','priority','disable_pro_active','click','position','hide_offline','check_operator_messages','top','units','leaveamessage','department','identifier','survey','dot','bot_id'),
	'multiple_arguments' => array ( 'department', 'ua' )
);

$ViewList['htmlsnippet'] = array(
    'params' => array('id','type','sub_id'),
    'uparams' => array('hash'),
	'multiple_arguments' => array ( 'department', 'ua' )
);

$ViewList['chatcheckstatus'] = array(
    'params' => array(),
    'uparams' => array('status','department','vid','uactiv','wopen','uaction','hash','hash_resume','dot','hide_offline','isproactive'),
	'multiple_arguments' => array ( 'department' )
);

$ViewList['getstatusembed'] = array (
    'params' => array(),
    'uparams' => array('fresh','ua','operator','theme','hide_offline','leaveamessage','department','priority','survey','bot_id'),
	'multiple_arguments' => array ( 'department','ua' )
);

$ViewList['startchat'] = array (
    'params' => array(),
    'uparams' => array('ua','switchform','operator','theme','er','vid','hash_resume','sound','hash','offline','leaveamessage','department','priority','chatprefill','survey','prod','phash','pvhash','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);

$ViewList['start'] = array (
    'params' => array(),
    'uparams' => array('id','hash','department','theme','mobile','vid','identifier','inv','survey','priority','operator','leaveamessage','h','mode','bot'),
	'multiple_arguments' => array('department')
);

$ViewList['chatwidget'] = array (
    'params' => array(),
    'uparams' => array('mobile','bot_id','ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','sdemo','prod','phash','pvhash','fullheight','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);

$ViewList['reopen'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('mode','embedmode','theme','fullheight'),
);

$ViewList['readoperatormessage'] = array(
    'params' => array(),
    'uparams' => array('operator','theme','priority','vid','department','playsound','ua','survey','fullheight','inv','tag'),
	'multiple_arguments' => array ( 'department', 'ua' )
);

$ViewList['chatcheckoperatormessage'] = array(
    'params' => array(),
    'uparams' => array('tz','operator','theme','priority','vid','count_page','identifier','department','ua','survey','uactiv','wopen','fullheight','dyn'),
	'multiple_arguments' => array ( 'department','ua','dyn' )
);

$ViewList['extendcookie'] = array(
    'params' => array('vid'),
    'uparams' => array()
);

$ViewList['logpageview'] = array(
    'params' => array(),
    'uparams' => array('tz','vid','identifier','department','ua','uactiv','wopen'),
	'multiple_arguments' => array ( 'department','ua' )
);

$ViewList['chatwidgetclosed'] = array(
    'params' => array(),
    'uparams' => array('vid','hash','eclose'),
);

$ViewList['chat'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('theme','er','survey','cstarted')
);

$ViewList['printchat'] = array(
    'params' => array('chat_id','hash')
);

$ViewList['readchatmail'] = array(
    'params' => array('chat_id','hash')
);

$ViewList['chatpreview'] = array(
    'params' => array('chat_id','hash')
);

$ViewList['bbcodeinsert'] = array(
	'params' => array('chat_id'),
	'uparams' => array('mode')
);

$ViewList['chatwidgetchat'] = array(
    'params' => array('chat_id','hash'),
	'uparams' => array('mobile','sound','mode','theme','cstarted','survey','pchat','fullheight')
);

$ViewList['userclosechat'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('eclose'),
);

$ViewList['onlineusers'] = array(
    'params' => array(),
    'ajax' => true,
    'uparams' => array('clear_list','method','deletevisitor','timeout','csfr','department','maxrows','country','timeonsite'),
    'functions' => array( 'use_onlineusers' )
);

$ViewList['jsononlineusers'] = array(
    'params' => array(),
    'uparams' => array('department','maxrows','timeout'),
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
    'functions' => array( 'administrateconfig' )
);

$ViewList['editchatconfig'] = array(
    'params' => array('config_id'),
    'functions' => array( 'administrateconfig' )
);

$ViewList['syncandsoundesetting'] = array(
    'params' => array(),
    'functions' => array( 'administratesyncsound' )
);

$ViewList['cannedmsg'] = array(
    'params' => array(),
    'uparams' => array('action','id','csfr','message','title','fmsg','department_id'),
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

$ViewList['confirmleave'] = array(
    'params' => array('chat_id','hash')
);

$ViewList['sendchat'] = array(
		'params' => array('chat_id','hash')
);

$ViewList['transferchatrefilter'] = array(
    'params' => array('chat_id'),
    'uparams' => array('mode'),
    'functions' => array( 'use' )
);

$FunctionList['use'] = array('explain' => 'General permission to use chat module');
$FunctionList['open_all'] = array('explain' => 'Allow operator to open all chats, not only assigned to him');
$FunctionList['changeowner'] = array('explain' => 'Allow operator to change chat owner');
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
$FunctionList['allowtransferdirectly'] = array('explain' =>'Allow operator to transfer chat directly to other operator');
$FunctionList['use_onlineusers'] = array('explain' =>'Allow operator to view online visitors');
$FunctionList['chattabschrome'] = array('explain' =>'Allow operator to use chrome extension');
$FunctionList['canchangechatstatus'] = array('explain' =>'Allow operator to change chat status');
$FunctionList['administrateinvitations'] = array('explain' =>'Allow operator to change pro active invitations');
$FunctionList['administratecampaigs'] = array('explain' =>'Allow operator to change pro active campaigns');
$FunctionList['administratechatevents'] = array('explain' =>'Allow operator to change pro active chat events');
$FunctionList['administratechatvariables'] = array('explain' =>'Allow operator to change pro active chat variables');
$FunctionList['administrateresponder'] = array('explain' =>'Allow operator to change auto responder');
$FunctionList['maintenance'] = array('explain' =>'Allow operator to run maintenance');
$FunctionList['sees_all_online_visitors'] = array('explain' =>'Operator can see all online visitors, not only his department');
$FunctionList['geoadjustment'] = array('explain' => 'Allow operator to edit geo adjustment for chat status');
$FunctionList['take_screenshot'] = array('explain' => 'Allow operator to take visitor browser page screenshots');
$FunctionList['modifychat'] = array('explain' => 'Allow operator modify main chat information');
$FunctionList['allowredirect'] = array('explain' => 'Allow operator to redirect user to another page');
$FunctionList['administrategeoconfig'] = array('explain' => 'Allow operator to edit geo detection configuration');
$FunctionList['manage_product'] = array('explain' => 'Allow operator to manage products');
$FunctionList['administratesubject'] = array('explain' => 'Allow operator to manage subjects');
$FunctionList['modifychatcore'] = array('explain' => 'Allow operator to change chat core attributes');
$FunctionList['sendmail'] = array('explain' => 'Allow operator to send e-mail to visitor from chat window');
$FunctionList['redirectcontact'] = array('explain' => 'Allow operator to redirect visitor to contact form');
$FunctionList['holduse'] = array('explain' => 'Allow operator to use hold/unhold functionality');
$FunctionList['setsubject'] = array('explain' => 'Allow operator to use set chat subject');
$FunctionList['administratecolumn'] = array('explain' => 'Allow operator to configure chat columns');
$FunctionList['administratechatvariable'] = array('explain' => 'Allow operator to configure chat custom variables');
$FunctionList['administratechatpriority'] = array('explain' => 'Allow operator to configure chat priority by custom variables');
$FunctionList['administratesyncsound'] = array('explain' => 'Allow operator to configure chat sound and sync settings');
$FunctionList['voicemessages'] = array('explain' => 'Allow operator to send voice messages');


?>