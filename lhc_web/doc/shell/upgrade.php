<?php

@ini_set('error_reporting', E_ALL);
@ini_set('display_errors', 1);
@ini_set('session.gc_probability', 1);
@ini_set('session.gc_divisor', 100);
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
if ($is_secure_conn != null || $is_secure_conn != 'off') {
    @ini_set('session.cookie_secure',1);
}

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

$jsonObject = json_decode(erLhcoreClassModelChatOnlineUser::executeRequest('https://raw.githubusercontent.com/LiveHelperChat/livehelperchat/master/lhc_web/doc/update_db/structure.json'),true);

if (is_array($jsonObject)){
	$errorMessages = erLhcoreClassUpdate::doTablesUpdate($jsonObject);
	if (empty($errorMessages)) {
		$CacheManager = erConfigClassLhCacheConfig::getInstance();
		$CacheManager->expireCache();
		echo "UPDATE DONE\n";
	} else {
		echo "ERROR:\n".implode("\n", $errorMessages);
	}
}