<?php

$Module = array( "name" => "Live helper Chat REST API service");

$ViewList = array();

$ViewList['index'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['chat'] = array(
    'params' => array('id')
);

$ViewList['chats'] = array(
    'params' => array()
);

$ViewList['conversations'] = array(
    'params' => array()
);

$ViewList['onlineimage'] = array(
    'params' => array(),
    'uparams' => array('user_id','department'),
    'multiple_arguments' => array ( 'department' )
);

$ViewList['extensions'] = array(
    'params' => array()
);

$ViewList['chatscount'] = array(
    'params' => array()
);

$ViewList['updatelastactivity'] = array(
    'params' => array('user_id')
);

$ViewList['fetchchat'] = array(
    'params' => array()
);

$ViewList['editwebhook'] = array(
    'params' => array(),
);

$ViewList['fetchchatmessages'] = array(
    'params' => array()
);

$ViewList['getmessages'] = array(
    'params' => array()
);

$ViewList['departaments'] = array(
    'params' => array()
);

$ViewList['isonlineuser'] = array(
    'params' => array('user_id')
);

$ViewList['isonlinechat'] = array(
    'params' => array('chat_id')
);

$ViewList['setoperatorstatus'] = array(
    'params' => array()
);

$ViewList['setinvisibilitystatus'] = array(
    'params' => array()
);

$ViewList['setonlinestatus'] = array(
    'params' => array('user_id','online')
);

$ViewList['isonline'] = array(
    'params' => array()
);

$ViewList['isonlinedepartment'] = array(
    'params' => array('department_id')
);

$ViewList['getusers'] = array(
    'params' => array()
);

$ViewList['onlineusers'] = array(
    'params' => array()
);

$ViewList['startchatwithoperator'] = array(
    'params' => array('id','initiator_user_id')
);

$ViewList['groupsbyobject'] = array(
    'params' => array('object_id','type')
);

$ViewList['groupsidbyobject'] = array(
    'params' => array('object_id','type')
);

$ViewList['listofobjectid'] = array(
    'params' => array('user_id','type')
);

$ViewList['bots'] = array(
    'params' => array()
);

$ViewList['user'] = array(
    'params' => array('id')
);

$ViewList['notifications'] = array(
    'params' => array('token')
);

$ViewList['user_departments'] = array(
    'params' => array()
);

$ViewList['lang'] = array(
    'params' => array('ns')
);

$ViewList['bot'] = array(
    'params' => array('id')
);

$ViewList['departments'] = array(
    'params' => array()
);

$ViewList['department'] = array(
    'params' => array('id')
);

$ViewList['getuser'] = array(
    'params' => array()
);

$ViewList['login'] = array(
    'params' => array()
);

$ViewList['loginbytoken'] = array(
    'params' => array()
);

$ViewList['logout'] = array(
    'params' => array()
);

$ViewList['generateautologin'] = array(
    'params' => array()
);

$ViewList['swagger'] = array(
    'params' => array()
);

$ViewList['agentstatistic'] = array(
    'params' => array()
);

/**
 * Calls dedicated to users
 * */
// Starts chat and returns chat data including chat id and hash
$ViewList['startchat'] = array(
    'params' => array(),
    'uparams' => array('ua','operator','er','vid','hash_resume','sound','hash','offline','leaveamessage','department','priority','chatprefill','survey','prod','phash','pvhash'),
    'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);

$ViewList['addmsguser'] = array(
    'params' => array()
);

$ViewList['addmsgadmin'] = array(
    'params' => array()
);

$ViewList['file'] = array(
    'params' => array('id')
);

$ViewList['setchatstatus'] = array(
    'params' => array()
);

$ViewList['campaignsconversions'] = array(
    'params' => array()
);

$ViewList['setnewvid'] = array(
    'params' => array()
);

// Checks is there any pending messages for online visitor and if there is return action what to do next
$ViewList['chatcheckoperatormessage'] = array(
    'params' => array(),
    'uparams' => array('tz','operator','theme','priority','vid','count_page','identifier','department','ua','survey','uactiv','wopen'),
    'multiple_arguments' => array ( 'department','ua' )
);

// Updates additional chat attributes
$ViewList['updatechatattributes'] = array(
    'params' => array()
);

// Closes chat from visitor perspective
$ViewList['closechatasvisitor'] = array(
    'params' => array()
);

$ViewList['checkchatstatus'] = array(
    'params' => array(),
    'uparams' => array('mode','theme','dot')
);

$ViewList['surveychat'] = array(
    'params' => array('chat_id','survey_id'),
    'uparams' => array()
);

$FunctionList = array();
$FunctionList['use_admin'] = array('explain' => 'Allow operator to manage REST API');
$FunctionList['use_direct_logins'] = array('explain' => 'Allow operator use api directly with their username and password');

// Objects API
$FunctionList['object_api'] = array('explain' => 'Allow operator to user objects api');
$FunctionList['updatelastactivity'] = array('explain' => 'Allow operator update other operator last activity');
$FunctionList['file_download'] = array('explain' => 'Allow operator to download file');
$FunctionList['list_extensions'] = array('explain' => 'Allow operator to list extensions');
$FunctionList['survey'] = array('explain' => 'Allow operator to work with survey');
$FunctionList['generateautologinall'] = array('explain' => 'Allow operator to generate auto login for any account');

?>