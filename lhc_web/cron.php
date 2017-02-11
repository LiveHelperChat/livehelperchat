<?php
/**
 * Copyright 2009-2015 Remigijus Kiminas
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

ini_set('error_reporting', E_ALL);
ini_set('register_globals', 0);
ini_set('display_errors', 1);

ini_set("max_execution_time", "3600");

require_once dirname(__FILE__)."/ezcomponents/Base/src/base.php";

ezcBase::addClassRepository( dirname(__FILE__).'/', dirname(__FILE__).'/lib/autoloads');

spl_autoload_register(array('ezcBase','autoload'), true, false);

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

$cronjobPathOption = $input->registerOption(
    new ezcConsoleOption(
        'p',
        'path',
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

erLhcoreClassModule::$defaultTimeZone = $cfgSite->getSetting('site', 'time_zone', false);
erLhcoreClassModule::$dateFormat = $cfgSite->getSetting('site', 'date_format', false);
erLhcoreClassModule::$dateHourFormat = $cfgSite->getSetting('site', 'date_hour_format', false);
erLhcoreClassModule::$dateDateHourFormat = $cfgSite->getSetting('site', 'date_date_hour_format', false);

// Attatch extensions events listeners
erLhcoreClassModule::attatchExtensionListeners();

// php cron.php -s site_admin -c cron/workflow
// php cron.php -s site_admin -e customstatus -c cron/customcron
if ($extensionPartOption->value) {
	include_once('extension/'.$extensionPartOption->value.'/modules/lh'.$cronjobPartOption->value.'.php');
} else {
	include_once('modules/lh'.$cronjobPartOption->value.'.php');
}

?>