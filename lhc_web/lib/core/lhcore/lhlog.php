<?php

class erLhcoreClassLog {
           
    static function write($msg)
    {
         // Get the one and only instance of the ezcLog.
         $log = ezcLog::getInstance();
         // Get an instance to the default log mapper.
         $mapper = $log->getMapper();
         // Create a new Unix file writer, that writes to the file: "default.log".
         $writer = new ezcLogUnixFileWriter( "cache", "default.log" );
         // Create a filter that accepts every message (default behavior).
         $filter = new ezcLogFilter;
         // Combine the filter with the writer in a filter rule.
         $rule = new ezcLogFilterRule( $filter, $writer, true );
         // And finally assign the rule to the mapper.
         $mapper->appendRule( $rule );
         // Write a message to the log
         $log->log( $msg, ezcLog::WARNING ); 
    }
    
  

}


?>