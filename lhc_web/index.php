<?php

require_once "ezcomponents/Base/src/base.php"; // dependent on installation method, see below

function __autoload( $className )
{
        ezcBase::autoload( $className );
}

ezcBase::addClassRepository( './lib','./lib/autoloads'); 

// your code here
ezcBaseInit::setCallback(
 'ezcInitDatabaseInstance',
 'erDbClassLazyDatabaseConfiguration'
);
  
erLhcoreClassSystem::init();


$url = erLhcoreClassURL::getInstance();

if (!is_null($url->getParam( 'module' )) && file_exists('modules/lh'.$url->getParam( 'module' ).'/module.php')){
    $ModuleToRun = $url->getParam( 'module' );
    $ViewToRun = $url->getParam( 'function' );
	include_once('modules/lh'.$url->getParam( 'module' ).'/module.php');	
} else {	
	/*First page search results*/
	$ModuleToRun = 'front';
	$ViewToRun = 'default';
	include_once('modules/lhfront/module.php');	
}
      
$Result = erLhcoreClassModule::runModule($ViewList,$FunctionList);

$cfg = erConfigClassLhConfig::getInstance();

ob_start();
if (isset($Result['pagelayout']))
{
	include_once('design/'. $cfg->conf->getSetting( 'site', 'theme' ) .'/tpl/pagelayouts/'.$Result['pagelayout'].'.php');
}
else	
	include_once('design/'. $cfg->conf->getSetting( 'site', 'theme' ) .'/tpl/pagelayouts/main.php');	
ob_end_flush();


?>