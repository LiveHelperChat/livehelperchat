<?php

$Module = array( "name" => "Live helper chat faq module");

$ViewList = array();

$ViewList['list'] = array(
    'script' => 'list.php',
    'params' => array(),
	'functions' => array( 'manage_faq' )
);

$ViewList['delete'] = array(
		'script' => 'delete.php',
		'params' => array('id'),
		'functions' => array( 'manage_faq' )
);

$ViewList['view'] = array(
		'script' => 'view.php',
		'params' => array('id'),
		'functions' => array( 'manage_faq' )
);

$ViewList['new'] = array(
		'script' => 'new.php',
		'params' => array('id'),
		'functions' => array( 'manage_faq' )
);

$ViewList['faqwidget'] = array(
		'script' => 'faqwidget.php',
		'params' => array(),
);

$ViewList['getstatus'] = array(
		'script' => 'getstatus.php',
		'params' => array(),
		'functions' => array( ),
		'uparams' => array('position','top','units')
);

$ViewList['htmlcode'] = array(
		'script' => 'htmlcode.php',
		'params' => array(),
		'functions' => array( 'manage_faq' )
);

$FunctionList = array();
$FunctionList['manage_faq'] = array('explain' => 'Allow user to manage FAQ');

?>