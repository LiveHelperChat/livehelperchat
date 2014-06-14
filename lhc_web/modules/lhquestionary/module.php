<?php

$Module = array( "name" => "Questionary/Voting");

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
		'uparams' => array('csfr'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['deleteanswer'] = array(
		'script' => 'deleteanswer.php',
		'params' => array('id'),
		'uparams' => array('csfr'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['deleteoption'] = array(
		'script' => 'deleteoption.php',
		'params' => array('id'),
		'uparams' => array('csfr'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['htmlcode'] = array(
		'script' => 'htmlcode.php',
		'params' => array(),
		'functions' => array( 'manage_questionary' )
);

$ViewList['getstatus'] = array(
		'script' => 'getstatus.php',
		'params' => array(),
		'functions' => array( ),
		'uparams' => array('theme','noresponse','position','expand','top','units','width','height')
);

$ViewList['votingwidget'] = array(
		'script' => 'votingwidget.php',
		'params' => array(),
		'uparams' => array('theme','mode'),
		'functions' => array()
);

$ViewList['votingwidgetclosed'] = array(
		'script' => 'votingwidgetclosed.php',
		'params' => array(),
		'functions' => array( )
);

$ViewList['previewanswer'] = array(
		'script' => 'previewanswer.php',
		'params' => array('id'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['edit'] = array(
		'script' => 'edit.php',
		'params' => array('id',),
		'uparams' => array('tab','option_id'),
		'functions' => array( 'manage_questionary' )
);

$ViewList['embed'] = array(
		'script' => 'embed.php',
		'params' => array(),
		'uparams' => array('theme'),
		'functions' => array()
);

$ViewList['embedcode'] = array(
		'script' => 'embedcode.php',
		'params' => array(),
		'functions' => array('manage_questionary')
);

$FunctionList['manage_questionary'] = array('explain' => 'Allow user to manage questionary');

?>