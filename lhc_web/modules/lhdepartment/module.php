<?php

$Module = array( "name" => "Departments configuration");

$ViewList = array();

$ViewList['departments'] = array(
    'params' => array(),
    'uparams' => array('visible_if_online','hidden','disabled','name'),
    'functions' => array( 'list' )
    );

$ViewList['new'] = array(
    'params' => array(),
    'functions' => array( 'create' )
);

$ViewList['edit'] = array(
    'params' => array('departament_id'),
    'functions' => array( 'edit' )
);

$ViewList['index'] = array(
    'params' => array(),
    'functions' => array( 'list' )
);

$ViewList['group'] = array(
    'params' => array(),
    'functions' => array( 'managegroups' )
);

$ViewList['limitgroup'] = array(
    'params' => array(),
    'functions' => array( 'managegroups' )
);

$ViewList['newgroup'] = array(
    'params' => array(),
    'functions' => array( 'managegroups' )
);

$ViewList['newlimitgroup'] = array(
    'params' => array(),
    'functions' => array( 'managegroups' )
);

$ViewList['editlimitgroup'] = array(
    'params' => array('id'),
    'functions' => array( 'managegroups' )
);

$ViewList['editgroup'] = array(
    'params' => array('id'),
    'functions' => array( 'managegroups' )
);

$ViewList['deletegroup'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'managegroups' )
);

$ViewList['deletelimitgroup'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'managegroups' )
);

$FunctionList['list'] = array('explain' => 'Access to list departments');
$FunctionList['create'] = array('explain' => 'Permission to create a new department');
$FunctionList['edit'] = array('explain' => 'Permission to edit department');
$FunctionList['delete'] = array('explain' => 'Permission to delete department');
$FunctionList['selfedit'] = array('explain' => 'Allow user to choose his departments');
$FunctionList['actworkflow'] = array('explain' => 'Allow user to change transfer workflow');
$FunctionList['actautoassignment'] = array('explain' => 'Allow user to change auto assignment');
$FunctionList['manageall'] = array('explain' => 'Allow user to manage all departments, not only assigned to him');
$FunctionList['managegroups'] = array('explain' => 'Allow user to manage all departments, not only assigned to him');

?>