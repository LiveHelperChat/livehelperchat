<?php

//exit;
//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

$star_microtile = microtime();
function set_time ( $start_time, $end_time )
{
	$start = explode(' ', $start_time);
	$end = explode(' ', $end_time);
	return  $time = $end[0] + $end[1] - $start[0] - $start[1];
}

/* DEBUG END */
ini_set('error_reporting', 1);
ini_set('display_errors', 1);


require_once "ezcomponents/Base/src/base.php"; // dependent on installation method, see below

function __autoload( $className )
{
        ezcBase::autoload( $className );
}

ezcBase::addClassRepository( './','./lib/autoloads'); 
erLhcoreClassSystem::init();

// your code here
ezcBaseInit::setCallback(
 'ezcInitDatabaseInstance',
 'erLhcoreClassLazyDatabaseConfiguration'
);

$Result = erLhcoreClassModule::moduleInit();

$tpl = erLhcoreClassTemplate::getInstance('pagelayouts/main.php');
$tpl->set('Result',$Result);
if (isset($Result['pagelayout']))
{
	$tpl->setFile('pagelayouts/'.$Result['pagelayout'].'.php');
}

echo $tpl->fetch();