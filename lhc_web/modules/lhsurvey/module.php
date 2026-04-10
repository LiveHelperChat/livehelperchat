<?php

$Module = ['name' => 'Survey'];

$ViewList = [
    'fillwidget' => [
        'params' => [],
        'uparams' => ['chatid', 'ua', 'switchform', 'operator', 'theme', 'vid', 'sound', 'hash', 'hash_resume', 'mode', 'offline', 'leaveamessage', 'department', 'priority', 'chatprefill', 'survey', 'eclose'],
    ],
    'fill' => [
        'params' => [],
        'uparams' => ['chatid', 'ua', 'switchform', 'operator', 'theme', 'vid', 'sound', 'hash', 'hash_resume', 'mode', 'offline', 'leaveamessage', 'department', 'priority', 'chatprefill', 'survey', 'eclose'],
    ],
    'fillinline' => [
        'params' => [],
        'uparams' => [],
    ],
    'backtochat' => [
        'params' => ['chat_id', 'hash', 'survey'],
        'uparams' => [],
    ],
    'themesurvey' => [
        'params' => ['theme'],
        'uparams' => [],
    ],
    'isfilled' => [
        'params' => ['chat_id', 'hash', 'survey'],
        'uparams' => [],
    ],
    'choosesurvey' => [
        'params' => ['chat_id', 'survey_id'],
        'uparams' => [],
        'functions' => ['redirect_to_survey'],
    ],
    'collected' => [
        'params' => ['survey_id'],
        'uparams' => ['timefrom', 'timeto', 'department_id', 'user_id', 'print', 'xls', 'xlslist', 'xml', 'json', 'group_results', 'minimum_chats', 'max_stars_1', 'max_stars_2', 'max_stars_3', 'max_stars_4', 'max_stars_5', 'question_options_1', 'question_options_2', 'question_options_3', 'question_options_4', 'question_options_5', 'department_ids', 'department_group_ids', 'user_ids', 'group_ids', 'csvlist', 'action', 'id', 'csfr'],
        'functions' => ['list_survey'],
        'multiple_arguments' => ['max_stars_1', 'max_stars_2', 'max_stars_3', 'max_stars_4', 'max_stars_5', 'department_ids', 'department_group_ids', 'user_ids', 'group_ids'],
    ],
    'collecteditem' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['list_survey'],
    ],
];

$FunctionList = [
    'list_survey' => ['explain' => 'Allow operator to view survey statistic'],
    'manage_survey' => ['explain' => 'Allow operator to edit survey'],
    'delete_survey' => ['explain' => 'Allow operator to delete survey'],
    'delete_collected' => ['explain' => 'Allow operator to delete collected items'],
    'redirect_to_survey' => ['explain' => 'Allow operator to redirect visitor to survey'],
];
