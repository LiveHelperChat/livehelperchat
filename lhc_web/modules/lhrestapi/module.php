<?php

$Module = array( "name" => "Live helper Chat REST API service");

$ViewList = array();

$ViewList['index'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['chats'] = array(
    'params' => array()
);

$ViewList['fetchchat'] = array(
    'params' => array()
);

$ViewList['fetchchatmessages'] = array(
    'params' => array()
);

$ViewList['getmessages'] = array(
    'params' => array()
);

$ViewList['departaments'] = array(
    'params' => array()
);

$ViewList['isonlineuser'] = array(
    'params' => array('user_id')
);

$ViewList['isonline'] = array(
    'params' => array()
);

$ViewList['isonlinedepartment'] = array(
    'params' => array('department_id')
);

$FunctionList = array();
$FunctionList['use_admin'] = array('explain' => 'Allow operator to manage REST API');

?>