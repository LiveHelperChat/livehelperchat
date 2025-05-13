<?php

$Module = array( "name" => "Audit",
				 'variable_params' => true );

$ViewList = array();

$ViewList['configuration'] = array(
    'params' => array(),
    'uparams' => array('csfr','action','id'),
    'functions' => array( 'use' ),
);

$ViewList['loginhistory'] = array(
    'params' => array(),
    'uparams' => array('user_id'),
    'functions' => array( 'use' ),
);

$ViewList['debuginvitation'] = array(
    'params' => array('ouser_id','invitation_id','tag'),
    'functions' => array( 'use' ),
);

$ViewList['logrecord'] = array(
    'params' => array('id'),
    'functions' => array( 'log_preview' ),
);

$ViewList['logjserror'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array(  ),
);

$ViewList['test'] = array(
    'params' => array(),
    'uparams' => array()
);

$ViewList['copycurl'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'see_audit_system' ),
);

$FunctionList['use'] = array('explain' => 'Allow operator to configure audit module');
$FunctionList['log_preview'] = array('explain' => 'Allow operator to preview log record');
$FunctionList['see_system'] = array('explain' => 'Allow operator to see system status');
$FunctionList['see_audit_system'] = array('explain' => 'Allow operator to see audit system messages');
$FunctionList['ignore_view_actions'] = array('explain' => 'Do not log view actions from operator');
$FunctionList['see_op_actions'] = array('explain' => 'Allow operator to see other operator logged actions');

?>