<?php

class erLhcoreClassLog implements ezcBaseConfigurationInitializer {
	
	// to write to the log put a write statement in the code with the content to log
	//
	// Example: erLhcoreClassLog::write(print_r($_POST,true));
	// or: erLhcoreClassLog::write("Log entry");
	//
	// default log location is CACHE/DEFAULT.LOG since it is writable Change below.
	static function write($msg) {
		// Use log
		$log = ezcLog::getInstance ();
		$log->log ( $msg, ezcLog::WARNING );
	}
	
	public static function configureObject($log) {
		$writeAll = new ezcLogUnixFileWriter ( "cache", "default.log" );
		$log->getMapper ()->appendRule ( new ezcLogFilterRule ( new ezcLogFilter (), $writeAll, true ) );
	}
}

ezcBaseInit::setCallback ( 'ezcInitLog', 'erLhcoreClassLog' );

?>
