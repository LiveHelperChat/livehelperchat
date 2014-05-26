<?php

$Module = array( "name" => "Documents sharer");

$ViewList = array();

$ViewList['index'] = array(
		'script' => 'index.php',
		'params' => array(),
		'functions' => array( 'manage_dc' )
);

$ViewList['list'] = array(
		'params' => array(),
		'functions' => array( 'manage_dc' )
);

$ViewList['new'] = array(
		'params' => array('id'),
		'functions' => array( 'manage_dc' )
);

$ViewList['edit'] = array(
		'params' => array('id'),
		'functions' => array( 'manage_dc' )
);

$ViewList['download'] = array(
		'params' => array('id'),
		'functions' => array( )
);

$ViewList['view'] = array(
		'params' => array('id'),
		'functions' => array( )
);

$ViewList['previewimages'] = array(
		'params' => array('id'),
		'functions' => array( 'manage_dc' )
);

$FunctionList = array();
$FunctionList['manage_dc'] = array('explain' => 'Allow user to manage documents sharer');

?>