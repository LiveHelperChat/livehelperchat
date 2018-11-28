<?php

$Module = array( "name" => "Generic Bot",
				 'variable_params' => true );

$ViewList = array();

$ViewList['bot'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['initbot'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['addpayload'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['downloadbot'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['import'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['triggersbybot'] = array(
    'params' => array('id','trigger_id'),
    'uparams' => array('preview','element'),
    'functions' => array( 'use' )
);

$ViewList['getpayloads'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['argumenttemplates'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['nodegroups'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['list'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['new'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['edit'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['delete'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use' )
);

$ViewList['nodegrouptriggers'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['addgroup'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['updategroup'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['updatetrigger'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['savetrigger'] = array(
    'params' => array(),
    'uparams' => array('method'),
    'functions' => array( 'use' )
);

$ViewList['addtrigger'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['addtriggerevent'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['deletetriggerevent'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['deletegroup'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['updatetriggerevent'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['removetrigger'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['setdefaulttrigger'] = array(
    'params' => array('id','default'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['setdefaultunknowntrigger'] = array(
    'params' => array('id','default'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['nodetriggeractions'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['buttonclicked'] = array(
    'params' => array('chat_id', 'hash'),
    'uparams' => array('type')
);

$ViewList['updatebuttonclicked'] = array(
    'params' => array('chat_id', 'hash'),
    'uparams' => array()
);

$FunctionList['use'] = array('explain' => 'General permission to use generic bot module');

?>