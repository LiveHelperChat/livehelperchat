<?php

$Module = array( "name" => "Permissions configuration");

$ViewList = array();

$ViewList['roles'] = array(
    'script' => 'roles.php',
    'params' => array(),
    'functions' => array( 'list' )
);

$ViewList['newrole'] = array(
    'script' => 'newrole.php',
    'params' => array(),
    'functions' => array( 'new' )
);

$ViewList['editrole'] = array(
    'script' => 'editrole.php',
    'params' => array('role_id'),
    'functions' => array( 'edit' )
);

$ViewList['getpermissionsummary'] = array(
    'params' => array('user_id'),
    'functions' => array( 'see_permissions' )
);

$ViewList['request'] = array(
    'params' => array('permissions'),
    'functions' => array( 'see_permissions' )
);

$ViewList['modulefunctions'] = array(
    'script' => 'modulefunctions.php',
    'params' => array('module_path'),
    'functions' => array( 'edit' )
);

$ViewList['deleterole'] = array(
    'script' => 'deleterole.php',
    'params' => array('role_id'),
    'uparams' => array('csfr'),
    'functions' => array( 'delete' )
);

$ViewList['groupassignrole'] = array(
    'script' => 'groupassignrole.php',
    'params' => array('group_id'),
    'functions' => array( 'delete' )
);

$ViewList['roleassigngroup'] = array(
    'script' => 'roleassigngroup.php',
    'params' => array('role_id'),
    'functions' => array( 'delete' )
);

$FunctionList['edit'] = array('explain' => 'Access to edit role');
$FunctionList['delete'] = array('explain' => 'Access to delete role');
$FunctionList['list'] = array('explain' => 'Access to list roles');
$FunctionList['new'] = array('explain' => 'Access to create new role');
$FunctionList['see_permissions'] = array('explain' => 'Allow operator to see his permissions');
$FunctionList['see_permissions_users'] = array('explain' => 'Allow operator to see all users permissions');

?>