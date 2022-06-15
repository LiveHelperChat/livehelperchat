<?php

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