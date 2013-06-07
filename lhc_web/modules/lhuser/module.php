<?php

$Module = array( "name" => "Users, groups management");

$ViewList = array();

$ViewList['login'] = array(
    'script' => 'login.php',
    'params' => array()
    );

$ViewList['logout'] = array(
    'script' => 'logout.php',
    'params' => array()
    );

$ViewList['account'] = array(
    'script' => 'logout.php',
    'params' => array(),
    'functions' => array( 'selfedit' )
    );

$ViewList['userlist'] = array(
    'script' => 'userlist.php',
    'params' => array(),
    'functions' => array( 'userlist' )
    );

$ViewList['grouplist'] = array(
    'script' => 'grouplist.php',
    'params' => array(),
    'functions' => array( 'grouplist' )
    );

$ViewList['edit'] = array(
    'script' => 'edit.php',
    'params' => array('user_id'),
    'functions' => array( 'edituser' )
    );

$ViewList['delete'] = array(
    'script' => 'delete.php',
    'params' => array('user_id'),
    'functions' => array( 'deleteuser' )
    );

$ViewList['new'] = array(
    'script' => 'new.php',
    'params' => array(),
    'functions' => array( 'createuser' )
    );

$ViewList['newgroup'] = array(
    'script' => 'newgroup.php',
    'params' => array(),
    'functions' => array( 'creategroup', 'editgroup' )
    );

$ViewList['editgroup'] = array(
    'script' => 'editgroup.php',
    'params' => array('group_id'),
    'functions' => array( 'editgroup' )
    );

$ViewList['groupassignuser'] = array(
    'script' => 'groupassignuser.php',
    'params' => array('group_id'),
    'functions' => array( 'groupassignuser' )
    );

$ViewList['deletegroup'] = array(
    'script' => 'deletegroup.php',
    'params' => array('group_id'),
    'functions' => array( 'deletegroup' )
    );

$ViewList['forgotpassword'] = array(
    'script' => 'forgotpassword.php',
    'params' => array(),
);

$ViewList['remindpassword'] = array(
    'script' => 'remindpassword.php',
    'params' => array('hash'),
);

$ViewList['setsetting'] = array (
		'script' => 'setsetting.php',
		'params' => array('identifier','value')
);

$ViewList['setsettingajax'] = array (
		'script' => 'setsettingajax.php',
		'params' => array('identifier','value')
);

$ViewList['setoffline'] = array (
		'script' => 'setoffline.php',
		'functions' => array( 'changeonlinestatus' ),
		'params' => array('status')
);

$FunctionList['groupassignuser'] = array('explain' => 'Allow user to assign user to group');
$FunctionList['editgroup'] = array('explain' => 'Allow user to edit group');
$FunctionList['creategroup'] = array('explain' => 'Allow user to create group');
$FunctionList['deletegroup'] = array('explain' => 'Allow user to delete group');
$FunctionList['createuser'] = array('explain' => 'Allow user to create another user');
$FunctionList['deleteuser'] = array('explain' => 'Allow user to delete another user');
$FunctionList['edituser'] = array('explain' => 'Allow user to edit another user');
$FunctionList['grouplist'] = array('explain' => 'Allow user to list group');
$FunctionList['userlist'] = array('explain' => 'Allow user to list users');
$FunctionList['selfedit'] = array('explain' => 'Allow user to edit his own data');
$FunctionList['editdepartaments'] = array('explain' => 'Allow user to edit his responsible departaments');
$FunctionList['userlistonline'] = array('explain' => 'Allow user to see logged operators list');
$FunctionList['changeonlinestatus'] = array('explain' => 'Allow user to change his online status');

?>