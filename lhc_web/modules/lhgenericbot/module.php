<?php

$Module = ['name' => 'Generic Bot', 'variable_params' => true];

$ViewList = [
    'bot' => [
        'params' => ['id'],
        'uparams' => ['type'],
        'functions' => ['use'],
    ],
    'initbot' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'testpattern' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['test_pattern'],
    ],
    'commands' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'newcommand' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'triggersearch' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'usecases' => [
        'params' => ['type', 'id'],
        'uparams' => ['arg1', 'arg2'],
        'functions' => ['use_cases'],
    ],
    'editcommand' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'help' => [
        'params' => ['context'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'addpayload' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'loadusecases' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'downloadbot' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'downloadbotgroup' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'import' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'botimportgroup' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'triggersbybot' => [
        'params' => ['id', 'trigger_id'],
        'uparams' => ['preview', 'element', 'asarg'],
        'functions' => ['use_operator'],
    ],
    'getpayloads' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'argumenttemplates' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'nodegroups' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'list' => [
        'params' => [],
        'uparams' => ['name'],
        'functions' => ['use'],
    ],
    'listrestapi' => [
        'params' => [],
        'uparams' => ['name'],
        'functions' => ['use'],
    ],
    'restapimethods' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'listexceptions' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'listtranslations' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use_individualization'],
    ],
    'listtranslationsitems' => [
        'params' => [],
        'uparams' => ['group_id'],
        'functions' => ['use_individualization'],
    ],
    'new' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'newrestapi' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'newexception' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'newtrgroup' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use_individualization'],
    ],
    'newtritem' => [
        'params' => [],
        'uparams' => ['group_id'],
        'functions' => ['use_individualization'],
    ],
    'edittrgroup' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use_individualization'],
    ],
    'edittritem' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use_individualization'],
    ],
    'editrestapi' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'editexception' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'edit' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'delete' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use'],
    ],
    'deletecommand' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use'],
    ],
    'deleterestapi' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use'],
    ],
    'deleteexception' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use'],
    ],
    'deletetritem' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_individualization'],
    ],
    'deletetrgroup' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_individualization'],
    ],
    'nodegrouptriggers' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'addgroup' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'updategroup' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'updatetrigger' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'savetrigger' => [
        'params' => [],
        'uparams' => ['method'],
        'functions' => ['use'],
    ],
    'addtrigger' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'addtriggerevent' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'deletetriggerevent' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'deletegroup' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'updatetriggerevent' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'removetrigger' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'maketriggercopy' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'setdefaulttrigger' => [
        'params' => ['id', 'default'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'setinprogresstrigger' => [
        'params' => ['id', 'default'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'settriggerposition' => [
        'params' => ['id', 'pos'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'setasargument' => [
        'params' => ['id', 'default'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'settriggergroup' => [
        'params' => ['id', 'group_id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'setdefaultunknowntrigger' => [
        'params' => ['id', 'default'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'setdefaultunknownbtntrigger' => [
        'params' => ['id', 'default'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'setdefaultalwaystrigger' => [
        'params' => ['id', 'default'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'nodetriggeractions' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use'],
    ],
    'notifications' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use_operator'],
    ],
    'buttonclicked' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => ['type', 'theme'],
    ],
    'updatebuttonclicked' => [
        'params' => ['chat_id', 'hash'],
        'uparams' => [],
    ],
    'chatactions' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['see_actions'],
    ],
    'conditions' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['manage_conditions'],
    ],
    'newcondition' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['manage_conditions'],
    ],
    'editcondition' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['manage_conditions'],
    ],
    'deletecondition' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['manage_conditions'],
    ],
];

$FunctionList = [
    'use' => ['explain' => 'General permission to use generic bot module'],
    'use_operator' => ['explain' => 'Allow operator to change bot notifications settings'],
    'see_actions' => ['explain' => 'Allow operator to see chat actions'],
    'use_individualization' => ['explain' => 'Allow operator to change bot individualisation'],
    'manage_conditions' => ['explain' => 'Allow operator to manage conditions templates'],
    'use_cases' => ['explain' => 'Allow operator see use cases of the object'],
    'test_pattern' => ['explain' => 'Allow operator see use cases of the object'],
];
