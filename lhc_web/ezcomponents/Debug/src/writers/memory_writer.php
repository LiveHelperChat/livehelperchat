<?php

/**
 * File containing the ezcDebugWriterMemory class.
 *
 * @package Debug
 * @version 1.2.1
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * This class implements a LogWriter. This writer will write all the log messages
 * it receives to an internal structure. The whole internal structure can be sent
 * to an formatter when the getLogEntries() method is called.
 *
 * @package Debug
 * @version 1.2.1
 * @access private
 */
class ezcDebugMemoryWriter implements ezcLogWriter
{
    /**
     * Internal structure to hold the log messages.
     *
     * @var array(ezcDebugStructure)
     */
    private $logData = array();

    /**
     * Resets the writer to its initial state.
     *
     * @return void
     */
    public function reset()
    {
        $this->logData = array();
    }

    /**
     * Writes a log entry to the internal memory structure.
     *
     * The writer can use the severity, source, and category to filter the
     * incoming messages and determine the location where the messages should
     * be written.
     *
     * $extraInfo may contain extra information that can be added to the log. For example:
     * line numbers, file names, usernames, etc.
     *
     * $severity can be one of:
     * <ul>
     * <li>{@link ezcDebug::DEBUG}</li>
     * <li>{@link ezcDebug::SUCCESS_AUDIT}</li>
     * <li>{@link ezcDebug::FAILED_AUDIT}</li>
     * <li>{@link ezcDebug::INFO}</li>
     * <li>{@link ezcDebug::NOTICE}</li>
     * <li>{@link ezcDebug::WARNING}</li>
     * <li>{@link ezcDebug::ERROR}</li>
     * <li>{@link ezcDebug::FATAL}</li>
     * </ul>.
     *
     * @param string $message
     * @param int $severity  ezcLog::* 
     * @param string $source
     * @param string $category
     * @param array(string=>string) $extraInfo
     */
    public function writeLogMessage( $message, $severity, $source, $category, $extraInfo = array() )
    {
        $log = new ezcDebugStructure();
        $log->message = $message;
        $log->severity = $severity;
        $log->source = $source;
        $log->category = $category;
        $log->datetime = time();

        if ( is_array( $extraInfo ) )
        {
            foreach ( $extraInfo as $key => $val )
            {
                $log->$key = $val;
            }
        }

        $this->logData[] = $log;
    }

    /**
     * Returns the log messages stored in memory.
     *
     * @return array(ezcDebugStructure)
     */
    public function getStructure()
    {
        return $this->logData;
    }
}
?>
