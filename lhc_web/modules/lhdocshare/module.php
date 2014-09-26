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

$ViewList['embedcode'] = array(
		'params' => array('id'),
		'functions' => array( 'manage_dc' )
);

$ViewList['download'] = array(
		'params' => array('id'),
		'functions' => array()	
);

$ViewList['downloadpdf'] = array(
		'params' => array('id'),
		'functions' => array()	
);

$ViewList['view'] = array(
		'params' => array('id'),
		'functions' => array( )
);

$ViewList['configuration'] = array(
		'params' => array(),
		'functions' => array( 'change_configuration' )
);

$ViewList['previewimages'] = array(
		'params' => array('id'),
		'functions' => array( 'manage_dc' )
);

$ViewList['delete'] = array(
		'params' => array('id'),
		'uparams' => array('csfr'),
		'functions' => array( 'deletedoc' )
);

$ViewList['docwidget'] = array(
		'params' => array('doc_id'),
		'functions' => array()
);

$ViewList['embed'] = array(
		'params' => array('doc_id'),
		'uparams' => array('height'),
		'functions' => array()
);

$FunctionList = array();
$FunctionList['manage_dc'] = array('explain' => 'Allow user to manage documents sharer module');
$FunctionList['deletedoc'] = array('explain' => 'Allow user to delete his own documents');
$FunctionList['deleteglobaldoc'] = array('explain' => 'Allow user to delete all documents');
$FunctionList['change_configuration'] = array('explain' => 'Allow user to change documents sharer configuration');

?>