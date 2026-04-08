<?php

$Module = ['name' => 'Files module'];

$ViewList = [
    'configuration' => [
        'params' => [],
        'functions' => ['use'],
    ],
    'uploadfile' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => [],
    ],
    'fileoptions' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => [],
    ],
    'uploadfileonline' => [
        'params' => ['vid'],
        'uparams' => [],
    ],
    'chatfileslist' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use_operator'],
    ],
    'onlinefileslist' => [
        'params' => ['online_user_id'],
        'uparams' => [],
        'functions' => ['use_operator'],
    ],
    'useronlinefileslist' => [
        'params' => ['vid'],
        'uparams' => [],
        'functions' => [],
    ],
    'removepreview' => [
        'params' => [],
        'uparams' => [],
    ],
    'downloadfile' => [
        'params' => ['file_id', 'hash'],
        'uparams' => ['inline', 'vhash', 'vts'],
    ],
    'verifyaccess' => [
        'params' => ['file_id', 'hash'],
        'uparams' => ['reverify'],
        'functions' => ['verify_file'],
    ],
    'uploadfileadmin' => [
        'params' => ['chat_id'],
        'uparams' => [],
        'functions' => ['use_operator'],
    ],
    'uploadfileadminonlineuser' => [
        'params' => ['online_user_id'],
        'uparams' => [],
        'functions' => ['use_operator'],
    ],
    'new' => [
        'params' => [],
        'uparams' => ['mode', 'persistent'],
        'functions' => ['upload_new_file'],
    ],
    'attatchfile' => [
        'params' => ['chat_id'],
        'uparams' => ['user_id'],
        'functions' => ['use_operator'],
    ],
    'attatchfileimg' => [
        'params' => [],
        'uparams' => ['persistent', 'user_id', 'visitor', 'upload_name', 'replace', 'file_id'],
        'functions' => ['use_operator'],
    ],
    'attatchfilemail' => [
        'params' => [],
        'uparams' => ['mode', 'user_id'],
        'functions' => ['use_operator'],
    ],
    'list' => [
        'params' => [],
        'uparams' => ['chat_id', 'user_ids', 'user_id', 'visitor', 'persistent', 'upload_name'],
        'functions' => ['file_list'],
        'multiple_arguments' => ['user_ids'],
    ],
    'listmail' => [
        'params' => [],
        'uparams' => ['conversation_id', 'message_id', 'user_id', 'visitor', 'persistent', 'upload_name'],
        'functions' => ['file_list_mail'],
    ],
    'editmail' => [
        'params' => ['file_id'],
        'functions' => ['file_list_mail'],
    ],
    'delete' => [
        'params' => ['file_id'],
        'uparams' => ['csfr'],
        'functions' => ['file_delete'],
    ],
    'edit' => [
        'params' => ['file_id'],
        'uparams' => [],
        'functions' => ['use_operator'],
    ],
    'deletechatfile' => [
        'params' => ['file_id'],
        'uparams' => ['csfr'],
        'functions' => ['file_delete_chat'],
    ],
    'storescreenshot' => [
        'params' => [],
        'uparams' => ['vid', 'hash', 'hash_resume'],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'Allow user to configure files upload'],
    'use_operator' => ['explain' => 'Allow operators to send files to visitor'],
    'upload_new_file' => ['explain' => 'Allow operator to upload new file'],
    'file_list' => ['explain' => 'Allow operators to list all uploaded files'],
    'file_delete' => ['explain' => 'Allow operators to delete all files'],
    'file_delete_chat' => ['explain' => 'Allow operators to delete their chat files'],
    'download_unverified' => ['explain' => 'Allow operators to download unverified files'],
    'download_verified' => ['explain' => 'Allow operators to download verified, but sensitive files'],
    'verify_file' => ['explain' => 'Allow to verify access to files'],
    'file_list_mail' => ['explain' => 'Allow to list mail messages files'],
];
