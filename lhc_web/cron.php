<?php

ini_set('error_reporting', E_ALL);
ini_set('register_globals', 0);
ini_set('display_errors', 1);

ini_set("max_execution_time", "3600");

require_once dirname(__FILE__)."/ezcomponents/Base/src/base.php";

function __autoload( $className )
{
        ezcBase::autoload( $className );
}

ezcBase::addClassRepository( dirname(__FILE__).'/', dirname(__FILE__).'/lib/autoloads');

$input = new ezcConsoleInput();

$helpOption = $input->registerOption(
new ezcConsoleOption(
    's',
    'siteaccess',
    ezcConsoleInput::TYPE_STRING
)
);

$cronjobPartOption = $input->registerOption(
new ezcConsoleOption(
    'c',
    'cronjob',
    ezcConsoleInput::TYPE_STRING
)
);

$extensionPartOption = $input->registerOption(
new ezcConsoleOption(
    'e',
    'extension',
    ezcConsoleInput::TYPE_STRING
)
);

try
{
    $input->process();
}
catch ( ezcConsoleOptionException $e )
{
    die( $e->getMessage() );
}

ezcBaseInit::setCallback(
 'ezcInitDatabaseInstance',
 'erLhcoreClassLazyDatabaseConfiguration'
);

$instance = erLhcoreClassSystem::instance();
$instance->SiteAccess = $helpOption->value;
$instance->SiteDir = dirname(__FILE__).'/';
$cfgSite = erConfigClassLhConfig::getInstance();
$defaultSiteAccess = $cfgSite->getSetting( 'site', 'default_site_access' );
$optionsSiteAccess = $cfgSite->getSetting('site_access_options',$helpOption->value);
$instance->Language = $optionsSiteAccess['locale'];
$instance->ThemeSite = $optionsSiteAccess['theme'];
$instance->WWWDirLang = '/'.$helpOption->value;

// php cron.php -s site_admin -c cron/workflow
// php cron.php -s site_admin -e customstatus -c cron/customcron
if ($extensionPartOption->value) {
	include_once('extension/'.$extensionPartOption->value.'/modules/lh'.$cronjobPartOption->value.'.php');
} else {
	include_once('modules/lh'.$cronjobPartOption->value.'.php');
}

?>