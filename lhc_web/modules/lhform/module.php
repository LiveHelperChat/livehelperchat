<?php

$Module = array( "name" => "Forms module");

$ViewList = array();

$ViewList['htmlcode'] = array(
		'script' => 'htmlcode.php',
		'params' => array(),
		'functions' => array( 'manage_fm' )
);

$ViewList['index'] = array(
		'script' => 'index.php',
		'params' => array(),
		'functions' => array( 'manage_fm' )
);

$ViewList['fill'] = array(
		'script' => 'fill.php',
		'params' => array('form_id'),
		'functions' => array(  )
);

$FunctionList = array();
$FunctionList['manage_fm'] = array('explain' => 'Allow user to manage form module');

?>