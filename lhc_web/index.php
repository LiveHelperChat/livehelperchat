<?php

@ini_set('error_reporting', 0);
@ini_set('display_errors', 0);
@ini_set('session.gc_maxlifetime', 200000);
@ini_set('session.cookie_lifetime', 2000000);
@ini_set('session.cookie_httponly',1);
/*
 * Vulnerability: SC-1628
 * Name: SSL cookie without secure flag set
 * Type: Web Servers
 * Asset Group: Network Segment
 * 
 * URI: /index.php/chat/startchat
 * Other Info: PHPSESSID=4fqbt1u2k5ci475ieiku4aaie0; path=/; HttpOnly
 * 
 * Source: SureCloud 
 */
// https://bugs.php.net/bug.php?id=49184
// https://bugs.debian.org/cgi-bin/bugreport.cgi?bug=730094
if (filter_has_var(INPUT_SERVER, "HTTPS")) {
        $is_secure_conn = filter_input(INPUT_SERVER, "HTTPS",
FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE);
    } else {
        if (isset($_SERVER["HTTPS"]))
            $is_secure_conn = filter_var($_SERVER["HTTPS"],
FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE);
        else
            $is_secure_conn = null;
    }
if ($is_secure_conn != null) {
    @ini_set('session.cookie_secure',1);
}

require_once "ezcomponents/Base/src/base.php"; // dependent on installation method, see below

ezcBase::addClassRepository( './','./lib/autoloads');

spl_autoload_register(array('ezcBase','autoload'), true, false);

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