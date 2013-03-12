<?php

@ini_set('error_reporting', 0);
@ini_set('display_errors', 0);
@ini_set('session.gc_probability', 1);
@ini_set('session.gc_divisor', 100);
@ini_set('session.gc_maxlifetime', 200000);
@ini_set('session.cookie_lifetime', 2000000);

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