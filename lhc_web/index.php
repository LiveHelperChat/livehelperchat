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

@ini_set('error_reporting', 0);
@ini_set('display_errors', 0);
@ini_set('session.gc_maxlifetime', 200000);
@ini_set('session.cookie_lifetime', 2000000);
@ini_set('session.cookie_httponly',1);
@ini_set('session.cookie_samesite', 'Lax');

// Uncomment these if you are using chrome extension
// Min PHP 7.3v is required
// @ini_set('session.cookie_samesite', 'None');
// @ini_set('session.cookie_secure', true);

// Uncomment if you are using composer dependencies
require_once 'lib/vendor/autoload.php';

require_once "ezcomponents/Base/src/base.php"; // dependent on installation method, see below

ezcBase::addClassRepository( './','./lib/autoloads');

spl_autoload_register(array('ezcBase','autoload'), true, false);
spl_autoload_register(array('erLhcoreClassSystem','autoload'), true, false);

erLhcoreClassSystem::init();

// your code here
ezcBaseInit::setCallback(
    'ezcInitDatabaseInstance',
    'erLhcoreClassLazyDatabaseConfiguration'
);

set_exception_handler( array('erLhcoreClassModule', 'defaultExceptionHandler') );
set_error_handler (  array('erLhcoreClassModule', 'defaultWarningHandler') );

try {

    $Result = erLhcoreClassModule::moduleInit();

    $tpl = erLhcoreClassTemplate::getInstance('pagelayouts/main.php');
    $tpl->set('Result',$Result);
    if (isset($Result['pagelayout']))
    {
        $tpl->setFile('pagelayouts/'.$Result['pagelayout'].'.php');
    }

    echo $tpl->fetch();

} catch (Exception $e) {

    if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
        echo "<pre>";
        print_r($e);
        echo "</pre>";
        exit;
    }

    error_log($e);

    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-After: 300');

    include_once('design/defaulttheme/tpl/lhkernel/fatal_error.tpl.php');

    erLhcoreClassLog::write(print_r($e,true));

    erLhcoreClassModule::logException($e);
}


flush();
session_write_close();

if ( function_exists('fastcgi_finish_request') ) {
    fastcgi_finish_request();
};

erLhcoreClassChatEventDispatcher::getInstance()->executeFinishRequest();