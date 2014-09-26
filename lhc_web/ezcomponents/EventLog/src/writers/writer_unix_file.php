<?php
/**
 * File containing the ezcLogUnixFileWriter class.
 *
 * @package EventLog
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Writes the log messages to a file in a format that is frequently used on the Unix operating system.
 *
 * @package EventLog
 * @version 1.4
 * @mainclass
 */
class ezcLogUnixFileWriter extends ezcLogFileWriter
{
    /**
     * Write the logEntries to a file.
     *
     * Each line in the log file represents a log message. The log
     * messages have the following style:
     * <pre>
     * MMM dd HH:mm:ss [Severity] [Source] [Category] Message (ExtraInfo)
     * </pre>
     *
     * With:
     * - MMM: The 3 letter abbreviation of the month.
     * - dd: The day of the month.
     * - HH: The hour.
     * - mm: The minutes.
     * - ss: The seconds.
     *
     * Example:
     * <pre>
     * Jan 24 15:32:56 [Debug] [Paynet] [Shop] Connecting to the paynet server (file: paynet_server.php, line: 224).
     * Jan 24 15:33:01 [Debug] [Paynet] [Shop] Connected with the server (file: paynet_server.php, line: 710).
     * </pre>
     *
     * This method will be called by the {@link ezcLog} class.  The $eventSource and $eventCategory are either given
     * in the {@link ezcLog::log()} method or are the defaults from the {@link ezcLog} class.
     *
     * @param string $message
     * @param int $eventType
     * @param string $eventSource
     * @param string $eventCategory
     * @param array(string=>string) $extraInfo
     */
    public function writeLogMessage( $message, $eventType, $eventSource, $eventCategory, $extraInfo = array() )
    {
        $extra = "";

        if ( sizeof( $extraInfo ) > 0 )
        {
            $extra =  " (" . $this->implodeWithKey( ", ", ": ", $extraInfo ) . ")";
        }

        if ( $eventCategory == false )
        {
            $eventCategory = "";
        }
        $logMsg = date( "M d H:i:s" ) .
            " [".ezcLog::translateSeverityName( $eventType ) .
            "] ".
            ( $eventSource == "" ? "" : "[$eventSource] ") .
            ( $eventCategory == "" ? "" : "[$eventCategory] " ).
            "{$message}{$extra}\n";

        $this->write( $eventType, $eventSource, $eventCategory, $logMsg );
    }

    /**
     * Returns a string from the hash $data.
     *
     * The string $splitEntry specifies the string that will be inserted between the pairs.
     * The string $splitKeyVal specifies the string that will be inserted in each pair.
     *
     * Example:
     * <code>
     * $this->implodeWithKey( ", ", ": ", array( "Car" => "red", "Curtains" => "blue" );
     * </code>
     *
     * Will create the following string:
     * <pre>
     * Car: red, Curtains: blue
     * </pre>
     *
     * @param string $splitEntry
     * @param string $splitKeyVal
     * @param array(mixed=>mixed) $data
     * @return string
     */
    protected function implodeWithKey( $splitEntry, $splitKeyVal, $data)
    {
        $total = "";
        if ( is_array( $data ) )
        {
            foreach ( $data as $key => $val )
            {
                $total .=  $splitEntry . $key . $splitKeyVal . $val;
            }
        }

        return substr( $total, strlen( $splitEntry ) );
    }
}
?>
