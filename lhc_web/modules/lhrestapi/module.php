<?php

$Module = ['name' => 'Live helper Chat REST API service'];

$ViewList = [
    'index' => [
        'params' => [],
        'functions' => ['use_admin'],
    ],
    'chat' => [
        'params' => ['id'],
    ],
    'chats' => [
        'params' => [],
    ],
    'conversations' => [
        'params' => [],
    ],
    'onlineimage' => [
        'params' => [],
        'uparams' => ['user_id', 'department'],
        'multiple_arguments' => ['department'],
    ],
    'extensions' => [
        'params' => [],
    ],
    'chatscount' => [
        'params' => [],
    ],
    'updatelastactivity' => [
        'params' => ['user_id'],
    ],
    'fetchchat' => [
        'params' => [],
    ],
    'editwebhook' => [
        'params' => [],
    ],
    'fetchchatmessages' => [
        'params' => [],
    ],
    'getmessages' => [
        'params' => [],
    ],
    'departaments' => [
        'params' => [],
    ],
    'isonlineuser' => [
        'params' => ['user_id'],
    ],
    'isonlinechat' => [
        'params' => ['chat_id'],
    ],
    'setoperatorstatus' => [
        'params' => [],
    ],
    'setinvisibilitystatus' => [
        'params' => [],
    ],
    'setonlinestatus' => [
        'params' => ['user_id', 'online'],
    ],
    'isonline' => [
        'params' => [],
    ],
    'isonlinedepartment' => [
        'params' => ['department_id'],
    ],
    'getusers' => [
        'params' => [],
    ],
    'onlineusers' => [
        'params' => [],
    ],
    'startchatwithoperator' => [
        'params' => ['id', 'initiator_user_id'],
    ],
    'groupsbyobject' => [
        'params' => ['object_id', 'type'],
    ],
    'groupsidbyobject' => [
        'params' => ['object_id', 'type'],
    ],
    'listofobjectid' => [
        'params' => ['user_id', 'type'],
    ],
    'bots' => [
        'params' => [],
    ],
    'user' => [
        'params' => ['id'],
    ],
    'notifications' => [
        'params' => ['token'],
    ],
    'user_departments' => [
        'params' => [],
    ],
    'lang' => [
        'params' => ['ns'],
    ],
    'bot' => [
        'params' => ['id'],
    ],
    'departments' => [
        'params' => [],
    ],
    'department' => [
        'params' => ['id'],
    ],
    'getuser' => [
        'params' => [],
    ],
    'login' => [
        'params' => [],
    ],
    'loginbytoken' => [
        'params' => [],
    ],
    'logout' => [
        'params' => [],
    ],
    'generateautologin' => [
        'params' => [],
    ],
    'swagger' => [
        'params' => [],
    ],
    'agentstatistic' => [
        'params' => [],
    ],
    'startchat' => [
        'params' => [],
        'uparams' => ['ua', 'operator', 'er', 'vid', 'hash_resume', 'sound', 'hash', 'offline', 'leaveamessage', 'department', 'priority', 'chatprefill', 'survey', 'prod', 'phash', 'pvhash'],
        'multiple_arguments' => ['department', 'ua', 'prod'],
    ],
    'addmsguser' => [
        'params' => [],
    ],
    'addmsgadmin' => [
        'params' => [],
    ],
    'file' => [
        'params' => ['id'],
    ],
    'setchatstatus' => [
        'params' => [],
    ],
    'campaignsconversions' => [
        'params' => [],
    ],
    'setnewvid' => [
        'params' => [],
    ],
    'chatcheckoperatormessage' => [
        'params' => [],
        'uparams' => ['tz', 'operator', 'theme', 'priority', 'vid', 'count_page', 'identifier', 'department', 'ua', 'survey', 'uactiv', 'wopen'],
        'multiple_arguments' => ['department', 'ua'],
    ],
    'updatechatattributes' => [
        'params' => [],
    ],
    'closechatasvisitor' => [
        'params' => [],
    ],
    'checkchatstatus' => [
        'params' => [],
        'uparams' => ['mode', 'theme', 'dot'],
    ],
    'surveychat' => [
        'params' => ['chat_id', 'survey_id'],
        'uparams' => [],
    ],
];

$FunctionList = [
    'use_admin' => ['explain' => 'Allow operator to manage REST API'],
    'use_direct_logins' => ['explain' => 'Allow operator use api directly with their username and password'],
    'object_api' => ['explain' => 'Allow operator to user objects api'],
    'updatelastactivity' => ['explain' => 'Allow operator update other operator last activity'],
    'file_download' => ['explain' => 'Allow operator to download file'],
    'list_extensions' => ['explain' => 'Allow operator to list extensions'],
    'survey' => ['explain' => 'Allow operator to work with survey'],
    'generateautologinall' => ['explain' => 'Allow operator to generate auto login for any account'],
];
