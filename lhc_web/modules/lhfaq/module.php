<?php

$Module = array( "name" => "FAQ");

$ViewList = array();

$ViewList['list'] = array(
    'params' => array(),
	'functions' => array( 'manage_faq' )
);

$ViewList['delete'] = array(
		'params' => array('id'),
		'uparams' => array('csfr'),
		'functions' => array( 'manage_faq' )
);

$ViewList['view'] = array(
		'params' => array('id'),
		'functions' => array( 'manage_faq' )
);

$ViewList['new'] = array(
		'params' => array('id'),
		'functions' => array( 'manage_faq' )
);

$ViewList['faqwidget'] = array(
		'params' => array(),
		'uparams' => array('theme','url','mode','identifier'),
);

$ViewList['getstatus'] = array(
		'params' => array(),
		'functions' => array( ),
		'uparams' => array('theme','noresponse','position','top','units')
);

$ViewList['embed'] = array(
		'params' => array(),
		'uparams' => array('theme'),
		'functions' => array()
);

$ViewList['embedcode'] = array(
		'params' => array(),
		'functions' => array('manage_faq')
);

$ViewList['htmlcode'] = array(
		'params' => array(),
		'functions' => array( 'manage_faq' )
);

$FunctionList = array();
$FunctionList['manage_faq'] = array('explain' => 'Allow user to manage FAQ');

?>