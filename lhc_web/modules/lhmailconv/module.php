<?php

$Module = array( "name" => "Mail conversation module");

$ViewList = array();

$ViewList['index'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['mailbox'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['inlinedownload'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['previewmail'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['transfermail'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['apimaildownload'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['mailprint'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['mailprintcovnersation'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['view'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['single'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['apinoreplyrequired'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['apicloseconversation'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['apideleteconversation'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['loadmainconv'] = array(
    'params' => array('id'),
    'uparams' => array('mode'),
    'functions' => array( 'use_admin' )
);

$ViewList['saveremarks'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['mailhistory'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['conversations'] = array(
    'params' => array(),
    'uparams' => array('department_ids','department_group_ids','user_ids','group_ids','subject_id','wait_time_from','wait_time_till','conversation_id','nick','email','timefrom','timeto','user_id','xls','conversation_status','hum','product_id','timefrom','timefrom_minutes','timefrom_hours','timeto', 'timeto_minutes', 'timeto_hours', 'department_group_id', 'group_id'),
    'functions' => array( 'use_admin' ),
    'multiple_arguments' => array(
        'department_ids',
        'department_group_ids',
        'user_ids',
        'group_ids',
        'bot_ids',
    )
);

$ViewList['newmailbox'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['syncmailbox'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['matchingrules'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['newmatchrule'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['editmailbox'] = array(
    'params' => array('id'),
    'uparams' => array('action'),
    'functions' => array( 'use_admin' )
);

$ViewList['editmatchrule'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['deletemailbox'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['deleteconversation'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['deleteresponsetemplate'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['deletematchingrule'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['responsetemplates'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['newresponsetemplate'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['editresponsetemplate'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['notifications'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$FunctionList = array();
$FunctionList['use_admin'] = array('explain' => 'Permission to use mail conversation module');

?>