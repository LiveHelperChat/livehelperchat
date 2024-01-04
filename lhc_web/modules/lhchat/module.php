<?php

$Module = array( "name" => "Chat");

$ViewList = array();

$ViewList['adminchat'] = array(
    'params' => array('chat_id'),
    'uparams' => array('remember','arg','ol'),
    'functions' => array( 'use' ),
    'multiple_arguments' => array('arg','ol')
);

$ViewList['getchatdata'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$ViewList['icondetailed'] = array(
    'params' => array('chat_id','column_id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$ViewList['chathistory'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$ViewList['sendmassmessage'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$ViewList['singleaction'] = array(
    'params' => array('chat_id','action'),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$ViewList['subjectwidget'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'subject_chats_options' ),
);

$ViewList['loadoperatorjs'] = array(
    'params' => array(),
    'uparams' => array('type','id'),
    'functions' => array( 'use' ),
);

$ViewList['loadmaindata'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['loadpreviousmessages'] = array(
    'params' => array('chat_id','message_id'),
    'uparams' => array('initial','original'),
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
		'uparams' => array('hash','hash_resume','vid','userinit'),
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
    'params' => array('chat_id','msg_id'),
    'uparams' => array(),
    'functions' => array( 'editprevious' )
);

$ViewList['quotemessage'] = array(
    'params' => array('id'),
    'uparams' => array('type'),
    'functions' => array( 'use' )
);

$ViewList['updatemsg'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['updatemessagedata'] = array(
    'params' => array('chat_id', 'hash', 'msg_id'),
    'uparams' => array(),
    'functions' => array(  )
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

$ViewList['abstractclick'] = array(
    'params' => array('msg_id','payload'),
    'functions' => array( 'use' )
);

$ViewList['setsubstatus'] = array(
    'params' => array('chat_id','substatus'),
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
    'uparams' => array(
        'bcs',
        'oopu',
        'oopugroups',
        'subjectd',
        'limits',
        'subjectd',
        'sdgroups',
        'subjectdprod',
        'subjectu',
        'sugroups',
        'hsub','lda','bdgroups','botdprod','w','clcs','limitgc','limitb','botd','odpgroups','ddgroups','udgroups','mdgroups', 'cdgroups', 'pdgroups','adgroups','pugroups','augroups','onop', 'acs', 'mcd', 'limitmc', 'mcdprod','activeu','pendingu','topen','departmentd','operatord','actived','pendingd','closedd','unreadd','limita','limitp','limitc','limitu','limito','limitd','activedprod','unreaddprod','pendingdprod','closeddprod','psort'),
    'ajax' => true,
    'functions' => array( 'use' ),
    'multiple_arguments' => array (
        'oopu',
        'oopugroups',
        'subjectd',
        'subjectd',
        'sdgroups',
        'subjectdprod',
        'subjectu',
        'sugroups',
        'hsub','bdgroups','botdprod','botd','w','odpgroups','ddgroups','udgroups','mdgroups', 'cdgroups', 'pdgroups', 'adgroups', 'pugroups','augroups','mcd','operatord','mcdprod', 'activeu', 'pendingu', 'actived', 'closedd' , 'pendingd', 'unreadd','departmentd','activedprod','unreaddprod','pendingdprod','closeddprod')
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
    'uparams' => array('sortby','timefromts','transfer_happened','phone','not_invitation','proactive_chat','view','dropped_chat','abandoned_chat','country_ids','has_unread_op_messages','cls_us','export','chat_status_ids','cf','with_bot','no_operator','has_operator','without_bot','bot_ids','ip','department_ids','department_group_ids','user_ids','group_ids','subject_id','anonymized','una','chat_duration_from','chat_duration_till','wait_time_from','wait_time_till','chat_id','nick','email','timefrom','timeto','department_id','user_id','print','xls','fbst','chat_status','hum','product_id','timefrom','timefrom_seconds','timefrom_minutes','timefrom_hours','timeto', 'timeto_minutes', 'timeto_seconds', 'timeto_hours', 'department_group_id', 'group_id', 'invitation_id',
        'country_ids',
        'region',
        'iwh_ids',
        'theme_ids',
        'frt_from',
        'frt_till',
        'mart_from',
        'mart_till',
        'aart_till',
        'aart_from',
        'ipp'
        ),
    'functions' => array( 'use' ),
    'multiple_arguments' => array(
        'department_ids',
        'department_group_ids',
        'user_ids',
        'group_ids',
        'bot_ids',
        'subject_id',
        'country_ids',
        'chat_status_ids',
        'cf',
        'country_ids',
        'iwh_ids',
        'theme_ids'
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

$ViewList['reactmodal'] = array(
    'params' => array('msg_id'),
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

$ViewList['reaction'] = array(
    'params' => array('msg_id'),
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
    'uparams' => array('remove_block','csfr','ip','nick'),
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
    'uparams' => array('sound','id','hash','department','theme','mobile','vid','identifier','inv','survey','priority','operator','leaveamessage','mode','bot','scope','fs','trigger'),
	'multiple_arguments' => array('department')
);

$ViewList['begin'] = array (
    'params' => array(),
    'uparams' => array('sound','id','hash','department','theme','mobile','vid','identifier','inv','survey','priority','operator','leaveamessage','mode','bot','scope','fs','trigger'),
	'multiple_arguments' => array('department')
);

$ViewList['modal'] = array (
    'params' => array(),
    'uparams' => array('sound','id','hash','department','theme','mobile','vid','identifier','inv','survey','priority','operator','leaveamessage','mode','bot','scope','fs','trigger'),
	'multiple_arguments' => array('department')
);

$ViewList['demo'] = array (
    'params' => array(),
    'uparams' => array('sound','id','hash','department','theme','mobile','vid','identifier','inv','survey','priority','operator','leaveamessage','mode','bot','scope','fs','trigger'),
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
    'uparams' => array('vid','hash','eclose','close','conversion'),
);

$ViewList['chat'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('theme','er','survey','cstarted')
);

$ViewList['printchat'] = array(
    'params' => array('chat_id','hash')
);

$ViewList['downloadtxt'] = array(
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
    'uparams' => array('clear_list','method','deletevisitor','timeout','csfr','department','maxrows','country','timeonsite','department_dpgroups','nochat'),
    'functions' => array( 'use_onlineusers' ),
    'multiple_arguments' => array(
        'department',
        'department_dpgroups'
    )
);

$ViewList['jsononlineusers'] = array(
    'params' => array(),
    'uparams' => array('department','maxrows','timeout','department_dpgroups'),
    'functions' => array( 'use_onlineusers' ),
    'multiple_arguments' => array(
        'department',
        'department_dpgroups'
    )
);

$ViewList['getonlineuserinfo'] = array(
    'params' => array('id'),
    'uparams' => array('tab','chat_id'),
    'functions' => array( 'use' )
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
    'uparams' => array('action','id','csfr','message','title','fmsg','department_id','subject_id','tab','user_id','timefrom','timeto','sortby','export','used_freq','group_ids','user_ids','department_group_ids','department_ids'),
    'functions' => array( 'explorecannedmsg' ),
    'multiple_arguments' => array(
        'department_id',
        'subject_id',
        'user_id',
        'department_ids',
        'department_group_ids',
        'user_ids',
        'group_ids',
    )
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
    'functions' => array( 'explorecannedmsg' )
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

$ViewList['reacttomessagemodal'] = array(
    'params' => array('message_id'),
    'uparams' => array('theme')
);

$ViewList['sendchat'] = array(
		'params' => array('chat_id','hash')
);

$ViewList['transferchatrefilter'] = array(
    'params' => array('chat_id'),
    'uparams' => array('mode'),
    'functions' => array( 'use' )
);

$ViewList['searchprovider'] = array(
    'params' => array('scope'),
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
$FunctionList['explorecannedmsg'] = array('explain' =>'Allow operator to explore canned messages. He will see canned messages based on departments he is member of.');
$FunctionList['explorecannedmsg_all'] = array('explain' =>'Allow operator to explore canned messages. He will see all departments canned messages.');
$FunctionList['allowopenremotechat'] = array('explain' =>'Allow operator to open other operators chats from same department');
$FunctionList['writeremotechat'] = array('explain' =>'Allow operator to write to another operator chat');
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
$FunctionList['chatdebug'] = array('explain' => 'Allow operator to see raw chat details in chat edit window');
$FunctionList['administrate_alert_icon'] = array('explain' => 'Allow operator to manage alert icons list');
$FunctionList['prev_chats'] = array('explain' => 'Allow operator to see previous chats from visitor');
$FunctionList['changedepartment'] = array('explain' => 'Allow operator to change chat department');
$FunctionList['subject_chats'] = array('explain' => 'Allow operator see subject filtered chats');
$FunctionList['subject_chats_options'] = array('explain' => 'Allow operator to choose what subjects should be applied as filter');
$FunctionList['export_chats'] = array('explain' => 'Allow operator to export filtered chats');
$FunctionList['htmlbbcodeenabled'] = array('explain' => 'Allow operator to use [html] bbcode.');
$FunctionList['metamsgenabled'] = array('explain' => 'Allow operator to use meta_msg in message add interface.');
$FunctionList['seeip'] = array('explain' => 'Allow operator to see full IP');
$FunctionList['editprevious'] = array('explain' => 'Allow operator to edit his previous messages');
$FunctionList['editpreviousop'] = array('explain' => 'Allow operator to edit other operators previous messages');
$FunctionList['editpreviouvis'] = array('explain' => 'Allow operator to edit visitors previous messages');
$FunctionList['impersonate'] = array('explain' => 'Allow operator to impersonate another operator on joining chat window');
$FunctionList['allowtransfertoanydep'] = array('explain' => 'Allow operator to transfer chat to any department.');
$FunctionList['list_all_chats'] = array('explain' => 'Allow operator to list all chats independently of operator and status.');
$FunctionList['list_my_chats'] = array('explain' => 'Allow operator to list chats he is owner');
$FunctionList['list_pending_chats'] = array('explain' => 'Allow operator to list chats without an owner and in status pending.');
$FunctionList['use_unhidden_phone'] = array('explain' => 'Allow operator to see full phone number');
$FunctionList['chat_see_email'] = array('explain' => 'Allow operator to see e-mail of the visitor');
$FunctionList['chat_see_unhidden_email'] = array('explain' => 'Allow operator to see full e-mail address of the visitor');
$FunctionList['see_sensitive_information'] = array('explain' => 'Allow operator to see sensitive information in the messages');
$FunctionList['my_chats_filter'] = array('explain' => 'Allow operator to see department filter for my active pending chats widget');
$FunctionList['allowopenclosedchats'] = array('explain' => 'Allow operator to open closed chats');

?>