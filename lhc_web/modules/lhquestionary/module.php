<?php

$Module = array( "name" => "Questionary module");

$ViewList = array();

$ViewList['newquestion'] = array(
		'script' => 'newquestion.php',
		'params' => array(),
		'functions' => array( 'manage_questionary' )
);

$ViewList['list'] = array(
		'script' => 'list.php',
		'params' => array(),
		'functions' => array( 'manage_questionary' )
);

$ViewList['delete'] = array(
		'script' => 'delete.php',
		'params' => array('id'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['deleteanswer'] = array(
		'script' => 'deleteanswer.php',
		'params' => array('id'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['previewanswer'] = array(
		'script' => 'previewanswer.php',
		'params' => array('id'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['edit'] = array(
		'script' => 'edit.php',
		'params' => array('id'),
		'uparams' => array('tab'),
		'functions' => array( 'manage_questionary' )
);

$FunctionList['manage_questionary'] = array('explain' => 'Allow user to manage questionary');

?>