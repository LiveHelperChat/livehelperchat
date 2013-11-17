<?php

$Module = array( "name" => "XMPP module configuration");

$ViewList = array();

$ViewList['configuration'] = array(
    'params' => array(),
	'functions' => array( 'configurexmp' )
);

$FunctionList = array();
$FunctionList['configurexmp'] = array('explain' => 'Allow user to configure XMPP');

?>