<?php

$Module = array( "name" => "Forms module");

$ViewList = array();

$ViewList['fill'] = array(
		'params' => array('form_id'),
		'uparams' => array('collected_id')
);

$ViewList['collected'] = array(
		'params' => array('form_id'),
		'uparams' => array('creator_user_ids','evaluated_user_ids','chat_time','action','id','csfr','department_ids','user_ids','timefrom','timeto','timefrom_hours','timefrom_minutes','timefrom_seconds','timeto_hours','timeto_minutes','timeto_seconds'),
		'multiple_arguments' => array(
			'department_ids',
			'user_ids',
			'creator_user_ids',
			'evaluated_user_ids'
		),
		'functions' => array(  'view_fm' )
);

$ViewList['viewcollected'] = array(
		'params' => array('collected_id'),
		'functions' => array(  'view_fm' )
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
		'functions' => array(  'view_fm' )
);

/*
 * zip file with XLS file and documents
 *  
 * */
$ViewList['downloaditem'] = array(
		'params' => array('collected_id'),
		'functions' => array('view_fm')
);

/*
 * single attribute download
 * 
 * */
$ViewList['download'] = array(
		'params' => array('collected_id','attr_name'),
		'functions' => array('view_fm')
);

$ViewList['embed'] = array(	
		'params' => array('form_id')
);

$ViewList['formwidget'] = array(
		'params' => array('form_id')
);

$FunctionList = array();
$FunctionList['view_fm'] = array('explain'   => 'Allow user to view forms and collected records');
$FunctionList['manage_fm'] = array('explain'   => 'Allow user to create and edit forms');
$FunctionList['delete_fm'] = array('explain'   => 'Allow user to delete forms');
$FunctionList['delete_collected'] = array('explain'   => 'Allow user to delete collected item');
$FunctionList['generate_js'] = array('explain' => 'Allow user to generate page embed js');
$FunctionList['fill_private'] = array('explain' => 'Allow user fill a private form');
