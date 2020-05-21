<?php

$Module = array( "name" => "Group chats module",
    'variable_params' => true );

$ViewList = array();

$ViewList['loadgroupchat'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
    'multiple_arguments' => array()
);

$ViewList['loadpreviousmessages'] = array(
    'params' => array('id','msg_id'),
    'uparams' => array('initial'),
    'functions' => array( 'use' ),
    'multiple_arguments' => array()
);

$ViewList['inviteoperator'] = array(
    'params' => array('id','op_id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
    'multiple_arguments' => array()
);

$ViewList['startchatwithoperator'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
    'multiple_arguments' => array()
);

$ViewList['leave'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
    'multiple_arguments' => array()
);

$ViewList['addmessage'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
    'multiple_arguments' => array()
);

$ViewList['sync'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' ),
    'multiple_arguments' => array()
);

$ViewList['list'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'manage' )
);

$ViewList['edit'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'manage' )
);

$ViewList['edit'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'manage' )
);

$ViewList['delete'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'manage' )
);

$ViewList['new'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'manage' )
);

$ViewList['newgroupajax'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['searchoperator'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['cancelinvite'] = array(
    'params' => array('id','op_id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$FunctionList['use'] = array('explain' => 'Allow operator to use private/public groups');
$FunctionList['manage'] = array('explain' => 'Permission to manage group chat module');
$FunctionList['public_chat'] = array('explain' => 'Allow operator to create a public group chat');

?>