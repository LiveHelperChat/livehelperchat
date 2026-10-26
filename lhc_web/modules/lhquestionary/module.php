<?php

$Module = array( "name" => "Questionary/Voting");

$ViewList = array();

$ViewList['newquestion'] = array(
		'params' => array(),
		'functions' => array( 'manage_questionary' )
);

$ViewList['list'] = array(
		'params' => array(),
		'functions' => array( 'manage_questionary' )
);

$ViewList['delete'] = array(
		'params' => array('id'),
		'uparams' => array('csfr'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['deleteanswer'] = array(
		'params' => array('id'),
		'uparams' => array('csfr'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['deleteoption'] = array(
		'params' => array('id'),
		'uparams' => array('csfr'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['htmlcode'] = array(
		'params' => array(),
		'functions' => array( 'manage_questionary' )
);

$ViewList['getstatus'] = array(
		'params' => array(),
		'uparams' => array('theme','noresponse','position','expand','top','units','width','height')
);

$ViewList['votingwidget'] = array(
		'params' => array(),
		'uparams' => array('theme','mode'),
);

$ViewList['votingwidgetclosed'] = array(
		'params' => array(),
);

$ViewList['previewanswer'] = array(
		'params' => array('id'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['edit'] = array(
		'params' => array('id',),
		'uparams' => array('tab','option_id'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['embed'] = array(
		'params' => array(),
		'uparams' => array('theme'),
);

$ViewList['embedcode'] = array(
		'script' => 'embedcode.php',
		'params' => array(),
		'functions' => array('manage_questionary')
);

$FunctionList['manage_questionary'] = array('explain' => 'Allow user to manage questionary');

?>