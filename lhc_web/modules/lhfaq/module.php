<?php

$Module = array( "name" => "Live helper chat faq module");

$ViewList = array();

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

$ViewList['view'] = array(
		'script' => 'view.php',
		'params' => array('id'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['new'] = array(
		'script' => 'new.php',
		'params' => array('id'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['faqwidget'] = array(
		'script' => 'faqwidget.php',
		'params' => array(),

);

$FunctionList = array();
$FunctionList['manage_faq'] = array('explain' => 'Allow user to manage FAQ');

?>