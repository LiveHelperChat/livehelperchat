<?php
/**
 * File containing the ezcLogWriter interface.
 *
 * @package EventLog
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcLogWriter defines the common interface for all classes that implement
 * their log writer.
 *
 * See the ezcLogFileWriter for an example of creating your own log writer.
 *
 * @package EventLog
 * @version 1.4
 */
interface ezcLogWriter
{
    /**
     * Writes the message $message to the log.
     *
     * The writer can use the severity, source, and category to filter the
     * incoming messages and determine the location where the messages should
     * be written.
     *
     * The array $optional contains extra information that can be added to the log. For example:
     * line numbers, file names, usernames, etc.
     *
     * @throws ezcLogWriterException
     *         If the log writer was unable to write the log message
     *
     * @param string $message
     * @param int $severity
     *        ezcLog::DEBUG, ezcLog::SUCCESS_AUDIT, ezcLog::FAILED_AUDIT, ezcLog::INFO, ezcLog::NOTICE,
     *        ezcLog::WARNING, ezcLog::ERROR or ezcLog::FATAL.
     * @param string $source
     * @param string $category
     * @param array(string=>string) $optional
     */
    public function writeLogMessage( $message, $severity, $source, $category, $optional = array() );
}
?>
