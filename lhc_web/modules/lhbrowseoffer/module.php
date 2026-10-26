<?php

$Module = array( "name" => "Browse offers");

$ViewList = array();

$ViewList['getstatus'] = array(
		'params' => array(),
		'uparams' => array('size','units','identifier','height','canreopen','showoverlay','timeout')
);

$ViewList['htmlcode'] = array(
		'params' => array(),
		'functions' => array( 'manage_bo' )
);

$ViewList['index'] = array(
		'params' => array(),
		'functions' => array( 'manage_bo' )
);

$ViewList['widget'] = array(
		'params' => array('hash')
);

$ViewList['widgetclosed'] = array(
		'params' => array('id')
);

$ViewList['addhit'] = array(
		'params' => array('hash')
);

$FunctionList = array();
$FunctionList['manage_bo'] = array('explain' => 'Allow user to manage browse offers');

?>