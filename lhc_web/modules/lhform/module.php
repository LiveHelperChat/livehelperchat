<?php

$Module = array( "name" => "Forms module");

$ViewList = array();

$ViewList['index'] = array(
		'params' => array(),
		'functions' => array( 'manage_fm' )
);

$ViewList['fill'] = array(
		'params' => array('form_id'),
		'functions' => array(  )
);

$ViewList['collected'] = array(
		'params' => array('form_id'),
		'uparams' => array('action','id','csfr'),
		'functions' => array(  'manage_fm' )
);

$ViewList['viewcollected'] = array(
		'params' => array('collected_id'),
		'functions' => array(  'manage_fm' )
);

$ViewList['embedcode'] = array(
		'params' => array(),
		'functions' => array('generate_js')
);

/*
 * XLS file with all files
*
* */
$ViewList['downloadcollected'] = array(
		'params' => array('form_id'),
		'functions' => array(  'manage_fm' )
);

/*
 * zip file with XLS file and documents
 *  
 * */
$ViewList['downloaditem'] = array(
		'params' => array('collected_id'),
		'functions' => array('manage_fm')
);

/*
 * single attribute download
 * 
 * */
$ViewList['download'] = array(
		'params' => array('collected_id','attr_name'),
		'functions' => array('manage_fm')
);

$ViewList['embed'] = array(	
		'params' => array('form_id'),
		'functions' => array()
);

$ViewList['formwidget'] = array(
		'params' => array('form_id')
);

$FunctionList = array();
$FunctionList['manage_fm'] = array('explain'   => 'Allow user to manage form module');
$FunctionList['delete_fm'] = array('explain'   => 'Allow user to delete forms');
$FunctionList['generate_js'] = array('explain' => 'Allow user to generate page embed js');
