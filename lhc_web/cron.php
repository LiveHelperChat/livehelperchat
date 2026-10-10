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

// This is a command line only entry point. Without this guard the options would
// be parsed from the query string when register_argc_argv is enabled, allowing an
// anonymous HTTP request to include arbitrary files.
if (php_sapi_name() != 'cli') {
    echo "PHP code to execute directly on the command line\n";
    exit(-1);
}

ini_set('error_reporting', E_ALL);
ini_set('register_globals', 0);
ini_set('display_errors', 1);

ini_set("max_execution_time", "3600");

// Uncomment if you are using composer dependencies
require_once 'lib/vendor/autoload.php';
require_once dirname(__FILE__)."/ezcomponents/Base/src/base.php";

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

function defaultCronjobFatalHandler($errno, $errstr, $errfile, $errline) {

    $msg = 'Unexpected error, the message was : ' . $errstr . ' in ' . $errfile . ' on line ' . $errline;

    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

    if ($errno == E_USER_ERROR || $errno == E_COMPILE_ERROR || $errno == E_PARSE || $errno == E_ERROR || $errno == E_RECOVERABLE_ERROR || $errno == E_WARNING) {
        error_log($msg);

        erLhcoreClassLog::write($msg);
        erLhcoreClassLog::write(
            json_encode([
                'msg' => $msg,
                'trace' => $trace
            ]),
            ezcLog::SUCCESS_AUDIT,
            array(
                'source' => 'lhc',
                'category' => 'cronjob_fatal',
                'line' => __LINE__,
                'file' => __FILE__,
                'object_id' => 0
            )
        );
    }

    return false;
}

function defaultCronjobExceptionHandler($e) {

    error_log($e);

    erLhcoreClassLog::write(json_encode([
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTrace(),
        'raw' => (string)$e,
    ],JSON_PRETTY_PRINT));

    erLhcoreClassLog::write(
        json_encode([
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
            'raw' => (string)$e,
        ],JSON_PRETTY_PRINT)
        ,
        ezcLog::SUCCESS_AUDIT,
        array(
            'source' => 'lhc',
            'category' => 'cronjob_exception',
            'line' => __LINE__,
            'file' => __FILE__,
            'object_id' => 0
        )
    );
}

set_exception_handler( 'defaultCronjobExceptionHandler' );
set_error_handler ( 'defaultCronjobFatalHandler' );

$instance = erLhcoreClassSystem::instance();
$instance->backgroundMode = true;
$instance->SiteAccess = $helpOption->value;
$instance->SiteDir = dirname(__FILE__).'/';
$cfgSite = erConfigClassLhConfig::getInstance();
$defaultSiteAccess = $cfgSite->getSetting( 'site', 'default_site_access' );
$optionsSiteAccess = $cfgSite->getSetting('site_access_options',$helpOption->value);
$instance->Language = $optionsSiteAccess['locale'];
$instance->ThemeSite = $optionsSiteAccess['theme'];
$instance->WWWDirLang = '/'.$helpOption->value;

$tz = isset($optionsSiteAccess['time_zone']) ? $optionsSiteAccess['time_zone'] : $cfgSite->getSetting( 'site', 'time_zone', false);

erLhcoreClassModule::$defaultTimeZone = $tz;
erLhcoreClassModule::$dateFormat = $cfgSite->getSetting('site', 'date_format', false);
erLhcoreClassModule::$dateHourFormat = $cfgSite->getSetting('site', 'date_hour_format', false);
erLhcoreClassModule::$dateDateHourFormat = $cfgSite->getSetting('site', 'date_date_hour_format', false);

if (erLhcoreClassModule::$defaultTimeZone != '') {
    date_default_timezone_set(erLhcoreClassModule::$defaultTimeZone);
}

// Attatch extensions events listeners
erLhcoreClassModule::attatchExtensionListeners();

// php cron.php -s site_admin -c cron/workflow
// php cron.php -s site_admin -e customstatus -c cron/customcron

// Validate job/extension names and resolve the target through realpath so the
// include can never escape the modules directories (e.g. via "../").
if (!preg_match('#^[a-zA-Z0-9_\\-]+(/[a-zA-Z0-9_\\-]+)*$#', (string)$cronjobPartOption->value)) {
    die("Invalid cronjob option value\n");
}

if ($extensionPartOption->value && !preg_match('#^[a-zA-Z0-9_\\-]+$#', (string)$extensionPartOption->value)) {
    die("Invalid extension option value\n");
}

if ($extensionPartOption->value) {
    $cronjobFile = dirname(__FILE__) . '/extension/' . $extensionPartOption->value . '/modules/lh' . $cronjobPartOption->value . '.php';
    $cronjobBaseDir = realpath(dirname(__FILE__) . '/extension/' . $extensionPartOption->value . '/modules');
} else {
    $cronjobFile = dirname(__FILE__) . '/modules/lh' . $cronjobPartOption->value . '.php';
    $cronjobBaseDir = realpath(dirname(__FILE__) . '/modules');
}

$cronjobRealFile = realpath($cronjobFile);

if ($cronjobBaseDir === false || $cronjobRealFile === false || strpos($cronjobRealFile, $cronjobBaseDir . DIRECTORY_SEPARATOR) !== 0) {
    die("Cronjob file not found or not allowed\n");
}

include_once($cronjobRealFile);

?>