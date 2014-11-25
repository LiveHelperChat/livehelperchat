<?php

@ini_set('error_reporting', E_ALL);
@ini_set('display_errors', 1);
@ini_set('session.gc_probability', 1);
@ini_set('session.gc_divisor', 100);
@ini_set('session.gc_maxlifetime', 200000);
@ini_set('session.cookie_lifetime', 2000000);
@ini_set('session.cookie_httponly',1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
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