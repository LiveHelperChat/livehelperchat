<?php

$Module = array( "name" => "Mail archive module");

$ViewList = array();

$ViewList['archive'] = array(
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['newarchive'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$ViewList['configuration'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$ViewList['list'] = array(
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['scheduledpurge'] = array(
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['scheduledpurgedelete'] = array(
    'params' => array('schedule', 'class'),
    'uparams' => array('csfr'),
    'functions' => array( 'archive' )
);

$ViewList['listarchivemails'] = array(
    'params' => array('id'),
    'uparams' => array('is_external','ipp','timefromts','opened','phone','lang_ids','is_followup','sortby','conversation_status_ids','undelivered','view','has_attachment','mailbox_ids','conversation_id','subject','department_ids','department_group_ids','user_ids','group_ids','subject_id','wait_time_from','wait_time_till','conversation_id','nick','email','timefrom','timeto','user_id','export','conversation_status','hum','product_id','timefrom','timefrom_minutes','timefrom_hours','timeto', 'timeto_minutes', 'timeto_hours', 'department_group_id', 'group_id'),
    'functions' => array( 'archive' ),
    'multiple_arguments' => array(
        'department_ids',
        'department_group_ids',
        'user_ids',
        'group_ids',
        'bot_ids',
        'mailbox_ids',
        'conversation_status_ids',
        'lang_ids',
        'subject_id'
    )
);

$ViewList['edit'] = array(
    'params' => array('id'),
    'functions' => array( 'configuration' )
);

$ViewList['process'] = array(
    'params' => array('id'),
    'functions' => array( 'configuration' )
);

$ViewList['startarchive'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$ViewList['archivechats'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$FunctionList['archive'] = array('explain' => 'Allow user to use archive functionality');
$FunctionList['configuration'] = array('explain' => 'Allow user to configure archive');

?>