<?php

$Module = ['name' => 'Chat'];

$ViewList = [
    'adminchat' => [
        'params' => ['chat_id'],
        'uparams' => ['remember', 'arg', 'ol'],
        'functions' => ['use'],
        'multiple_arguments' => ['arg', 'ol'],
    ],
    'getchatdata' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'verifytoken' => [
        'params' => [],
        'uparams' => [],
    ],
    'icondetailed' => [
        'params' => ['chat_id', 'column_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'relatedactions' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'chathistory' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'sendmassmessage' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'singleaction' => [
        'params' => ['chat_id', 'action'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'subjectwidget' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['subject_chats_options'],
    ],
    'loadoperatorjs' => [
        'params' => [],
        'uparams' => ['type', 'id'],
        'functions' => ['use'],
    ],
    'loadmaindata' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'loadpreviousmessages' => [
        'params' => ['chat_id', 'message_id'],
        'uparams' => ['initial', 'original'],
        'functions' => ['use'],
        'multiple_arguments' => [],
    ],
    'subject' => [
        'params' => ['chat_id'],
        'uparams' => ['subject', 'status'],
        'functions' => ['setsubject'],
    ],
    'getnotificationsdata' => [
        'params' => [],
        'uparams' => ['id'],
        'ajax' => true,
        'functions' => ['use'],
        'multiple_arguments' => ['id'],
    ],
    'getcannedfiltered' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'holdaction' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['holduse'],
    ],
    'copymessages' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'updateattribute' => [
        'params' => [],
        'uparams' => ['hash', 'hash_resume', 'vid'],
    ],
    'updatejsvars' => [
        'params' => [],
        'uparams' => ['hash', 'hash_resume', 'vid', 'userinit', 'encrypted'],
    ],
    'logevent' => [
        'params' => [],
        'uparams' => ['hash', 'hash_resume', 'vid'],
    ],
    'setnewvid' => [
        'params' => [],
        'uparams' => [],
    ],
    'redirectcontact' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['redirectcontact'],
    ],
    'changestatus' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['canchangechatstatus'],
    ],
    'editprevious' => [
        'params' => ['chat_id', 'msg_id'],
        'uparams' => [],
        'functions' => ['editprevious'],
    ],
    'deletemsg' => [
        'params' => ['chat_id', 'msg_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'quotemessage' => [
        'params' => ['id'],
        'uparams' => ['type'],
        'functions' => ['use'],
    ],
    'updatemsg' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'updatemessagedata' => [
        'params' => ['chat_id', 'hash', 'msg_id'],
        'uparams' => [],
        'functions' => [],
    ],
    'printchatadmin' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'loadactivechats' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'previewchat' => [
        'params' => ['chat_id'],
        'functions' => ['use'],
    ],
    'previewmessage' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'closechatadmin' => [
        'params' => ['chat_id'],
        'functions' => ['use'],
    ],
    'abstractclick' => [
        'params' => ['msg_id', 'payload'],
        'functions' => ['use'],
    ],
    'setsubstatus' => [
        'params' => ['chat_id', 'substatus'],
        'functions' => ['use'],
    ],
    'notificationsettings' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'startchatwithoperator' => [
        'params' => ['user_id'],
        'uparams' => ['mode'],
        'functions' => ['use'],
    ],
    'closechat' => [
        'params' => ['chat_id'],
        'uparams' => ['csfr'],
        'functions' => ['use'],
    ],
    'sendmail' => [
        'params' => ['chat_id'],
        'functions' => ['sendmail'],
    ],
    'modifychat' => [
        'params' => ['chat_id'],
        'uparams' => ['pos'],
        'functions' => ['modifychat'],
    ],
    'transferchat' => [
        'params' => ['chat_id'],
        'functions' => ['allowtransfer'],
    ],
    'accepttransfer' => [
        'params' => ['transfer_id'],
        'uparams' => ['postaction', 'mode', 'scope'],
        'functions' => ['use'],
    ],
    'deletechatadmin' => [
        'params' => ['chat_id'],
        'functions' => ['deletechat'],
    ],
    'delete' => [
        'params' => ['chat_id'],
        'uparams' => ['csfr'],
        'functions' => ['deletechat'],
    ],
    'syncadmininterface' => [
        'params' => [],
        'uparams' => [
            'on_opf', 'mmd', 'mmdgroups', 'limitmm', 'bcs', 'oopu', 'oopugroups', 'subjectd', 'limits', 'sdgroups',
            'subjectdprod', 'subjectu', 'sugroups', 'limitam', 'pmd', 'pendingmu', 'pendingmd', 'pmug', 'amd',
            'activemu', 'limitalm', 'activemd', 'almd', 'almug', 'limitpm', 'amug', 'alarmmd', 'alarmmu', 'hsub',
            'lda', 'bdgroups', 'botdprod', 'w', 'clcs', 'limitgc', 'limitb', 'botd', 'odpgroups', 'ddgroups',
            'udgroups', 'mdgroups', 'cdgroups', 'pdgroups', 'adgroups', 'pugroups', 'augroups', 'onop', 'acs',
            'mcd', 'limitmc', 'mcdprod', 'activeu', 'pendingu', 'topen', 'departmentd', 'operatord', 'actived',
            'pendingd', 'closedd', 'unreadd', 'limita', 'limitp', 'limitc', 'limitu', 'limito', 'limitd',
            'activedprod', 'unreaddprod', 'pendingdprod', 'closeddprod', 'psort',
        ],
        'ajax' => true,
        'functions' => ['use'],
        'multiple_arguments' => [
            'mmd', 'mmdgroups', 'oopu', 'oopugroups', 'subjectd', 'sdgroups', 'subjectdprod', 'subjectu',
            'sugroups', 'pmd', 'pendingmu', 'pendingmd', 'pmug', 'amd', 'activemu', 'activemd', 'almd', 'almug',
            'amug', 'alarmmd', 'alarmmu', 'hsub', 'bdgroups', 'botdprod', 'botd', 'w', 'odpgroups', 'ddgroups',
            'udgroups', 'mdgroups', 'cdgroups', 'pdgroups', 'adgroups', 'pugroups', 'augroups', 'mcd', 'operatord',
            'mcdprod', 'activeu', 'pendingu', 'actived', 'closedd', 'pendingd', 'unreadd', 'departmentd',
            'activedprod', 'unreaddprod', 'pendingdprod', 'closeddprod',
        ],
    ],
    'loadinitialdata' => [
        'params' => [],
        'uparams' => ['chatopen', 'chatgopen', 'chatmopen'],
        'ajax' => true,
        'functions' => ['use'],
        'multiple_arguments' => ['chatopen', 'chatgopen', 'chatmopen'],
    ],
    'list' => [
        'params' => [],
        'uparams' => [
            'cls_time', 'sortby', 'timefrom_type', 'timefromts', 'transfer_happened', 'phone', 'not_invitation',
            'proactive_chat', 'view', 'dropped_chat', 'abandoned_chat', 'country_ids', 'has_unread_op_messages',
            'cls_us', 'export', 'chat_status_ids', 'cf', 'with_bot', 'no_operator', 'has_operator', 'without_bot',
            'bot_ids', 'ip', 'department_ids', 'department_group_ids', 'user_ids', 'group_ids', 'subject_id',
            'anonymized', 'una', 'chat_duration_from', 'chat_duration_till', 'wait_time_from', 'wait_time_till',
            'chat_id', 'nick', 'email', 'timefrom', 'timeto', 'department_id', 'user_id', 'print', 'xls', 'fbst',
            'chat_status', 'hum', 'product_id', 'timefrom', 'timefrom_seconds', 'timefrom_minutes', 'timefrom_hours',
            'timeto', 'timeto_minutes', 'timeto_seconds', 'timeto_hours', 'department_group_id', 'group_id',
            'invitation_id', 'country_ids', 'region', 'iwh_ids', 'theme_ids', 'frt_from', 'frt_till', 'mart_from',
            'mart_till', 'aart_till', 'aart_from', 'priority_from', 'priority_till', 'ipp', 'op_msg_count',
            'vi_msg_count', 'bot_msg_count', 'all_msg_count', 'all_msg_count_till', 'as_participant',
            'participant_not_owner',
        ],
        'functions' => ['use'],
        'multiple_arguments' => [
            'department_ids', 'department_group_ids', 'user_ids', 'group_ids', 'bot_ids', 'subject_id',
            'country_ids', 'chat_status_ids', 'cf', 'country_ids', 'iwh_ids', 'theme_ids',
        ],
    ],
    'dashboardwidgets' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'chattabs' => [
        'params' => ['chat_id'],
        'functions' => ['allowchattabs'],
    ],
    'chattabschrome' => [
        'params' => [],
        'uparams' => ['mode'],
        'functions' => [],
    ],
    'single' => [
        'params' => ['chat_id'],
        'functions' => ['singlechatwindow'],
    ],
    'chatfootprint' => [
        'params' => ['chat_id'],
        'functions' => ['use'],
    ],
    'refreshonlineinfo' => [
        'params' => ['chat_id'],
        'functions' => ['use'],
    ],
    'checkscreenshot' => [
        'params' => ['chat_id'],
        'functions' => ['use'],
    ],
    'checkscreenshotonline' => [
        'params' => ['online_id'],
        'functions' => ['use'],
    ],
    'operatortyping' => [
        'params' => ['chat_id', 'status'],
        'functions' => ['use'],
    ],
    'syncadmin' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'addmsgadmin' => [
        'params' => ['chat_id'],
        'functions' => ['use'],
    ],
    'reactmodal' => [
        'params' => ['msg_id'],
        'functions' => ['use'],
    ],
    'updatechatstatus' => [
        'params' => ['chat_id'],
        'functions' => ['use'],
    ],
    'addoperation' => [
        'params' => ['chat_id'],
        'functions' => ['use'],
    ],
    'refreshcustomfields' => [
        'params' => [],
        'uparams' => ['vid', 'hash', 'hash_resume'],
    ],
    'addonlineoperation' => [
        'params' => ['online_user_id'],
        'functions' => ['use'],
    ],
    'addonlineoperationiframe' => [
        'params' => ['online_user_id'],
        'functions' => ['use'],
    ],
    'saveremarks' => [
        'params' => ['chat_id'],
        'functions' => ['use'],
    ],
    'reaction' => [
        'params' => ['msg_id'],
        'functions' => ['use'],
    ],
    'saveonlinenotes' => [
        'params' => ['online_user_id'],
        'functions' => ['use'],
    ],
    'addmsguser' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => ['mode'],
    ],
    'editprevioususer' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => [],
    ],
    'updatemsguser' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => ['mode'],
    ],
    'getmessage' => [
        'params' => ['chat_id', 'hash', 'msgid'],
        'uparams' => ['mode'],
    ],
    'getmessageadmin' => [
        'params' => ['chat_id', 'msgid'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'voteaction' => [
        'params' => ['chat_id', 'hash', 'type'],
        'uparams' => [],
    ],
    'syncuser' => [
        'params' => ['chat_id', 'message_id', 'hash'],
        'uparams' => ['mode', 'ot', 'theme', 'modeembed'],
    ],
    'transfertohuman' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => [],
    ],
    'editnick' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => [],
    ],
    'usertyping' => [
        'params' => ['chat_id', 'hash', 'status'],
        'uparams' => [],
    ],
    'checkchatstatus' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => ['mode', 'theme', 'dot'],
    ],
    'transferuser' => [
        'params' => ['chat_id', 'item_id'],
        'functions' => ['allowtransfer'],
    ],
    'blockuser' => [
        'params' => ['chat_id'],
        'functions' => ['allowblockusers'],
    ],
    'blockedusers' => [
        'params' => [],
        'uparams' => ['remove_block', 'csfr', 'ip', 'nick'],
        'functions' => ['allowblockusers'],
    ],
    'getstatus' => [
        'params' => [],
        'uparams' => ['fresh', 'ua', 'ma', 'operator', 'theme', 'priority', 'disable_pro_active', 'click', 'position', 'hide_offline', 'check_operator_messages', 'top', 'units', 'leaveamessage', 'department', 'identifier', 'survey', 'dot', 'bot_id'],
        'multiple_arguments' => ['department', 'ua'],
    ],
    'htmlsnippet' => [
        'params' => ['id', 'type', 'sub_id'],
        'uparams' => ['hash'],
        'multiple_arguments' => ['department', 'ua'],
    ],
    'chatcheckstatus' => [
        'params' => [],
        'uparams' => ['status', 'department', 'vid', 'uactiv', 'wopen', 'uaction', 'hash', 'hash_resume', 'dot', 'hide_offline', 'isproactive'],
        'multiple_arguments' => ['department'],
    ],
    'getstatusembed' => [
        'params' => [],
        'uparams' => ['fresh', 'ua', 'operator', 'theme', 'hide_offline', 'leaveamessage', 'department', 'priority', 'survey', 'bot_id'],
        'multiple_arguments' => ['department', 'ua'],
    ],
    'startchat' => [
        'params' => [],
        'uparams' => ['ua', 'switchform', 'operator', 'theme', 'er', 'vid', 'hash_resume', 'sound', 'hash', 'offline', 'leaveamessage', 'department', 'priority', 'chatprefill', 'survey', 'prod', 'phash', 'pvhash', 'ajaxmode'],
        'multiple_arguments' => ['department', 'ua', 'prod'],
    ],
    'start' => [
        'params' => [],
        'uparams' => ['sound', 'id', 'hash', 'department', 'theme', 'mobile', 'vid', 'identifier', 'inv', 'survey', 'priority', 'operator', 'leaveamessage', 'mode', 'bot', 'scope', 'fs', 'trigger', 'encrypted'],
        'multiple_arguments' => ['department'],
    ],
    'begin' => [
        'params' => [],
        'uparams' => ['sound', 'id', 'hash', 'department', 'theme', 'mobile', 'vid', 'identifier', 'inv', 'survey', 'priority', 'operator', 'leaveamessage', 'mode', 'bot', 'scope', 'fs', 'trigger', 'encrypted'],
        'multiple_arguments' => ['department'],
    ],
    'modal' => [
        'params' => [],
        'uparams' => ['sound', 'id', 'hash', 'department', 'theme', 'mobile', 'vid', 'identifier', 'inv', 'survey', 'priority', 'operator', 'leaveamessage', 'mode', 'bot', 'scope', 'fs', 'trigger', 'encrypted'],
        'multiple_arguments' => ['department'],
    ],
    'demo' => [
        'params' => [],
        'uparams' => ['sound', 'id', 'hash', 'department', 'theme', 'mobile', 'vid', 'identifier', 'inv', 'survey', 'priority', 'operator', 'leaveamessage', 'mode', 'bot', 'scope', 'fs', 'trigger', 'encrypted', 'debug'],
        'multiple_arguments' => ['department'],
        'functions' => ['use'],
    ],
    'chatwidget' => [
        'params' => [],
        'uparams' => ['mobile', 'bot_id', 'ua', 'switchform', 'operator', 'theme', 'vid', 'sound', 'hash', 'hash_resume', 'mode', 'offline', 'leaveamessage', 'department', 'priority', 'chatprefill', 'survey', 'sdemo', 'prod', 'phash', 'pvhash', 'fullheight', 'ajaxmode'],
        'multiple_arguments' => ['department', 'ua', 'prod'],
    ],
    'reopen' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => ['mode', 'embedmode', 'theme', 'fullheight'],
    ],
    'readoperatormessage' => [
        'params' => [],
        'uparams' => ['operator', 'theme', 'priority', 'vid', 'department', 'playsound', 'ua', 'survey', 'fullheight', 'inv', 'tag'],
        'multiple_arguments' => ['department', 'ua'],
    ],
    'chatcheckoperatormessage' => [
        'params' => [],
        'uparams' => ['tz', 'operator', 'theme', 'priority', 'vid', 'count_page', 'identifier', 'department', 'ua', 'survey', 'uactiv', 'wopen', 'fullheight', 'dyn'],
        'multiple_arguments' => ['department', 'ua', 'dyn'],
    ],
    'extendcookie' => [
        'params' => ['vid'],
        'uparams' => [],
    ],
    'logpageview' => [
        'params' => [],
        'uparams' => ['tz', 'vid', 'identifier', 'department', 'ua', 'uactiv', 'wopen'],
        'multiple_arguments' => ['department', 'ua'],
    ],
    'chatwidgetclosed' => [
        'params' => [],
        'uparams' => ['vid', 'hash', 'eclose', 'close', 'conversion'],
    ],
    'chat' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => ['theme', 'er', 'survey', 'cstarted'],
    ],
    'printchat' => [
        'params' => ['chat_id', 'hash'],
    ],
    'downloadtxt' => [
        'params' => ['chat_id', 'hash'],
    ],
    'readchatmail' => [
        'params' => ['chat_id', 'hash'],
    ],
    'chatpreview' => [
        'params' => ['chat_id', 'hash'],
    ],
    'bbcodeinsert' => [
        'params' => ['chat_id'],
        'uparams' => ['mode'],
    ],
    'chatwidgetchat' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => ['mobile', 'sound', 'mode', 'theme', 'cstarted', 'survey', 'pchat', 'fullheight'],
    ],
    'userclosechat' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => ['eclose'],
    ],
    'onlineusers' => [
        'params' => [],
        'ajax' => true,
        'uparams' => ['clear_list', 'method', 'deletevisitor', 'timeout', 'csfr', 'department', 'maxrows', 'country', 'timeonsite', 'department_dpgroups', 'nochat'],
        'functions' => ['use_onlineusers'],
        'multiple_arguments' => ['department', 'department_dpgroups'],
    ],
    'jsononlineusers' => [
        'params' => [],
        'uparams' => ['department', 'maxrows', 'timeout', 'department_dpgroups'],
        'functions' => ['use_onlineusers'],
        'multiple_arguments' => ['department', 'department_dpgroups'],
    ],
    'getonlineuserinfo' => [
        'params' => ['id'],
        'uparams' => ['tab', 'chat_id'],
        'functions' => ['use'],
    ],
    'sendnotice' => [
        'params' => ['online_id'],
        'functions' => ['use'],
    ],
    'geoconfiguration' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['administrategeoconfig'],
    ],
    'listchatconfig' => [
        'params' => [],
        'functions' => ['administrateconfig'],
    ],
    'editchatconfig' => [
        'params' => ['config_id'],
        'functions' => ['administrateconfig'],
    ],
    'syncandsoundesetting' => [
        'params' => [],
        'functions' => ['administratesyncsound'],
    ],
    'cannedmsg' => [
        'params' => [],
        'uparams' => ['action', 'id', 'csfr', 'message', 'title', 'fmsg', 'department_id', 'subject_id', 'tab', 'user_id', 'timefrom', 'timeto', 'sortby', 'export', 'used_freq', 'group_ids', 'user_ids', 'department_group_ids', 'department_ids'],
        'functions' => ['explorecannedmsg'],
        'multiple_arguments' => ['department_id', 'subject_id', 'user_id', 'department_ids', 'department_group_ids', 'user_ids', 'group_ids'],
    ],
    'maintenance' => [
        'params' => [],
        'uparams' => ['csfr', 'action'],
        'functions' => ['maintenance'],
    ],
    'newcannedmsg' => [
        'params' => [],
        'functions' => ['administratecannedmsg'],
    ],
    'cannedmsgedit' => [
        'params' => ['id'],
        'functions' => ['explorecannedmsg'],
    ],
    'geoadjustment' => [
        'params' => [],
        'functions' => ['geoadjustment'],
    ],
    'accept' => [
        'params' => ['hash', 'validation_hash', 'email'],
    ],
    'confirmleave' => [
        'params' => ['chat_id', 'hash'],
    ],
    'reacttomessagemodal' => [
        'params' => ['message_id'],
        'uparams' => ['theme'],
    ],
    'sendchat' => [
        'params' => ['chat_id', 'hash'],
    ],
    'transferchatrefilter' => [
        'params' => ['chat_id'],
        'uparams' => ['mode', 'obj'],
        'functions' => ['use'],
    ],
    'searchprovider' => [
        'params' => ['scope'],
        'functions' => ['use'],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'General permission to use chat module'],
    'open_all' => ['explain' => 'Allow operator to open all pending chats, not only assigned to him'],
    'changeowner' => ['explain' => 'Allow operator to change chat owner'],
    'singlechatwindow' => ['explain' => 'Allow operator to use single chat window functionality'],
    'allowchattabs' => ['explain' => 'Allow operator to user chat rooms functionality'],
    'deletechat' => ['explain' => 'Allow operator to delete their own chats'],
    'deleteglobalchat' => ['explain' => 'Allow to delete all chats'],
    'allowtransfer' => ['explain' => 'Allow user to transfer chat to another user/department'],
    'allowcloseremote' => ['explain' => 'Allow operator to close another operator chat'],
    'allowblockusers' => ['explain' => 'Allow operator to block visitors'],
    'administrateconfig' => ['explain' => 'Allow to change chat config'],
    'allowclearonlinelist' => ['explain' => 'Allow operator to clean online users list'],
    'administratecannedmsg' => ['explain' => 'Allow operator change canned messages'],
    'explorecannedmsg' => ['explain' => 'Allow operator to explore canned messages. They will see canned messages based on departments they are a member of.'],
    'explorecannedmsg_all' => ['explain' => 'Allow operator to explore canned messages. They will see all departments canned messages.'],
    'allowopenremotechat' => ['explain' => 'Allow operator to open other operators chats from same department'],
    'writeremotechat' => ['explain' => 'Allow operator to write to another operator chat'],
    'allowreopenremote' => ['explain' => 'Allow operator to reopen other operators chats'],
    'allowtransfertoanyuser' => ['explain' => 'Allow operator to transfer chat to any online operator, not only their own department users'],
    'allowtransferdirectly' => ['explain' => 'Allow operator to transfer chat directly to other operator'],
    'use_onlineusers' => ['explain' => 'Allow operator to view online visitors'],
    'chattabschrome' => ['explain' => 'Allow operator to use chrome extension'],
    'canchangechatstatus' => ['explain' => 'Allow operator to change chat status'],
    'administrateinvitations' => ['explain' => 'Allow operator to change pro active invitations'],
    'administratecampaigs' => ['explain' => 'Allow operator to change pro active campaigns'],
    'administratechatevents' => ['explain' => 'Allow operator to change pro active chat events'],
    'administratechatvariables' => ['explain' => 'Allow operator to change pro active chat variables'],
    'administrateresponder' => ['explain' => 'Allow operator to change auto responder'],
    'maintenance' => ['explain' => 'Allow operator to run maintenance'],
    'sees_all_online_visitors' => ['explain' => 'Operator can see all online visitors, not only their department'],
    'geoadjustment' => ['explain' => 'Allow operator to edit geo adjustment for chat status'],
    'take_screenshot' => ['explain' => 'Allow operator to take visitor browser page screenshots'],
    'modifychat' => ['explain' => 'Allow operator modify main chat information'],
    'allowredirect' => ['explain' => 'Allow operator to redirect user to another page'],
    'administrategeoconfig' => ['explain' => 'Allow operator to edit geo detection configuration'],
    'manage_product' => ['explain' => 'Allow operator to manage products'],
    'administratesubject' => ['explain' => 'Allow operator to manage subjects'],
    'modifychatcore' => ['explain' => 'Allow operator to change chat core attributes'],
    'sendmail' => ['explain' => 'Allow operator to send e-mail to visitor from chat window'],
    'redirectcontact' => ['explain' => 'Allow operator to redirect visitor to contact form'],
    'holduse' => ['explain' => 'Allow operator to use hold/unhold functionality'],
    'setsubject' => ['explain' => 'Allow operator to use set chat subject'],
    'administratecolumn' => ['explain' => 'Allow operator to configure chat columns'],
    'administratechatvariable' => ['explain' => 'Allow operator to configure chat custom variables'],
    'administratechatpriority' => ['explain' => 'Allow operator to configure chat priority by custom variables'],
    'administratesyncsound' => ['explain' => 'Allow operator to configure chat sound and sync settings'],
    'voicemessages' => ['explain' => 'Allow operator to send voice messages'],
    'chatdebug' => ['explain' => 'Allow operator to see raw chat details in chat edit window'],
    'administrate_alert_icon' => ['explain' => 'Allow operator to manage alert icons list'],
    'prev_chats' => ['explain' => 'Allow operator to see previous chats from visitor'],
    'changedepartment' => ['explain' => 'Allow operator to change chat department'],
    'subject_chats' => ['explain' => 'Allow operator see subject filtered chats'],
    'subject_chats_options' => ['explain' => 'Allow operator to choose what subjects should be applied as filter'],
    'export_chats' => ['explain' => 'Allow operator to export filtered chats'],
    'htmlbbcodeenabled' => ['explain' => 'Allow operator to use [html] bbcode.'],
    'metamsgenabled' => ['explain' => 'Allow operator to use meta_msg in message add interface.'],
    'seeip' => ['explain' => 'Allow operator to see full IP'],
    'editprevious' => ['explain' => 'Allow operator to edit their previous message.'],
    'editpreviousop' => ['explain' => 'Allow operator to edit other operators previous messages'],
    'editpreviouvis' => ['explain' => 'Allow operator to edit visitors previous messages'],
    'editpreviousall' => ['explain' => 'Allow operator to edit all their previous messages.'],
    'impersonate' => ['explain' => 'Allow operator to impersonate another operator on joining chat window'],
    'whispermode' => ['explain' => 'Allow operator to use whisper mode'],
    'allowtransfertoanydep' => ['explain' => 'Allow operator to transfer chat to any department.'],
    'list_all_chats' => ['explain' => 'Allow operator to list all chats independently of operator and status.'],
    'list_my_chats' => ['explain' => 'Allow operator to list chats they are owner of'],
    'list_pending_chats' => ['explain' => 'Allow operator to list chats without an owner and in status pending.'],
    'use_unhidden_phone' => ['explain' => 'Allow operator to see full phone number'],
    'chat_see_email' => ['explain' => 'Allow operator to see e-mail of the visitor'],
    'chat_see_unhidden_email' => ['explain' => 'Allow operator to see full e-mail address of the visitor'],
    'chat_export_email' => ['explain' => 'Allow operator to see e-mail address in exported file'],
    'chat_export_phone' => ['explain' => 'Allow operator to see phone in exported file'],
    'see_sensitive_information' => ['explain' => 'Allow operator to see sensitive information in the messages'],
    'my_chats_filter' => ['explain' => 'Allow operator to see department filter for my active pending chats widget'],
    'allowopenclosedchats' => ['explain' => 'Allow operator to open closed chats'],
    'removemsgop' => ['explain' => 'Allow to remove operator any operator message'],
    'removemsgvi' => ['explain' => 'Allow to remove operator any visitor message'],
    'no_edit_history' => ['explain' => 'Do not store message edit history if edited by chat owner'],
    'see_operator_name' => ['explain' => 'Allow operator to see chat message real operator name'],
    'open_unassigned_chat' => ['explain' => 'Allow operator to open unassigned pending chat and become an owner of it.'],
];
