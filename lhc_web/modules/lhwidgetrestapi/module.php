<?php

$Module = ['name' => 'Live helper Chat REST API service'];

$ViewList = [
    'chooselanguage' => [
        'params' => [],
        'uparams' => ['id', 'hash'],
    ],
    'setsiteaccess' => [
        'params' => [],
        'uparams' => ['id', 'hash', 'vid'],
    ],
    'getproducts' => [
        'params' => ['id', 'product_id'],
    ],
    'avatar' => [
        'params' => ['id'],
    ],
    'settings' => [
        'params' => [],
        'uparams' => ['department', 'ua', 'identifier'],
        'multiple_arguments' => ['department', 'ua'],
    ],
    'offlinesettings' => [
        'params' => [],
        'uparams' => ['ua', 'switchform', 'operator', 'theme', 'vid', 'sound', 'hash', 'hash_resume', 'mode', 'offline', 'leaveamessage', 'department', 'priority', 'chatprefill', 'survey', 'sdemo', 'prod', 'phash', 'pvhash', 'fullheight', 'ajaxmode'],
        'multiple_arguments' => ['department', 'ua', 'prod'],
    ],
    'updatejs' => [
        'params' => [],
        'uparams' => [],
        'multiple_arguments' => [],
    ],
    'lang' => [
        'params' => [],
        'uparams' => [],
        'multiple_arguments' => [],
    ],
    'onlinesettings' => [
        'params' => [],
        'uparams' => ['ua', 'switchform', 'operator', 'theme', 'vid', 'sound', 'hash', 'hash_resume', 'mode', 'offline', 'leaveamessage', 'department', 'priority', 'chatprefill', 'survey', 'sdemo', 'prod', 'phash', 'pvhash', 'fullheight', 'ajaxmode'],
        'multiple_arguments' => ['department', 'ua', 'prod'],
    ],
    'submitoffline' => [
        'params' => [],
        'uparams' => ['ua', 'switchform', 'operator', 'theme', 'vid', 'sound', 'hash', 'hash_resume', 'mode', 'offline', 'leaveamessage', 'department', 'priority', 'chatprefill', 'survey', 'sdemo', 'prod', 'phash', 'pvhash', 'fullheight', 'ajaxmode'],
        'multiple_arguments' => ['department', 'ua', 'prod'],
    ],
    'submitonline' => [
        'params' => [],
        'uparams' => ['ua', 'switchform', 'operator', 'theme', 'vid', 'sound', 'hash', 'hash_resume', 'mode', 'offline', 'leaveamessage', 'department', 'priority', 'chatprefill', 'survey', 'sdemo', 'prod', 'phash', 'pvhash', 'fullheight', 'ajaxmode'],
        'multiple_arguments' => ['department', 'ua', 'prod'],
    ],
    'initchat' => [
        'params' => [],
        'uparams' => [],
        'multiple_arguments' => [],
    ],
    'uisettings' => [
        'params' => [],
        'uparams' => [],
        'multiple_arguments' => [],
    ],
    'fetchmessages' => [
        'params' => [],
        'uparams' => [],
    ],
    'getmessagesnippet' => [
        'params' => [],
        'uparams' => [],
    ],
    'fetchmessage' => [
        'params' => [],
        'uparams' => [],
    ],
    'logconversions' => [
        'params' => [],
        'uparams' => ['vid'],
    ],
    'addmsguser' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => ['mode'],
    ],
    'sendmailsettings' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => ['action'],
    ],
    'chatcheckstatus' => [
        'params' => [],
        'uparams' => ['status', 'department', 'vid', 'uactiv', 'wopen', 'uaction', 'hash', 'hash_resume', 'dot', 'hide_offline', 'isproactive'],
        'multiple_arguments' => ['department'],
    ],
    'checkchatstatus' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => ['mode', 'theme', 'dot'],
    ],
    'loadsound' => [
        'params' => ['sound'],
        'uparams' => [],
    ],
    'themepage' => [
        'params' => ['theme'],
        'uparams' => [],
    ],
    'themeneedhelp' => [
        'params' => ['theme'],
        'uparams' => [],
    ],
    'theme' => [
        'params' => ['theme'],
        'uparams' => ['p'],
    ],
    'themestatus' => [
        'params' => ['theme'],
        'uparams' => [],
    ],
    'checkinvitation' => [
        'params' => [],
        'uparams' => [],
    ],
    'getinvitation' => [
        'params' => [],
        'uparams' => [],
    ],
    'proactiveonclick' => [
        'params' => ['id'],
        'uparams' => [],
    ],
    'screensharesettings' => [
        'params' => [],
        'uparams' => [],
    ],
    'executejs' => [
        'params' => [],
        'uparams' => ['id', 'hash', 'ext', 'dep'],
        'multiple_arguments' => ['dep'],
    ],
];

$FunctionList = [
    'use_admin' => ['explain' => 'Allow operator to manage REST API'],
];
