<?php

$Module = array( "name" => "XMPP module configuration");

$ViewList = array();

$ViewList['configuration'] = array(
    'params' => array(),
    'uparams' => array('gtalkoauth'),
	'functions' => array( 'configurexmp' )
);

$FunctionList = array();
$FunctionList['configurexmp'] = array('explain' => 'Allow user to configure XMPP');

?>