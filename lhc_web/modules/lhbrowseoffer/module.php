<?php

$Module = array( "name" => "Browse offers");

$ViewList = array();

$ViewList['getstatus'] = array(
		'script' => 'getstatus.php',
		'params' => array(),
		'functions' => array( ),
		'uparams' => array('size','units','identifier','height','canreopen','showoverlay','timeout')
);

$ViewList['htmlcode'] = array(
		'script' => 'htmlcode.php',
		'params' => array(),
		'functions' => array( 'manage_bo' )
);

$ViewList['index'] = array(
		'script' => 'index.php',
		'params' => array(),
		'functions' => array( 'manage_bo' )
);

$ViewList['widget'] = array(
		'script' => 'widget.php',
		'params' => array('hash'),
		'functions' => array( )
);

$ViewList['widgetclosed'] = array(
		'script' => 'widgetclosed.php',
		'params' => array('id'),
		'functions' => array( )
);

$ViewList['addhit'] = array(
		'script' => 'addhit.php',
		'params' => array('hash'),
		'functions' => array( )
);

$FunctionList = array();
$FunctionList['manage_bo'] = array('explain' => 'Allow user to manage browse offers');

?>