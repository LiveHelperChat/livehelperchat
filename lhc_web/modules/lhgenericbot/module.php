<?php

$Module = array( "name" => "Generic Bot",
				 'variable_params' => true );

$ViewList = array();

$ViewList['bot'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['initbot'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['testpattern'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['commands'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['newcommand'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['editcommand'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['help'] = array(
    'params' => array('context'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['addpayload'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['loadusecases'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['downloadbot'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['downloadbotgroup'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['import'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['botimportgroup'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['triggersbybot'] = array(
    'params' => array('id','trigger_id'),
    'uparams' => array('preview','element','asarg'),
    'functions' => array( 'use_operator' )
);

$ViewList['getpayloads'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['argumenttemplates'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['nodegroups'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['list'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['listrestapi'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['restapimethods'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['listexceptions'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['listtranslations'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_individualization' )
);

$ViewList['listtranslationsitems'] = array(
    'params' => array(),
    'uparams' => array('group_id'),
    'functions' => array( 'use_individualization' )
);

$ViewList['new'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['newrestapi'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['newexception'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['newtrgroup'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_individualization' )
);

$ViewList['newtritem'] = array(
    'params' => array(),
    'uparams' => array('group_id'),
    'functions' => array( 'use_individualization' )
);

$ViewList['edittrgroup'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use_individualization' )
);

$ViewList['edittritem'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use_individualization' )
);

$ViewList['editrestapi'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['editexception'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['edit'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['delete'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use' )
);

$ViewList['deletecommand'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use' )
);

$ViewList['deleterestapi'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use' )
);

$ViewList['deleteexception'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use' )
);

$ViewList['deletetritem'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_individualization' )
);

$ViewList['deletetrgroup'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_individualization' )
);

$ViewList['nodegrouptriggers'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['addgroup'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['updategroup'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['updatetrigger'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['savetrigger'] = array(
    'params' => array(),
    'uparams' => array('method'),
    'functions' => array( 'use' )
);

$ViewList['addtrigger'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['addtriggerevent'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['deletetriggerevent'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['deletegroup'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['updatetriggerevent'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['removetrigger'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['maketriggercopy'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['setdefaulttrigger'] = array(
    'params' => array('id','default'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['setinprogresstrigger'] = array(
    'params' => array('id','default'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['setasargument'] = array(
    'params' => array('id','default'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['settriggergroup'] = array(
    'params' => array('id','group_id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['setdefaultunknowntrigger'] = array(
    'params' => array('id','default'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['setdefaultunknownbtntrigger'] = array(
    'params' => array('id','default'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['setdefaultalwaystrigger'] = array(
    'params' => array('id','default'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['nodetriggeractions'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['notifications'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use_operator' )
);

$ViewList['buttonclicked'] = array(
    'params' => array('chat_id', 'hash' ),
    'uparams' => array('type','theme')
);

$ViewList['updatebuttonclicked'] = array(
    'params' => array('chat_id', 'hash'),
    'uparams' => array()
);

$ViewList['chatactions'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'see_actions' )
);

$ViewList['conditions'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'manage_conditions' )
);

$ViewList['newcondition'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'manage_conditions' )
);

$ViewList['editcondition'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'manage_conditions' )
);

$ViewList['deletecondition'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'manage_conditions' )
);

$FunctionList['use'] = array('explain' => 'General permission to use generic bot module');
$FunctionList['use_operator'] = array('explain' => 'Allow operator to change bot notifications settings');
$FunctionList['see_actions'] = array('explain' => 'Allow operator to see chat actions');
$FunctionList['use_individualization'] = array('explain' => 'Allow operator to change bot individualisation');
$FunctionList['manage_conditions'] = array('explain' => 'Allow operator to manage conditions templates');

?>