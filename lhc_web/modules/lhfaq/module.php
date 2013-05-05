<?php

$Module = array( "name" => "FAQ");

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
		'uparams' => array('url','mode'),
);

$ViewList['getstatus'] = array(
		'script' => 'getstatus.php',
		'params' => array(),
		'functions' => array( ),
		'uparams' => array('position','top','units')
);

$ViewList['embed'] = array(
		'script' => 'embed.php',
		'params' => array(),
		'functions' => array()
);

$ViewList['embedcode'] = array(
		'script' => 'embedcode.php',
		'params' => array(),
		'functions' => array('manage_faq')
);

$ViewList['htmlcode'] = array(
		'script' => 'htmlcode.php',
		'params' => array(),
		'functions' => array( 'manage_faq' )
);

$FunctionList = array();
$FunctionList['manage_faq'] = array('explain' => 'Allow user to manage FAQ');

?>