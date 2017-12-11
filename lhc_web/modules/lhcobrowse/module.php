<?php

$Module = array( "name" => "CO Browse module");

$ViewList = array();

$ViewList['browse'] = array(
		'params' => array('chat_id'),
		'functions' => array('browse'),
		'uparams' => array()
);

$ViewList['mirror'] = array(
		'params' => array(),
		'functions' => array('browse'),
		'uparams' => array()
);

$ViewList['checkmirrorchanges'] = array(
		'params' => array('chat_id'),
		'functions' => array('browse'),
		'uparams' => array('cobrowsemode')
);

$ViewList['checkinitializer'] = array(
		'params' => array('chat_id'),
		'functions' => array('browse'),
		'uparams' => array()
);

$ViewList['storenodemap'] = array(
		'params' => array(),
		'uparams' => array('vid','hash','hash_resume','sharemode')
);

$ViewList['finishsession'] = array(
		'params' => array(),
		'uparams' => array('vid','hash','hash_resume','sharemode')
);

$ViewList['proxycss'] = array(
		'params' => array('chat_id'),
        'functions' => array('browse'),
		'uparams' => array('cobrowsemode')
);

$FunctionList = array();
$FunctionList['browse'] = array('explain' => 'Allow operator to use co-browse functionality');

?>
