<?php

$Module = array( "name" => "Departments configuration");

$ViewList = array();

$ViewList['departments'] = array(
    'params' => array(),
    'uparams' => array('visible_if_online','hidden','disabled','name','export'),
    'functions' => array( 'list' )
    );

$ViewList['new'] = array(
    'params' => array(),
    'functions' => array( 'create' )
);

$ViewList['edit'] = array(
    'params' => array('departament_id'),
    'uparams' => array('action'),
    'functions' => array( 'edit' )
);

$ViewList['clone'] = array(
    'params' => array('departament_id'),
    'functions' => array( 'edit' ),
    'uparams' => array('csfr'),
);

$ViewList['index'] = array(
    'params' => array(),
    'functions' => array( 'list' )
);

$ViewList['brands'] = array(
    'params' => array(),
    'functions' => array( 'managebrands' )
);

$ViewList['newbrand'] = array(
    'params' => array(),
    'functions' => array( 'managebrands' )
);

$ViewList['editbrand'] = array(
    'params' => array('id'),
    'functions' => array( 'managebrands' )
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
    'uparams' => array('action'),
    'functions' => array( 'managegroups' )
);

$ViewList['deletegroup'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'managegroups' )
);

$ViewList['deletebrand'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'managebrands' )
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
$FunctionList['see_all'] = array('explain' => 'Allow user to see all departments. Even if he does not have permission to see chats.');
$FunctionList['actworkflow'] = array('explain' => 'Allow user to change transfer workflow');
$FunctionList['actautoassignment'] = array('explain' => 'Allow user to change auto assignment');
$FunctionList['manageall'] = array('explain' => 'Allow user to manage all departments, not only assigned to him');
$FunctionList['managegroups'] = array('explain' => 'Allow user to manage all department groups, not only assigned to him');
$FunctionList['managesurvey'] = array('explain' => 'Allow operator to change department surveys');
$FunctionList['managealias'] = array('explain' => 'Allow operator to change department alias');
$FunctionList['managedesign'] = array('explain' => 'Allow operator to change design section');
$FunctionList['managebrands'] = array('explain' => 'Allow operator to manage brands');


?>